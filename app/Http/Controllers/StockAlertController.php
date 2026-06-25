<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class StockAlertController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        StockAlert::firstOrCreate([
            'product_id' => $product->id,
            'email'      => strtolower(trim($request->email)),
        ]);

        // Notificar al admin
        $adminEmail = config('app.admin_email');
        if ($adminEmail) {
            try {
                Mail::raw(
                    "Nuevo aviso de stock solicitado:\n\nProducto: {$product->name}\nEmail: {$request->email}",
                    fn ($m) => $m
                        ->to($adminEmail)
                        ->subject("⚡ Aviso de stock: {$product->name} — AURA33")
                );
            } catch (\Exception) {}
        }

        return back()->with('stock_alert_ok', '¡Listo! Te avisamos cuando vuelva a estar disponible 🔮');
    }
}
