<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\StripeController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Billing endpoint guards that short-circuit BEFORE any Stripe API call, so they
 * are safe to assert without a network/mock. The proration charge path
 * (always_invoice / error_if_incomplete → 402) calls Stripe statically and is
 * documented as an integration-test gap in the suite's README note.
 *
 * @see StripeController
 */
class BillingGuardsTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_rejects_an_invalid_plan(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/billing/checkout', ['plan' => 'enterprise'])
            ->assertStatus(422);
    }

    public function test_checkout_requires_authentication(): void
    {
        $this->postJson('/api/billing/checkout', ['plan' => 'pro'])
            ->assertUnauthorized();
    }

    public function test_addons_require_an_active_paid_subscription(): void
    {
        // Free user with no subscription → guarded out before touching Stripe.
        Sanctum::actingAs(User::factory()->create(['plan' => 'free']));

        $this->postJson('/api/billing/addons', ['pages_packs' => 1, 'links_packs' => 0])
            ->assertStatus(400);
    }

    public function test_preview_returns_null_amount_for_non_agency_user(): void
    {
        // previewAgency short-circuits (no Stripe call) when the user isn't an
        // agency subscriber.
        Sanctum::actingAs(User::factory()->create(['plan' => 'pro']));

        $this->postJson('/api/billing/preview', ['custom_pages' => 100])
            ->assertOk()
            ->assertJsonPath('amount_now', null);
    }

    public function test_portal_requires_a_billing_account(): void
    {
        Sanctum::actingAs(User::factory()->create(['stripe_customer_id' => null]));

        $this->postJson('/api/billing/portal')
            ->assertStatus(404);
    }
}
