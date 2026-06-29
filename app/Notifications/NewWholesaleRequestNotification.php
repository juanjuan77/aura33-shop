<?php

namespace App\Notifications;

use App\Models\WholesaleRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewWholesaleRequestNotification extends Notification
{
    use Queueable;

    public function __construct(public WholesaleRequest $wholesaler) {}

    public function via($notifiable): array { return ['mail']; }

    public function toMail($notifiable): MailMessage
    {
        $w = $this->wholesaler;

        return (new MailMessage)
            ->subject("🏪 Nueva solicitud mayorista — {$w->business_name}")
            ->greeting('¡Nueva solicitud mayorista!')
            ->line("**Negocio:** {$w->business_name}")
            ->line("**Nombre:** {$w->name}")
            ->line("**Email:** {$w->email}")
            ->line("**Teléfono:** {$w->phone}")
            ->line("**Ciudad:** {$w->city}, {$w->province}")
            ->line("**Tipo de negocio:** {$w->business_type}")
            ->when($w->cuit, fn($m) => $m->line("**CUIT:** {$w->cuit}"))
            ->when($w->notes, fn($m) => $m->line("**Notas:** {$w->notes}"))
            ->action('Ver solicitud en el admin', url('/a33mgr/wholesale-requests'));
    }
}
