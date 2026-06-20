<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'plan', 'stripe_customer_id', 'stripe_subscription_id', 'plan_expires_at', 'trial_ends_at', 'trial_reminders', 'is_beta', 'extra_pages', 'extra_links', 'agency_pages', 'agency_links', 'timezone', 'preferences', 'avatar_url', 'affiliate_code', 'referred_by'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    public function pages()
    {
        return $this->hasMany(Page::class);
    }

    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    /** Free plan: no paid features. Admins are never treated as free. */
    public function isFree(): bool
    {
        return ! $this->isAdmin() && (($this->plan ?? 'free') === 'free');
    }

    /** Paid plan (Creator/Pro or Agency). Unlocks deeplink, strict, bot protection. */
    public function isPaid(): bool
    {
        return $this->isAdmin() || in_array($this->plan, ['pro', 'agency'], true);
    }

    /** Agency plan: top tier — white-label, geo targeting, social analytics, API. */
    public function isAgency(): bool
    {
        return $this->isAdmin() || ($this->plan === 'agency');
    }

    /**
     * On an active card-free trial: the deadline is in the future and the user
     * has not yet converted to a paid Stripe subscription.
     */
    public function onTrial(): bool
    {
        return $this->trial_ends_at !== null
            && $this->trial_ends_at->isFuture()
            && ! $this->stripe_subscription_id;
    }

    /** Whole days left on the trial (rounded up), or null when not on a trial. */
    public function trialDaysLeft(): ?int
    {
        if (! $this->onTrial()) {
            return null;
        }

        return (int) ceil(now()->floatDiffInDays($this->trial_ends_at, false));
    }

    /**
     * Start a card-free beta trial: grant the plan now, set the deadline, tag the
     * cohort, and reset reminder bookkeeping. No Stripe object is created.
     */
    public function startTrial(string $plan = 'agency', int $days = 60): void
    {
        $this->update([
            'plan'            => $plan,
            'trial_ends_at'   => now()->addDays($days),
            'trial_reminders' => [],
            'is_beta'         => true,
            // Agency base capacity (25/25). NULL columns already resolve to 25.
            'agency_pages'    => $plan === 'agency' ? 25 : $this->agency_pages,
            'agency_links'    => $plan === 'agency' ? 25 : $this->agency_links,
        ]);
    }

    public function pageLimit(): int
    {
        if ($this->isAdmin()) return PHP_INT_MAX;
        if ($this->plan === 'agency') {
            return $this->agencyCapacity($this->agency_pages);
        }
        $base = $this->plan === 'pro' ? 5 : 1;
        return $base + (int) ($this->extra_pages ?? 0);
    }

    public function linkLimit(): int
    {
        if ($this->isAdmin()) return PHP_INT_MAX;
        if ($this->plan === 'agency') {
            return $this->agencyCapacity($this->agency_links);
        }
        $base = $this->plan === 'pro' ? 2 : 1;
        return $base + (int) ($this->extra_links ?? 0);
    }

    /**
     * Resolve an Agency capacity column to a concrete limit.
     * null = unconfigured (base 25 included) · 0 = unlimited (∞) · N = that tier.
     */
    private function agencyCapacity(?int $stored): int
    {
        $cap = $stored ?? 25;
        return $cap === 0 ? PHP_INT_MAX : $cap;
    }

    public function canCreatePage(): bool
    {
        if ($this->isAdmin()) return true;
        $count = $this->pages()->where('page_type', '!=', 'direct')->count();
        return $count < $this->pageLimit();
    }

    public function canCreateLink(): bool
    {
        if ($this->isAdmin()) return true;
        $count = $this->pages()->where('page_type', 'direct')->count();
        return $count < $this->linkLimit();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_beta' => 'boolean',
            'is_beta_partner' => 'boolean',
            'preferences' => 'array',
            'trial_ends_at' => 'datetime',
            'trial_reminders' => 'array',
            'agency_pages' => 'integer',
            'agency_links' => 'integer',
        ];
    }
}
