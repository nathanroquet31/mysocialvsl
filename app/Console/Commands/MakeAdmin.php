<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

/**
 * Grant founder/admin access by email — avoids the tinker + shell-quoting pain
 * on Laravel Cloud's one-shot command runner. is_admin is non-fillable by
 * design, so we forceFill it here.
 */
class MakeAdmin extends Command
{
    protected $signature = 'user:make-admin {email}';

    protected $description = 'Grant admin (founder) access to a user by email';

    public function handle(): int
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("No user found for {$email}.");
            return self::FAILURE;
        }

        $user->forceFill(['is_admin' => true])->save();

        $this->info("✅ {$user->email} is now an admin (is_admin = " . (int) $user->is_admin . ").");
        return self::SUCCESS;
    }
}
