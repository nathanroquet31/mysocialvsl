<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrialReminder extends Notification
{
    use Queueable;

    /**
     * @param int $daysLeft  Whole days left on the trial. 0 or less means the trial has ended.
     */
    public function __construct(public int $daysLeft)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $billingUrl = rtrim(config('app.frontend_url'), '/') . '/billing';

        if ($this->daysLeft <= 0) {
            return (new MailMessage)
                ->subject('Your MySocialVSL free trial has ended')
                ->greeting("Hi {$notifiable->name},")
                ->line('Your free trial has ended and your account is now on the Free plan.')
                ->line('Add a card to pick up right where you left off — your pages and links are safe.')
                ->action('Reactivate my plan', $billingUrl)
                ->line('Questions? Just reply to this email.');
        }

        $when = $this->daysLeft === 1 ? 'tomorrow' : "in {$this->daysLeft} days";

        return (new MailMessage)
            ->subject("Your MySocialVSL trial ends {$when}")
            ->greeting("Hi {$notifiable->name},")
            ->line("Your free trial ends {$when}.")
            ->line('Add your card now to keep your pages live and avoid any interruption — nothing changes today.')
            ->action('Add my card', $billingUrl)
            ->line('Want more time? Reply to this email and we’ll sort it out.');
    }
}
