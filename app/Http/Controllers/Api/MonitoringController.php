<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsEvent;
use App\Models\Page;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonitoringController extends Controller
{
    /**
     * Read-only business digest for the daily monitoring routine (Telegram).
     *
     * Public route, but gated by a shared secret (MONITORING_TOKEN) — the admin
     * metrics endpoint needs an authenticated session a cron agent can't hold, so
     * this is a purpose-built, read-only, token-guarded snapshot. Never writes.
     */
    public function digest(Request $request)
    {
        $expected = config('services.monitoring.token');
        $given    = (string) ($request->query('token') ?? $request->bearerToken() ?? '');

        if (! $expected || ! hash_equals($expected, $given)) {
            abort(403);
        }

        $now    = now();
        $dayAgo = $now->copy()->subDay();

        // Distinct visitors/clicks in the last 24h (session-level, consistent with
        // the dashboard's "real people not raw events" philosophy).
        $visits24h = (int) AnalyticsEvent::where('type', 'page_view')
            ->where('created_at', '>=', $dayAgo)
            ->distinct()->count('session_id');
        $clicks24h = (int) AnalyticsEvent::where('type', 'link_click')
            ->where('created_at', '>=', $dayAgo)
            ->distinct()->count('session_id');

        $byPlan = User::selectRaw('plan, count(*) as cnt')->groupBy('plan')->pluck('cnt', 'plan');

        return response()->json([
            // Growth
            'signups_24h'         => User::where('created_at', '>=', $dayAgo)->count(),
            'total_users'         => User::count(),
            'pages_created_24h'   => Page::where('created_at', '>=', $dayAgo)->count(),
            // Revenue-ish (paying = live Stripe subscription; trials never create one)
            'paying_users'        => User::whereNotNull('stripe_subscription_id')->count(),
            'by_plan'             => [
                'free'   => (int) ($byPlan['free'] ?? 0),
                'pro'    => (int) ($byPlan['pro'] ?? 0),
                'agency' => (int) ($byPlan['agency'] ?? 0),
            ],
            'active_trials'       => User::whereNotNull('trial_ends_at')
                ->whereNull('stripe_subscription_id')
                ->where('trial_ends_at', '>', $now)->count(),
            'trials_expiring_48h' => User::whereNull('stripe_subscription_id')
                ->whereBetween('trial_ends_at', [$now, $now->copy()->addHours(48)])->count(),
            // Traffic (last 24h, distinct visitors)
            'visits_24h'          => $visits24h,
            'clicks_24h'          => $clicks24h,
            'generated_at'        => $now->toIso8601String(),
        ]);
    }
}
