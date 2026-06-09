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

    // POST /billing/checkout  { plan: 'pro' | 'agency' }
    public function checkout(Request $request)
    {
        $request->validate(['plan' => 'required|in:pro,agency']);

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

        $priceId = $plan === 'agency'
            ? config('services.stripe.price_agency')
            : config('services.stripe.price_pro');

        $session = Session::create([
            'customer'            => $user->stripe_customer_id,
            'mode'                => 'subscription',
            'payment_method_types' => ['card'],
            'line_items'          => [['price' => $priceId, 'quantity' => 1]],
            'success_url'         => env('FRONTEND_URL', 'http://localhost:5173') . '/dashboard?upgraded=1',
            'cancel_url'          => env('FRONTEND_URL', 'http://localhost:5173') . '/billing',
            'metadata'            => ['user_id' => $user->id, 'plan' => $plan],
            'subscription_data'   => ['metadata' => ['user_id' => $user->id, 'plan' => $plan]],
            'allow_promotion_codes' => true,
        ]);

        return response()->json(['url' => $session->url]);
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
            'return_url' => env('FRONTEND_URL', 'http://localhost:5173') . '/billing',
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

        User::find($userId)?->update([
            'plan'                    => $plan,
            'stripe_subscription_id'  => $session->subscription,
            'plan_expires_at'         => null, // active subscription, no expiry
        ]);
    }

    private function handleSubscriptionUpdated(object $subscription): void
    {
        $userId = $subscription->metadata->user_id ?? null;
        if (!$userId) return;

        $plan   = $subscription->metadata->plan ?? 'pro';
        $status = $subscription->status;

        if (in_array($status, ['active', 'trialing'])) {
            User::find($userId)?->update([
                'plan'                   => $plan,
                'stripe_subscription_id' => $subscription->id,
                'plan_expires_at'        => null,
            ]);
        } elseif ($status === 'canceled') {
            $periodEnd = $subscription->current_period_end ?? null;
            User::find($userId)?->update([
                'plan'           => 'free',
                'plan_expires_at'=> $periodEnd ? \Carbon\Carbon::createFromTimestamp($periodEnd) : now(),
            ]);
        }
    }

    private function handleSubscriptionDeleted(object $subscription): void
    {
        $userId = $subscription->metadata->user_id ?? null;
        if (!$userId) return;

        User::find($userId)?->update([
            'plan'                   => 'free',
            'stripe_subscription_id' => null,
            'plan_expires_at'        => null,
        ]);
    }

    private function handlePaymentFailed(object $invoice): void
    {
        // Could send email notification — handled by Stripe email for now
    }
}
