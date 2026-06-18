<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Checkout\Session;
use Stripe\BillingPortal\Session as PortalSession;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    // POST /billing/checkout
    //   { plan: 'pro' | 'agency', custom_pages?: int|null, custom_links?: int|null }
    //   custom_* only apply to agency. null = unlimited (∞), absent = base (25).
    public function checkout(Request $request)
    {
        $request->validate([
            'plan'         => 'required|in:pro,agency',
            'custom_pages' => 'nullable|integer',
            'custom_links' => 'nullable|integer',
        ]);

        $user = $request->user();
        $plan = $request->plan;

        // Create or retrieve Stripe customer
        if (!$user->stripe_customer_id) {
            $customer = Customer::create([
                'email' => $user->email,
                'name'  => $user->name,
                'metadata' => ['user_id' => $user->id],
            ]);
            $user->update(['stripe_customer_id' => $customer->id]);
        }

        // Build the desired line items + resolved capacities (server-side, never trust client price).
        if ($plan === 'agency') {
            // input() returns null only when the key is present and null (∞); absent → base 25.
            $pages = $this->resolveTier('pages', $request->input('custom_pages', 25));
            $links = $this->resolveTier('links', $request->input('custom_links', 25));

            $lineItems = [['price' => config('services.stripe.price_agency'), 'quantity' => 1]];
            if ($pages['price']) $lineItems[] = ['price' => $pages['price'], 'quantity' => 1];
            if ($links['price']) $lineItems[] = ['price' => $links['price'], 'quantity' => 1];

            $metadata = [
                'user_id'      => (string) $user->id,
                'plan'         => 'agency',
                'agency_pages' => (string) $pages['capacity'],
                'agency_links' => (string) $links['capacity'],
            ];

            // Already on an active subscription → update it in place (avoid duplicate subs).
            if ($user->stripe_subscription_id) {
                return $this->updateAgencySubscription($user, $lineItems, $pages['capacity'], $links['capacity']);
            }
        } else {
            $lineItems = [['price' => config('services.stripe.price_pro'), 'quantity' => 1]];
            $metadata  = ['user_id' => (string) $user->id, 'plan' => 'pro'];
        }

        $session = Session::create([
            'customer'            => $user->stripe_customer_id,
            'mode'                => 'subscription',
            'payment_method_types' => ['card'],
            'line_items'          => $lineItems,
            'success_url'         => config('app.frontend_url') . '/dashboard?upgraded=1',
            'cancel_url'          => config('app.frontend_url') . '/billing',
            'metadata'            => $metadata,
            'subscription_data'   => ['metadata' => $metadata],
            'allow_promotion_codes' => true,
        ]);

        return response()->json(['url' => $session->url]);
    }

    /**
     * Resolve a requested Agency tier value to its capacity + extra price id.
     * Returns ['capacity' => int (25..800, or 0 = ∞), 'price' => ?string extra price id].
     */
    private function resolveTier(string $dimension, $value): array
    {
        $tiers   = config("services.stripe.agency_tiers.$dimension");
        $allowed = [50, 100, 200, 400, 600, 800];

        // null = unlimited (∞)
        if ($value === null || $value === '') {
            return ['capacity' => 0, 'price' => $tiers[0] ?? null];
        }

        $value = (int) $value;
        if ($value <= 25 || !in_array($value, $allowed, true)) {
            return ['capacity' => 25, 'price' => null]; // base only, no extra line item
        }

        return ['capacity' => $value, 'price' => $tiers[$value] ?? null];
    }

    /**
     * Update an existing subscription's agency line items (base + page tier + link tier)
     * instead of creating a second subscription. Applies immediate proration.
     */
    private function updateAgencySubscription(User $user, array $desiredLineItems, int $pagesCap, int $linksCap)
    {
        $subscription = \Stripe\Subscription::retrieve([
            'id'     => $user->stripe_subscription_id,
            'expand' => ['items.data.price'],
        ]);

        // Map every known agency price id so we can recognise / remove stale tier items.
        $managedPrices = [config('services.stripe.price_agency')];
        foreach (config('services.stripe.agency_tiers') as $dimTiers) {
            $managedPrices = array_merge($managedPrices, array_values($dimTiers));
        }

        $desiredPrices = array_column($desiredLineItems, 'price');
        $existing      = [];
        foreach ($subscription->items->data as $item) {
            $existing[$item->price->id] = $item->id;
        }

        $items = [];
        // Add anything desired that isn't already present.
        foreach ($desiredPrices as $priceId) {
            if (!isset($existing[$priceId])) {
                $items[] = ['price' => $priceId, 'quantity' => 1];
            }
        }
        // Delete managed items we no longer want (old tier / pro packs left over).
        foreach ($existing as $priceId => $itemId) {
            if (in_array($priceId, $managedPrices, true) && !in_array($priceId, $desiredPrices, true)) {
                $items[] = ['id' => $itemId, 'deleted' => true];
            }
        }

        if (!empty($items)) {
            try {
                \Stripe\Subscription::update($subscription->id, [
                    'items'              => $items,
                    'proration_behavior' => 'always_invoice',     // invoice the prorated difference NOW
                    'payment_behavior'   => 'error_if_incomplete', // fail the update if it can't be paid
                    'off_session'        => true,                  // charge the saved card without user interaction
                ]);
            } catch (\Stripe\Exception\ApiErrorException $e) {
                // The prorated charge could not be collected (declined card, SCA required, etc.).
                // Stripe did NOT apply the change, so we must NOT grant access — the user keeps
                // their previous plan/limits. This is the guard against "access without paying".
                return response()->json([
                    'error'   => 'payment_failed',
                    'message' => 'We could not charge the prorated difference to your card. Your plan was not changed.',
                ], 402);
            }
        }

        $user->update([
            'plan'         => 'agency',
            'agency_pages' => $pagesCap,
            'agency_links' => $linksCap,
            'plan_expires_at' => null,
        ]);
        $user->refresh();

        return response()->json([
            'updated'    => true,
            'plan'       => 'agency',
            'page_limit' => $user->pageLimit(),
            'link_limit' => $user->linkLimit(),
        ]);
    }

    // POST /billing/addons  { pages_packs: int, links_packs: int }
    public function updateAddons(Request $request)
    {
        $request->validate([
            'pages_packs' => 'required|integer|min:0|max:100',
            'links_packs' => 'required|integer|min:0|max:100',
        ]);

        $user = $request->user();

        if (!$user->stripe_subscription_id || $user->plan === 'free') {
            return response()->json(['error' => 'A paid subscription is required to add packs.'], 400);
        }

        $subscription = \Stripe\Subscription::retrieve([
            'id'     => $user->stripe_subscription_id,
            'expand' => ['items.data.price'],
        ]);

        $pricePages = config('services.stripe.price_extra_pages');
        $priceLinks = config('services.stripe.price_extra_links');

        $existingItems = [];
        foreach ($subscription->items->data as $item) {
            $existingItems[$item->price->id] = $item;
        }

        $itemsToUpdate = [];

        foreach ([
            [$pricePages, $request->pages_packs],
            [$priceLinks, $request->links_packs],
        ] as [$priceId, $quantity]) {
            $existing = $existingItems[$priceId] ?? null;
            if ($quantity === 0 && $existing) {
                $itemsToUpdate[] = ['id' => $existing->id, 'deleted' => true];
            } elseif ($quantity > 0 && $existing && $existing->quantity !== $quantity) {
                $itemsToUpdate[] = ['id' => $existing->id, 'quantity' => $quantity];
            } elseif ($quantity > 0 && !$existing) {
                $itemsToUpdate[] = ['price' => $priceId, 'quantity' => $quantity];
            }
        }

        if (!empty($itemsToUpdate)) {
            // Adding packs charges the prorated difference immediately; removing packs is a
            // credit (no charge). error_if_incomplete + off_session guarantees we only apply
            // the change locally when the proration is actually paid.
            try {
                \Stripe\Subscription::update($subscription->id, [
                    'items'              => $itemsToUpdate,
                    'proration_behavior' => 'always_invoice',
                    'payment_behavior'   => 'error_if_incomplete',
                    'off_session'        => true,
                ]);
            } catch (\Stripe\Exception\ApiErrorException $e) {
                return response()->json([
                    'error'   => 'payment_failed',
                    'message' => 'We could not charge the prorated difference to your card. Your add-ons were not changed.',
                ], 402);
            }
        }

        $user->update([
            'extra_pages' => $request->pages_packs * 100,
            'extra_links' => $request->links_packs * 100,
        ]);

        $user->refresh();

        return response()->json([
            'ok'          => true,
            'extra_pages' => $user->extra_pages,
            'extra_links' => $user->extra_links,
            'page_limit'  => $user->pageLimit(),
            'link_limit'  => $user->linkLimit(),
        ]);
    }

    // POST /billing/portal
    public function portal(Request $request)
    {
        $user = $request->user();

        if (!$user->stripe_customer_id) {
            return response()->json(['error' => 'No billing account found'], 404);
        }

        $session = PortalSession::create([
            'customer'   => $user->stripe_customer_id,
            'return_url' => config('app.frontend_url') . '/billing',
        ]);

        return response()->json(['url' => $session->url]);
    }

    // POST /billing/webhook  (public, no auth)
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sig     = $request->header('Stripe-Signature');
        $secret  = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sig, $secret);
        } catch (SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        match ($event->type) {
            'checkout.session.completed'          => $this->handleCheckoutCompleted($event->data->object),
            'customer.subscription.updated'       => $this->handleSubscriptionUpdated($event->data->object),
            'customer.subscription.deleted'       => $this->handleSubscriptionDeleted($event->data->object),
            'invoice.payment_failed'              => $this->handlePaymentFailed($event->data->object),
            default                               => null,
        };

        return response()->json(['ok' => true]);
    }

    private function handleCheckoutCompleted(object $session): void
    {
        $userId = $session->metadata->user_id ?? null;
        $plan   = $session->metadata->plan ?? 'pro';
        if (!$userId) return;

        $data = [
            'plan'                    => $plan,
            'stripe_subscription_id'  => $session->subscription,
            'plan_expires_at'         => null, // active subscription, no expiry
        ];

        if ($plan === 'agency') {
            // capacity sent in metadata: 25..800, or 0 = unlimited (∞)
            $data['agency_pages'] = isset($session->metadata->agency_pages) ? (int) $session->metadata->agency_pages : 25;
            $data['agency_links'] = isset($session->metadata->agency_links) ? (int) $session->metadata->agency_links : 25;
        }

        User::find($userId)?->update($data);
    }

    private function handleSubscriptionUpdated(object $subscription): void
    {
        $userId = $subscription->metadata->user_id ?? null;
        if (!$userId) return;

        $user = User::find($userId);
        if (!$user) return;

        $plan   = $subscription->metadata->plan ?? $user->plan ?? 'pro';
        $status = $subscription->status;

        if (in_array($status, ['active', 'trialing'])) {
            $update = [
                'plan'                   => $plan,
                'stripe_subscription_id' => $subscription->id,
                'plan_expires_at'        => null,
            ];

            if ($plan === 'agency') {
                // Derive capacities from the actual line items (handles portal-driven changes too).
                $caps = $this->agencyCapacitiesFromItems($subscription);
                $update['agency_pages'] = $caps['pages'];
                $update['agency_links'] = $caps['links'];
            } else {
                $extraPages = 0;
                $extraLinks = 0;
                $pricePages = config('services.stripe.price_extra_pages');
                $priceLinks = config('services.stripe.price_extra_links');

                foreach ($subscription->items?->data ?? [] as $item) {
                    if ($item->price->id === $pricePages) {
                        $extraPages = $item->quantity * 100;
                    } elseif ($item->price->id === $priceLinks) {
                        $extraLinks = $item->quantity * 100;
                    }
                }

                $update['extra_pages'] = $extraPages;
                $update['extra_links'] = $extraLinks;
            }

            $user->update($update);
        } elseif ($status === 'canceled') {
            // Ignore cancellation of a stale/orphan sub that isn't the user's current one.
            if ($user->stripe_subscription_id !== $subscription->id) return;

            $periodEnd = $subscription->current_period_end ?? null;
            $user->update([
                'plan'            => 'free',
                'plan_expires_at' => $periodEnd ? \Carbon\Carbon::createFromTimestamp($periodEnd) : now(),
                'extra_pages'     => 0,
                'extra_links'     => 0,
                'agency_pages'    => null,
                'agency_links'    => null,
            ]);
        }
    }

    private function handleSubscriptionDeleted(object $subscription): void
    {
        $userId = $subscription->metadata->user_id ?? null;
        if (!$userId) return;

        $user = User::find($userId);
        // Only downgrade if the deleted subscription is the user's CURRENT one.
        // Prevents a stale/orphan subscription cancellation from wiping an active plan.
        if (!$user || $user->stripe_subscription_id !== $subscription->id) return;

        $user->update([
            'plan'                   => 'free',
            'stripe_subscription_id' => null,
            'plan_expires_at'        => null,
            'agency_pages'           => null,
            'agency_links'           => null,
        ]);
    }

    /**
     * Reverse-map a subscription's line items to chosen Agency capacities.
     * Defaults to the base 25 included on each dimension; 0 = unlimited (∞).
     */
    private function agencyCapacitiesFromItems(object $subscription): array
    {
        $lookup = [];
        foreach (config('services.stripe.agency_tiers') as $dimension => $dimTiers) {
            foreach ($dimTiers as $cap => $priceId) {
                $lookup[$priceId] = ['dim' => $dimension, 'cap' => (int) $cap];
            }
        }

        $caps = ['pages' => 25, 'links' => 25]; // base included
        foreach ($subscription->items?->data ?? [] as $item) {
            $priceId = $item->price->id ?? null;
            if ($priceId && isset($lookup[$priceId])) {
                $caps[$lookup[$priceId]['dim']] = $lookup[$priceId]['cap'];
            }
        }

        return $caps;
    }

    private function handlePaymentFailed(object $invoice): void
    {
        // Could send email notification — handled by Stripe email for now
    }
}
