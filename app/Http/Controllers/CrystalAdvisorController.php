<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CrystalAdvisorController extends Controller
{
    public function recommend(Request $request)
    {
        $request->validate(['message' => 'required|string|max:1000']);

        $products = Product::where('active', true)
            ->where('stock', '>', 0)
            ->with('category')
            ->get();

        // Catálogo compacto para no consumir tokens innecesarios
        $lines = $products->map(function ($p) {
            $props = $p->properties ?? [];
            $beneficios = implode(', ', array_slice($props['beneficios'] ?? [], 0, 3));
            $chakra = $props['chakra'] ?? '';
            return "{$p->slug}|{$p->name}|chakra:{$chakra}|{$beneficios}";
        })->join("\n");

        $prompt = <<<PROMPT
Sos asesora holística de cristales. Tono cálido y empático.
Cliente dijo: "{$request->message}"

Cristales disponibles (slug|nombre|chakra|beneficios):
{$lines}

Elegí el MÁS adecuado. Respondé ÚNICAMENTE con este JSON (sin markdown, sin texto extra):
{"slug":"SLUG","nombre":"NOMBRE","razon":"Dos oraciones cálidas explicando por qué este cristal es ideal para esta persona.","frase":"Frase inspiradora corta."}
PROMPT;

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post('https://generativelanguage.googleapis.com/v1beta/models/' . env('GEMINI_MODEL', 'gemini-2.5-flash') . ':generateContent?key=' . env('GEMINI_API_KEY'), [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ],
                'generationConfig' => [
                    'temperature'     => 0.7,
                    'maxOutputTokens' => 2048,
                ],
            ]);

            if (! $response->successful()) {
                return response()->json(['error' => 'No pudimos conectar con el oráculo. Intentá de nuevo.'], 500);
            }

            $text = $response->json('candidates.0.content.parts.0.text', '');
            // Extraer el JSON aunque Gemini agregue texto alrededor
            $text = preg_replace('/```json|```/', '', $text);
            if (preg_match('/\{.*\}/s', $text, $matches)) {
                $text = $matches[0];
            }
            $data = json_decode(trim($text), true);

            if (! $data || ! isset($data['slug'])) {
                return response()->json(['error' => 'El oráculo no pudo interpretar tu energía. Intentá con otras palabras.'], 500);
            }

            // Buscar el producto recomendado
            $product = $products->firstWhere('slug', $data['slug']);
            if (! $product) {
                $product = $products->first();
            }

            return response()->json([
                'product' => [
                    'nombre'    => $product->name,
                    'slug'      => $product->slug,
                    'imagen'    => $product->image_url,
                    'precio'    => number_format($product->price_retail, 0, ',', '.'),
                    'categoria' => $product->category->name ?? '',
                    'url'       => route('product', $product->slug),
                ],
                'razon' => $data['razon'],
                'frase' => $data['frase'],
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al consultar el oráculo. Intentá más tarde.'], 500);
        }
    }
}
