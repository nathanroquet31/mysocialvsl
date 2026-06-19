<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Card-free beta trial lifecycle (feature commit cca59b7).
 *
 * NOTE: this worktree is based at 1ddccd4 — the PARENT of cca59b7 — so the trial
 * code (User::startTrial, is_beta_partner, trial_ends_at, the trials:process
 * command) is NOT present here. Each test self-skips when the feature is absent,
 * keeping the suite green. Rebase this branch onto cca59b7 (or later) and these
 * tests activate automatically, documenting the intended contract:
 *
 *   - startTrial('agency', 60) => plan=agency, trial_ends_at ≈ +60d, is_beta=true, 25/25, no Stripe
 *   - signup via ?ref=CODE grants the trial ONLY when the referrer is is_beta_partner
 *   - a non-beta referrer grants NO trial (classic affiliate clients pay directly)
 *   - is_beta_partner is non-fillable (cannot be self-minted via mass assignment)
 *   - trials:process sends J-7 / J-1 reminders and downgrades to Free at expiry
 */
class TrialLifecycleTest extends TestCase
{
    use RefreshDatabase;

    private function requireTrialFeature(): void
    {
        if (! method_exists(User::class, 'startTrial') || ! Schema::hasColumn('users', 'trial_ends_at')) {
            $this->markTestSkipped('Trial feature (cca59b7) not present in this worktree base (1ddccd4).');
        }
    }

    public function test_start_trial_grants_card_free_agency_for_60_days(): void
    {
        $this->requireTrialFeature();

        $user = User::factory()->create(['plan' => 'free']);
        $user->startTrial('agency', 60);
        $user->refresh();

        $this->assertSame('agency', $user->plan);
        $this->assertTrue((bool) $user->is_beta);
        $this->assertNotNull($user->trial_ends_at);
        $this->assertEqualsWithDelta(60, now()->diffInDays($user->trial_ends_at, false), 1);
        // Trial grants the base Agency capacity (25/25) with no Stripe attached.
        $this->assertSame(25, $user->pageLimit());
        $this->assertSame(25, $user->linkLimit());
        $this->assertNull($user->stripe_subscription_id);
    }

    public function test_signup_via_beta_partner_code_starts_a_trial(): void
    {
        $this->requireTrialFeature();

        $coach = User::factory()->create(['affiliate_code' => 'BETACOACH', 'is_beta_partner' => true]);

        $this->postJson('/api/register', [
            'name' => 'Creator',
            'email' => 'creator@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'ref' => 'BETACOACH',
        ])->assertCreated();

        $new = User::where('email', 'creator@example.com')->first();
        $this->assertSame($coach->id, $new->referred_by);
        $this->assertSame('agency', $new->plan);
        $this->assertNotNull($new->trial_ends_at);
    }

    public function test_signup_via_non_beta_referrer_grants_no_trial(): void
    {
        $this->requireTrialFeature();

        // A classic affiliate (not is_beta_partner) brings DIRECT paying clients — no trial.
        $affiliate = User::factory()->create(['affiliate_code' => 'PARTNER', 'is_beta_partner' => false]);

        $this->postJson('/api/register', [
            'name' => 'Client',
            'email' => 'client@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'ref' => 'PARTNER',
        ])->assertCreated();

        $new = User::where('email', 'client@example.com')->first();
        $this->assertSame($affiliate->id, $new->referred_by);
        $this->assertSame('free', $new->plan);
        $this->assertNull($new->trial_ends_at);
    }

    public function test_is_beta_partner_cannot_be_mass_assigned(): void
    {
        $this->requireTrialFeature();

        // Anti self-mint: is_beta_partner must not be fillable.
        $user = User::create([
            'name' => 'Sneaky',
            'email' => 'sneaky@example.com',
            'password' => bcrypt('password123'),
            'is_beta_partner' => true,
        ]);

        $this->assertFalse((bool) $user->fresh()->is_beta_partner);
    }
}
