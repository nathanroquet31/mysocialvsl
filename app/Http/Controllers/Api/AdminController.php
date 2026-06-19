<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsEvent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Founder/admin ops surface. Every route here sits behind auth:sanctum + the
 * `admin` middleware (is_admin), so no per-action authorization is repeated.
 *
 * Replaces the manual `tinker` workflow for flagging coaches as beta partners
 * and gives at-a-glance visibility on users, trials and conversions.
 */
class AdminController extends Controller
{
    /** Paginated user list with the fields that matter for beta ops, newest first. */
    public function users(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $paginator = User::query()
            ->when($search !== '', fn ($q) => $q->where('email', 'like', "%{$search}%"))
            ->withCount('pages')
            ->orderByDesc('id')
            ->paginate(25);

        $activity = $this->activityFor($paginator->getCollection()->pluck('id'));

        $paginator->setCollection(
            $paginator->getCollection()->map(fn (User $u) => $this->row($u, $activity[$u->id] ?? null))
        );

        return response()->json($paginator);
    }

    /**
     * Aggregate page views + link clicks (and last activity) across each user's
     * pages, in a single query. Keyed by user_id.
     *
     * @return array<int, array{views:int, clicks:int, last_event:?string}>
     */
    private function activityFor(Collection $userIds): array
    {
        if ($userIds->isEmpty()) {
            return [];
        }

        return AnalyticsEvent::query()
            ->join('pages', 'analytics_events.page_id', '=', 'pages.id')
            ->whereIn('pages.user_id', $userIds)
            ->selectRaw(
                'pages.user_id as user_id, '
                . "sum(case when analytics_events.type = 'page_view'  then 1 else 0 end) as views, "
                . "sum(case when analytics_events.type = 'link_click' then 1 else 0 end) as clicks, "
                . 'max(analytics_events.created_at) as last_event'
            )
            ->groupBy('pages.user_id')
            ->get()
            ->keyBy('user_id')
            ->map(fn ($r) => [
                'views'      => (int) $r->views,
                'clicks'     => (int) $r->clicks,
                'last_event' => $r->last_event,
            ])
            ->all();
    }

    /** Headline counts for the dashboard cards. */
    public function metrics()
    {
        // A paying user has a live Stripe subscription (trials never create one).
        $paying = User::whereNotNull('stripe_subscription_id')->count();

        // Active trials: deadline in the future and not yet converted to Stripe.
        $activeTrials = User::whereNotNull('trial_ends_at')
            ->whereNull('stripe_subscription_id')
            ->where('trial_ends_at', '>', now())
            ->count();

        $byPlan = User::selectRaw('plan, count(*) as cnt')
            ->groupBy('plan')
            ->pluck('cnt', 'plan');

        return response()->json([
            'total_users'    => User::count(),
            'active_trials'  => $activeTrials,
            'paying_users'   => $paying,
            'beta_partners'  => User::where('is_beta_partner', true)->count(),
            'by_plan'        => [
                'free'   => (int) ($byPlan['free'] ?? 0),
                'pro'    => (int) ($byPlan['pro'] ?? 0),
                'agency' => (int) ($byPlan['agency'] ?? 0),
            ],
        ]);
    }

    /**
     * Flag/unflag a user as a beta partner (coach). is_beta_partner is non-fillable
     * by design (anti self-mint), so we set it explicitly here — only reachable by
     * an admin. This is the action that used to require `tinker`.
     */
    public function toggleBetaPartner(Request $request, User $user)
    {
        $data = $request->validate([
            'is_beta_partner' => 'required|boolean',
        ]);

        $user->forceFill(['is_beta_partner' => $data['is_beta_partner']])->save();

        $user->loadCount('pages');

        return response()->json(
            $this->row($user, $this->activityFor(collect([$user->id]))[$user->id] ?? null)
        );
    }

    /** Shape a user for the admin table — ops fields + page/traffic activity. */
    private function row(User $u, ?array $activity = null): array
    {
        $views  = $activity['views'] ?? 0;
        $clicks = $activity['clicks'] ?? 0;

        return [
            'id'              => $u->id,
            'name'            => $u->name,
            'email'           => $u->email,
            'plan'            => $u->plan,
            'on_trial'        => $u->onTrial(),
            'trial_days_left' => $u->trialDaysLeft(),
            'trial_ends_at'   => $u->trial_ends_at,
            'is_beta'         => (bool) $u->is_beta,
            'is_beta_partner' => (bool) $u->is_beta_partner,
            'is_admin'        => (bool) $u->is_admin,
            'affiliate_code'  => $u->affiliate_code,
            'referred_by'     => $u->referred_by,
            'created_at'      => $u->created_at,
            // Activation / activity
            'pages_count'     => (int) ($u->pages_count ?? 0),
            'views'           => $views,
            'clicks'          => $clicks,
            'ctr'             => $views > 0 ? round($clicks / $views * 100, 1) : 0,
            'last_active'     => $activity['last_event'] ?? null,
        ];
    }
}
