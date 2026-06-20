<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Billing notification: a subscription invoice payment failed. Prompts the
 * user to update their card before access is downgraded.
 */
class PaymentFailed extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'category'    => 'billing',
            'title'       => 'Payment failed',
            'description' => 'We couldn’t charge your card. Update your payment method to keep your plan active.',
            'icon'        => 'bi-exclamation-triangle',
            'meta'        => ['action' => 'billing'],
        ];
    }
}
