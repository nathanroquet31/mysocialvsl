<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Billing notification: the user's plan changed (upgrade, downgrade, or a
 * card-free trial converting to paid). Stored in-app; no email here (Stripe
 * already sends payment receipts).
 */
class PlanChanged extends Notification
{
    use Queueable;

    /**
     * @param  string  $plan     The new plan: free | pro | agency
     * @param  string  $context  upgraded | downgraded | trial_converted
     */
    public function __construct(
        public string $plan,
        public string $context = 'upgraded',
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $label = ucfirst($this->plan);

        [$title, $description] = match ($this->context) {
            'downgraded'      => ['Plan changed to Free', "Your subscription ended — you're now on the Free plan. Upgrade anytime to get your features back."],
            'trial_converted' => ["Welcome to {$label} 🎉", "Your trial converted — you're now a paying {$label} member. Thanks for the support!"],
            default           => ["You're now on {$label} 🎉", "Your account has been upgraded to {$label}. All {$label} features are unlocked."],
        };

        return [
            'category'    => 'billing',
            'title'       => $title,
            'description' => $description,
            'icon'        => 'bi-credit-card-2-front',
            'meta'        => ['plan' => $this->plan, 'context' => $this->context],
        ];
    }
}
