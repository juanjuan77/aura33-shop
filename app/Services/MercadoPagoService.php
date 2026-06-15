<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;

class MercadoPagoService
{
    private string $accessToken;
    private string $baseUrl = 'https://api.mercadopago.com';

    public function __construct()
    {
        $this->accessToken = config('services.mercadopago.access_token');
    }

    public function createPreference(Order $order): array
    {
        $response = Http::withToken($this->accessToken)
            ->post("{$this->baseUrl}/checkout/preferences", [
                'items' => $order->items->map(fn($item) => [
                    'title'       => $item->product_name,
                    'quantity'    => (int) $item->quantity,
                    'unit_price'  => (float) $item->unit_price,
                    'currency_id' => 'ARS',
                ])->toArray(),
                'payer' => [
                    'name'  => $order->customer_name,
                    'email' => $order->customer_email,
                    'phone' => ['number' => $order->customer_phone ?? ''],
                ],
                'shipments' => [
                    'cost' => (float) $order->shipping_cost,
                    'mode' => 'not_specified',
                ],
                'back_urls' => [
                    'success' => route('mp.success'),
                    'failure' => route('mp.failure'),
                    'pending' => route('mp.pending'),
                ],
                'auto_return'      => 'approved',
                'external_reference' => $order->order_number,
                'notification_url' => route('mp.webhook'),
            ]);

        if ($response->failed()) {
            throw new \Exception('Error al crear preferencia MP: ' . $response->body());
        }

        return $response->json();
    }

    public function getPayment(string $paymentId): array
    {
        $response = Http::withToken($this->accessToken)
            ->get("{$this->baseUrl}/v1/payments/{$paymentId}");

        return $response->json();
    }
}
