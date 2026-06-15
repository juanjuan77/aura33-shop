<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReceiptUploadedNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via($notifiable): array { return ['mail']; }

    public function toMail($notifiable): MailMessage
    {
        $order = $this->order;

        return (new MailMessage)
            ->subject("📎 Comprobante recibido — {$order->order_number} — AURA33")
            ->greeting('¡Llegó un comprobante de pago!')
            ->line("**Pedido:** {$order->order_number}")
            ->line("**Cliente:** {$order->customer_name} ({$order->customer_email})")
            ->line("**Total:** $" . number_format($order->total, 0, ',', '.'))
            ->line("El cliente subió el comprobante de transferencia. Verificalo y confirmá el pedido.")
            ->action('Ver pedido y comprobante', url("/admin/orders/{$order->id}/edit"));
    }
}
