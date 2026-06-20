<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Security notification: a sign-in happened from an IP the account hadn't
 * been seen on before. Geo is best-effort (may be null).
 */
class NewLogin extends Notification
{
    use Queueable;

    public function __construct(
        public ?string $city = null,
        public ?string $country = null,
        public ?string $ip = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $place = collect([$this->city, $this->country])->filter()->implode(', ') ?: 'a new location';

        return [
            'category'    => 'security',
            'title'       => "New login from {$place}",
            'description' => 'If this wasn’t you, change your password and sign out other devices in Settings.',
            'icon'        => 'bi-shield-lock',
            'meta'        => ['ip' => $this->ip, 'city' => $this->city, 'country' => $this->country],
        ];
    }
}
