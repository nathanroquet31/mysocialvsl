<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\AuthController;
use App\Models\User;
use App\Notifications\Welcome;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * Affiliate attribution plumbing: signup with ?ref=CODE must stamp referred_by
 * with the owner of that affiliate_code. This is the shared rail both the beta
 * (coach trial) and the classic-affiliate tracks build on.
 *
 * @see AuthController::register
 */
class RegistrationReferralTest extends TestCase
{
    use RefreshDatabase;

    private function registerPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'New Creator',
            'email' => 'new@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ], $overrides);
    }

    public function test_registration_links_referred_by_to_the_affiliate_code_owner(): void
    {
        $coach = User::factory()->create(['affiliate_code' => 'COACH1']);

        $this->postJson('/api/register', $this->registerPayload(['ref' => 'COACH1']))
            ->assertCreated();

        $this->assertSame($coach->id, User::where('email', 'new@example.com')->first()->referred_by);
    }

    public function test_referral_code_lookup_is_case_insensitive(): void
    {
        $coach = User::factory()->create(['affiliate_code' => 'COACH1']);

        // The controller upper-cases the incoming ref before lookup.
        $this->postJson('/api/register', $this->registerPayload(['ref' => 'coach1']))
            ->assertCreated();

        $this->assertSame($coach->id, User::where('email', 'new@example.com')->first()->referred_by);
    }

    public function test_unknown_referral_code_leaves_referred_by_null(): void
    {
        $this->postJson('/api/register', $this->registerPayload(['ref' => 'DOESNOTEXIST']))
            ->assertCreated();

        $this->assertNull(User::where('email', 'new@example.com')->first()->referred_by);
    }

    public function test_registration_without_ref_has_no_referrer(): void
    {
        $this->postJson('/api/register', $this->registerPayload())
            ->assertCreated();

        $this->assertNull(User::where('email', 'new@example.com')->first()->referred_by);
    }

    public function test_new_users_default_to_the_free_plan(): void
    {
        // Baseline for the trial feature: without the beta rail, a fresh signup
        // is on Free (no card, no agency trial). See TrialLifecycleTest.
        $this->postJson('/api/register', $this->registerPayload())
            ->assertCreated();

        $this->assertSame('free', User::where('email', 'new@example.com')->first()->plan);
    }

    public function test_registration_sends_the_welcome_notification(): void
    {
        Notification::fake();

        $this->postJson('/api/register', $this->registerPayload())
            ->assertCreated();

        $user = User::where('email', 'new@example.com')->first();

        Notification::assertSentTo($user, Welcome::class, function (Welcome $notification) use ($user) {
            return in_array('mail', $notification->via($user));
        });
    }
}
