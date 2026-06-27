<?php

namespace App\Notifications;

use App\Models\ConsignmentRestockRequest;
use App\Models\WholesaleRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RestockRequestNotification extends Notification
{
    use Queueable;

    public function __construct(
        public ConsignmentRestockRequest $restock,
        public WholesaleRequest $wholesaler
    ) {}

    public function via($notifiable): array { return ['mail']; }

    public function toMail($notifiable): MailMessage
    {
        $lineas = collect($this->restock->items)->map(fn($i) =>
            '• ' . ($i['product_name'] ?? '?') .
            ' ×' . ($i['quantity'] ?? 0) .
            '  (' . ($i['category'] ?? '') . ')'
        )->implode("\n");

        $adminUrl = url('/a33mgr/restock-requests');

        return (new MailMessage)
            ->subject("📦 Pedido de reposición — {$this->wholesaler->business_name}")
            ->greeting("¡Nuevo pedido de reposición!")
            ->line("**{$this->wholesaler->business_name}** ({$this->wholesaler->city}) solicitó los siguientes productos:")
            ->line("---")
            ->line($lineas)
            ->line("---")
            ->action('Ver en admin', $adminUrl)
            ->line("Contacto: {$this->wholesaler->email}" . ($this->wholesaler->phone ? " · {$this->wholesaler->phone}" : ''))
            ->salutation("AURA33 · Sistema de consignaciones");
    }
}
