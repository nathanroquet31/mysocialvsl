<?php

namespace Tests\Feature;

use App\Models\Commission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Affiliate engine: a paid invoice from a referred customer credits a 20% recurring
 * commission to the *flagged* affiliate who referred them. Idempotent on the invoice id.
 *
 * @see \App\Http\Controllers\Api\StripeController::handlePaymentSucceeded
 * @see \App\Http\Controllers\Api\AffiliateController::show
 */
class AffiliateCommissionTest extends TestCase
{
    use RefreshDatabase;

    private string $secret = 'whsec_test_secret';

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.stripe.webhook_secret' => $this->secret]);
    }

    private function postPaymentSucceeded(string $invoiceId, string $customerId, int $amountPaid)
    {
        $event = [
            'id'   => 'evt_' . $invoiceId,
            'type' => 'invoice.payment_succeeded',
            'data' => ['object' => [
                'id'          => $invoiceId,
                'customer'    => $customerId,
                'amount_paid' => $amountPaid,
                'currency'    => 'usd',
            ]],
        ];

        $payload   = json_encode($event);
        $timestamp = time();
        $signature = hash_hmac('sha256', "{$timestamp}.{$payload}", $this->secret);

        return $this->call('POST', '/api/billing/webhook', [], [], [], [
            'HTTP_STRIPE_SIGNATURE' => "t={$timestamp},v1={$signature}",
            'CONTENT_TYPE' => 'application/json',
        ], $payload);
    }

    public function test_referred_payment_credits_20_percent_to_the_affiliate(): void
    {
        $affiliate = User::factory()->create(['is_affiliate' => true]);
        $customer  = User::factory()->create([
            'referred_by' => $affiliate->id,
            'stripe_customer_id' => 'cus_ref_1',
        ]);

        $this->postPaymentSucceeded('in_1', 'cus_ref_1', 4900)->assertOk();

        $this->assertDatabaseCount('commissions', 1);
        $commission = Commission::first();
        $this->assertSame($affiliate->id, $commission->affiliate_id);
        $this->assertSame($customer->id, $commission->referred_user_id);
        $this->assertSame(4900, $commission->amount_paid_cents);
        $this->assertSame(980, $commission->commission_cents); // 20% of $49.00
        $this->assertSame('pending', $commission->status);
    }

    public function test_duplicate_invoice_is_idempotent(): void
    {
        $affiliate = User::factory()->create(['is_affiliate' => true]);
        User::factory()->create(['referred_by' => $affiliate->id, 'stripe_customer_id' => 'cus_ref_2']);

        $this->postPaymentSucceeded('in_dup', 'cus_ref_2', 4900)->assertOk();
        $this->postPaymentSucceeded('in_dup', 'cus_ref_2', 4900)->assertOk(); // retry

        $this->assertDatabaseCount('commissions', 1);
    }

    public function test_recurring_payments_each_create_a_commission(): void
    {
        $affiliate = User::factory()->create(['is_affiliate' => true]);
        User::factory()->create(['referred_by' => $affiliate->id, 'stripe_customer_id' => 'cus_ref_3']);

        $this->postPaymentSucceeded('in_m1', 'cus_ref_3', 4900)->assertOk();
        $this->postPaymentSucceeded('in_m2', 'cus_ref_3', 4900)->assertOk();

        $this->assertDatabaseCount('commissions', 2);
        $this->assertSame(1960, (int) $affiliate->commissions()->sum('commission_cents'));
    }

    public function test_non_affiliate_referrer_earns_nothing(): void
    {
        // A beta-partner coach refers free trials, but is NOT an affiliate → no commission.
        $coach    = User::factory()->create(['is_affiliate' => false, 'is_beta_partner' => true]);
        User::factory()->create(['referred_by' => $coach->id, 'stripe_customer_id' => 'cus_ref_4']);

        $this->postPaymentSucceeded('in_x', 'cus_ref_4', 4900)->assertOk();

        $this->assertDatabaseCount('commissions', 0);
    }

    public function test_unreferred_payment_earns_nothing(): void
    {
        User::factory()->create(['referred_by' => null, 'stripe_customer_id' => 'cus_solo']);

        $this->postPaymentSucceeded('in_solo', 'cus_solo', 4900)->assertOk();

        $this->assertDatabaseCount('commissions', 0);
    }

    public function test_affiliate_dashboard_shows_real_earnings(): void
    {
        $affiliate = User::factory()->create(['is_affiliate' => true]);
        User::factory()->create(['referred_by' => $affiliate->id, 'stripe_customer_id' => 'cus_ref_5']);

        $this->postPaymentSucceeded('in_a', 'cus_ref_5', 4900)->assertOk();

        $this->actingAs($affiliate)->getJson('/api/affiliate')
            ->assertOk()
            ->assertJsonPath('enrolled', true)
            ->assertJsonPath('total_earned', 9.8)
            ->assertJsonPath('pending_payout', 9.8)
            ->assertJsonPath('paid_out', 0);
    }

    public function test_non_affiliate_user_is_not_enrolled(): void
    {
        $user = User::factory()->create(['is_affiliate' => false]);

        $this->actingAs($user)->getJson('/api/affiliate')
            ->assertOk()
            ->assertJsonPath('enrolled', false);
    }
}
