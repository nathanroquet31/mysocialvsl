<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

/**
 * Grant a plan to a user by email — permanently, or for a limited time.
 *
 *   php artisan user:set-plan email@x.com agency               # permanent Agency
 *   php artisan user:set-plan email@x.com agency --days=60     # 60-day card-free trial
 *   php artisan user:set-plan email@x.com agency --unlimited   # +∞ pages/links
 *   php artisan user:set-plan email@x.com free                 # revoke to free
 *
 * --days uses the existing card-free trial mechanism (auto-expires, no Stripe).
 */
class SetPlan extends Command
{
    protected $signature = 'user:set-plan {email} {plan=agency} {--days=} {--unlimited}';

    protected $description = 'Grant a plan to a user by email (permanent or time-limited)';

    public function handle(): int
    {
        $email = $this->argument('email');
        $plan  = $this->argument('plan');
        $days  = $this->option('days');

        if (! in_array($plan, ['free', 'pro', 'agency'], true)) {
            $this->error("Plan must be free, pro or agency (got: {$plan}).");
            return self::FAILURE;
        }

        $user = User::where('email', $email)->first();
        if (! $user) {
            $this->error("No user found for {$email}.");
            return self::FAILURE;
        }

        if ($days !== null) {
            // Time-limited, card-free access (auto-expires via ProcessTrials).
            $user->startTrial($plan, (int) $days);
            $this->info("✅ {$user->email} → {$plan} for {$days} days (trial, no card).");
        } else {
            // Permanent grant: set the plan, clear any trial deadline.
            $user->forceFill(['plan' => $plan, 'trial_ends_at' => null])->save();
            $this->info("✅ {$user->email} → {$plan} (permanent).");
        }

        if ($this->option('unlimited') && $plan === 'agency') {
            $user->forceFill(['agency_pages' => 0, 'agency_links' => 0])->save(); // 0 = ∞
            $this->info('   + unlimited pages & links.');
        }

        return self::SUCCESS;
    }
}
