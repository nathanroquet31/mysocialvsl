<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\StripeController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Stripe webhook: signature verification + the plan-state transitions it drives.
 * We sign payloads with the configured secret (the real HMAC scheme Stripe uses)
 * so the genuine Webhook::constructEvent path is exercised — no Stripe network call.
 *
 * @see StripeController::webhook
 */
class StripeWebhookTest extends TestCase
{
    use RefreshDatabase;

    private string $secret = 'whsec_test_secret';

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.stripe.webhook_secret' => $this->secret]);
    }

    /**
     * Post a webhook event with a valid Stripe-Signature header.
     */
    private function postSignedEvent(array $event)
    {
        $payload = json_encode($event);
        $timestamp = time();
        $signature = hash_hmac('sha256', "{$timestamp}.{$payload}", $this->secret);

        return $this->call(
            'POST',
            '/api/billing/webhook',
            [],
            [],
            [],
            [
                'HTTP_STRIPE_SIGNATURE' => "t={$timestamp},v1={$signature}",
                'CONTENT_TYPE' => 'application/json',
            ],
            $payload
        );
    }

    public function test_invalid_signature_is_rejected(): void
    {
        $response = $this->call(
            'POST',
            '/api/billing/webhook',
            [],
            [],
            [],
            ['HTTP_STRIPE_SIGNATURE' => 't=123,v1=deadbeef', 'CONTENT_TYPE' => 'application/json'],
            json_encode(['type' => 'checkout.session.completed', 'data' => ['object' => []]])
        );

        $response->assertStatus(400);
        $response->assertJsonPath('error', 'Invalid signature');
    }

    public function test_checkout_completed_upgrades_user_to_pro(): void
    {
        $user = User::factory()->create(['plan' => 'free']);

        $this->postSignedEvent([
            'id' => 'evt_1',
            'type' => 'checkout.session.completed',
            'data' => ['object' => [
                'subscription' => 'sub_pro_123',
                'metadata' => ['user_id' => (string) $user->id, 'plan' => 'pro'],
            ]],
        ])->assertOk();

        $user->refresh();
        $this->assertSame('pro', $user->plan);
        $this->assertSame('sub_pro_123', $user->stripe_subscription_id);
        $this->assertNull($user->plan_expires_at);
    }

    public function test_checkout_completed_sets_agency_capacities_from_metadata(): void
    {
        $user = User::factory()->create(['plan' => 'free']);

        $this->postSignedEvent([
            'id' => 'evt_2',
            'type' => 'checkout.session.completed',
            'data' => ['object' => [
                'subscription' => 'sub_agency_1',
                'metadata' => [
                    'user_id' => (string) $user->id,
                    'plan' => 'agency',
                    'agency_pages' => '200',
                    'agency_links' => '100',
                ],
            ]],
        ])->assertOk();

        $user->refresh();
        $this->assertSame('agency', $user->plan);
        $this->assertSame(200, $user->agency_pages);
        $this->assertSame(100, $user->agency_links);
    }

    public function test_subscription_deleted_downgrades_to_free(): void
    {
        $user = User::factory()->create([
            'plan' => 'agency',
            'stripe_subscription_id' => 'sub_live',
            'agency_pages' => 200,
        ]);

        $this->postSignedEvent([
            'id' => 'evt_3',
            'type' => 'customer.subscription.deleted',
            'data' => ['object' => [
                'id' => 'sub_live',
                'metadata' => ['user_id' => (string) $user->id],
            ]],
        ])->assertOk();

        $user->refresh();
        $this->assertSame('free', $user->plan);
        $this->assertNull($user->stripe_subscription_id);
        $this->assertNull($user->agency_pages);
    }

    public function test_deleting_a_stale_subscription_does_not_touch_current_plan(): void
    {
        // Guard against an orphan/old subscription cancellation wiping the active plan.
        $user = User::factory()->create([
            'plan' => 'pro',
            'stripe_subscription_id' => 'sub_current',
        ]);

        $this->postSignedEvent([
            'id' => 'evt_4',
            'type' => 'customer.subscription.deleted',
            'data' => ['object' => [
                'id' => 'sub_OLD_orphan',
                'metadata' => ['user_id' => (string) $user->id],
            ]],
        ])->assertOk();

        $user->refresh();
        $this->assertSame('pro', $user->plan, 'a stale sub deletion must not downgrade the user');
        $this->assertSame('sub_current', $user->stripe_subscription_id);
    }

    public function test_payment_failed_event_is_accepted_without_changing_plan(): void
    {
        $user = User::factory()->create(['plan' => 'pro', 'stripe_subscription_id' => 'sub_x']);

        $this->postSignedEvent([
            'id' => 'evt_5',
            'type' => 'invoice.payment_failed',
            'data' => ['object' => ['id' => 'in_1', 'subscription' => 'sub_x']],
        ])->assertOk();

        $this->assertSame('pro', $user->fresh()->plan);
    }

    public function test_unhandled_event_type_is_a_no_op_ok(): void
    {
        $this->postSignedEvent([
            'id' => 'evt_6',
            'type' => 'customer.created',
            'data' => ['object' => ['id' => 'cus_1']],
        ])->assertOk()->assertJsonPath('ok', true);
    }
}
