<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

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

        $users = User::query()
            ->when($search !== '', fn ($q) => $q->where('email', 'like', "%{$search}%"))
            ->orderByDesc('id')
            ->paginate(25)
            ->through(fn (User $u) => $this->row($u));

        return response()->json($users);
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

        return response()->json($this->row($user));
    }

    /** Shape a user for the admin table — only the ops-relevant fields. */
    private function row(User $u): array
    {
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
        ];
    }
}
