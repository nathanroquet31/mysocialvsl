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

#[Fillable(['name', 'email', 'password', 'plan', 'stripe_customer_id', 'stripe_subscription_id', 'plan_expires_at', 'extra_pages', 'extra_links', 'agency_pages', 'agency_links', 'timezone', 'preferences', 'avatar_url', 'affiliate_code', 'referred_by'])]
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
            'preferences' => 'array',
            'agency_pages' => 'integer',
            'agency_links' => 'integer',
        ];
    }
}
