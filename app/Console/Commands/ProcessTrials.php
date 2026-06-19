<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\TrialReminder;
use Illuminate\Console\Command;

class ProcessTrials extends Command
{
    protected $signature = 'trials:process';

    protected $description = 'Email card-free trial reminders and downgrade expired trials to free';

    /** Days-before-expiry buckets at which we email a reminder. */
    private const REMINDER_BUCKETS = [7, 1];

    public function handle(): int
    {
        $reminded = 0;
        $expired  = 0;

        // Only trialing users matter: a deadline set and no paid subscription yet.
        // (Converting clears trial_ends_at, so paid users never appear here.)
        User::whereNotNull('trial_ends_at')
            ->whereNull('stripe_subscription_id')
            ->chunkById(200, function ($users) use (&$reminded, &$expired) {
                foreach ($users as $user) {
                    if ($user->trial_ends_at->isPast()) {
                        $this->expire($user);
                        $expired++;
                    } elseif ($this->remind($user)) {
                        $reminded++;
                    }
                }
            });

        $this->info("Trials processed: {$reminded} reminded, {$expired} expired.");

        return self::SUCCESS;
    }

    /**
     * Send at most one reminder per run. Any bucket the deadline has now reached
     * (and that hasn't been sent) is fulfilled by a single email and marked sent,
     * so a missed daily run never causes a double-send or a skipped reminder.
     */
    private function remind(User $user): bool
    {
        $sent     = $user->trial_reminders ?? [];
        $daysLeft = (int) ceil(now()->floatDiffInDays($user->trial_ends_at, false));

        $due = array_filter(
            self::REMINDER_BUCKETS,
            fn ($bucket) => $daysLeft <= $bucket && ! in_array($bucket, $sent, true)
        );

        if (empty($due)) {
            return false;
        }

        $user->notify(new TrialReminder($daysLeft));
        $user->update(['trial_reminders' => array_values(array_unique([...$sent, ...$due]))]);

        return true;
    }

    /** Email the end-of-trial notice once, then drop the user to the Free plan. */
    private function expire(User $user): void
    {
        $sent = $user->trial_reminders ?? [];

        if (! in_array('ended', $sent, true)) {
            $user->notify(new TrialReminder(0));
        }

        $user->update([
            'plan'            => 'free',
            'trial_ends_at'   => null,
            'trial_reminders' => null,
            'agency_pages'    => null,
            'agency_links'    => null,
            // is_beta stays: they remain in the early-access cohort.
        ]);
    }
}
