<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // End of a card-free beta trial. NULL = no trial. While in the future
            // (and with no paid Stripe subscription) the user keeps their granted plan.
            $table->timestamp('trial_ends_at')->nullable()->after('plan_expires_at');

            // Cohort tag for trial/beta users — gets new tools ahead of everyone else.
            $table->boolean('is_beta')->default(false)->after('is_admin');

            // A coach/manager whose affiliate code grants the beta trial to their signups.
            // Only the founder flags these, so trials can't be self-minted by any referrer.
            $table->boolean('is_beta_partner')->default(false)->after('is_beta');

            // Which trial reminder buckets have already been emailed (e.g. [7, 1, "ended"]),
            // so the daily job never double-sends and survives a missed run.
            $table->json('trial_reminders')->nullable()->after('trial_ends_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['trial_ends_at', 'is_beta', 'is_beta_partner', 'trial_reminders']);
        });
    }
};
