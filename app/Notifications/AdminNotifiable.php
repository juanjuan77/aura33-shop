<?php

namespace App\Notifications;

use Illuminate\Notifications\Notifiable;

class AdminNotifiable
{
    use Notifiable;

    public function __construct(public string $email, public string $name = 'Admin') {}

    public function routeNotificationForMail(): string
    {
        return $this->email;
    }
}
