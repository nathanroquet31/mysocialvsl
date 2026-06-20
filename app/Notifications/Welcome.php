<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Account notification sent right after sign-up: greets the creator and points
 * them at support. Trial-specific welcome is handled by PlanChanged.
 */
class Welcome extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $name = $notifiable->name ? explode(' ', $notifiable->name)[0] : 'there';

        return [
            'category'    => 'account',
            'title'       => "Welcome to MySocialVSL, {$name} 👋",
            'description' => 'Create your first VSL page to get started. Got a question? The Help Center and support are one click away.',
            'icon'        => 'bi-stars',
            'meta'        => ['action' => 'help'],
        ];
    }
}
