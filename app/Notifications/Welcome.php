<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
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
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $name = $notifiable->name ? explode(' ', $notifiable->name)[0] : 'there';
        $dashboardUrl = rtrim(config('app.frontend_url'), '/') . '/dashboard';

        return (new MailMessage)
            ->subject('Welcome to MySocialVSL')
            ->greeting("Welcome to MySocialVSL, {$name}!")
            ->line('You can now create your first page and add your links to get started.')
            ->action('Go to dashboard', $dashboardUrl);
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
