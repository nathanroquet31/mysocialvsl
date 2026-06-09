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

#[Fillable(['name', 'email', 'password', 'plan', 'is_admin', 'stripe_customer_id', 'stripe_subscription_id', 'plan_expires_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    public function pages()
    {
        return $this->hasMany(Page::class);
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    public function pageLimit(): int
    {
        if ($this->isAdmin()) return PHP_INT_MAX;
        return match ($this->plan) {
            'agency' => PHP_INT_MAX,
            'pro'    => 5,
            default  => 1,
        };
    }

    public function canCreatePage(): bool
    {
        if ($this->isAdmin()) return true;
        return $this->pages()->count() < $this->pageLimit();
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
        ];
    }
}
