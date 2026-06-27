<?php

namespace App\Notifications;

use App\Models\Consignment;
use App\Models\WholesaleRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewConsignmentDeliveryNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Consignment $consignment,
        public WholesaleRequest $wholesaler
    ) {}

    public function via($notifiable): array { return ['mail']; }

    public function toMail($notifiable): MailMessage
    {
        $c     = $this->consignment->load('items.product');
        $fecha = $c->delivery_date?->format('d/m/Y') ?? now()->format('d/m/Y');

        $lineas = $c->items->map(fn($i) =>
            '• ' . ($i->product?->name ?? $i->product_name) .
            ' ×' . $i->quantity .
            '  —  $' . number_format($i->unit_price, 0, ',', '.') . ' c/u'
        )->implode("\n");

        $total = '$' . number_format(
            $c->items->sum(fn($i) => $i->quantity * $i->unit_price), 0, ',', '.'
        );

        $msg = (new MailMessage)
            ->subject("📦 Nueva entrega en consignación — AURA33")
            ->greeting("Hola, {$this->wholesaler->name}!")
            ->line("Registramos una nueva entrega en consignación para **{$this->wholesaler->business_name}**.")
            ->line("**Fecha de entrega:** {$fecha}")
            ->line("---")
            ->line("**Productos enviados:**")
            ->line($lineas)
            ->line("---")
            ->line("**Total en consignación:** {$total}");

        if ($c->notes) {
            $msg->line("**Notas:** {$c->notes}");
        }

        return $msg
            ->line("Podés ver tu cuenta corriente y el estado de tu stock en tu panel.")
            ->action('Ver mi panel', url('/mayoristas/panel'))
            ->line("Ante cualquier consulta respondé este email o escribinos por WhatsApp.")
            ->salutation("¡Gracias! · Equipo AURA33 💎");
    }
}
