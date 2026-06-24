<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via($notifiable): array { return ['mail']; }

    public function toMail($notifiable): MailMessage
    {
        $order = $this->order;
        $items = $order->items->map(fn($i) =>
            "• {$i->product_name} x{$i->quantity}  →  $" . number_format($i->subtotal, 0, ',', '.')
        )->implode("\n");

        $pago = $order->payment_method === 'mercadopago' ? 'MercadoPago' : 'Transferencia bancaria';

        return (new MailMessage)
            ->subject("🛍 Nuevo pedido {$order->order_number} — AURA33")
            ->greeting('¡Nuevo pedido recibido!')
            ->line("**Pedido:** {$order->order_number}")
            ->line("**Cliente:** {$order->customer_name}")
            ->line("**Email:** {$order->customer_email}")
            ->line("**Teléfono:** {$order->customer_phone}")
            ->line("**Envío:** {$order->shipping_address}, {$order->shipping_city}, {$order->shipping_province}")
            ->line("---")
            ->line("**Productos:**")
            ->line($items)
            ->line("---")
            ->line("**Subtotal:** $" . number_format($order->subtotal, 0, ',', '.'))
            ->line("**Envío:** $" . number_format($order->shipping_cost, 0, ',', '.'))
            ->line("**TOTAL: $" . number_format($order->total, 0, ',', '.') . "**")
            ->line("**Pago:** {$pago}")
            ->action('Ver pedido en el admin', url("/a33mgr/orders/{$order->id}/edit"));
    }
}
