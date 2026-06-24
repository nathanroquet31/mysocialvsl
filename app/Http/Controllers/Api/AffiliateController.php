<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AffiliateController extends Controller
{
    // GET /affiliate — code, link, referrals and real earnings (affiliates only)
    public function show(Request $request)
    {
        $user = $request->user();

        // Affiliate program is invite-only (founder flags is_affiliate). Regular users
        // never get a code/link, so attribution can't be self-minted for commissions.
        if (! $user->is_affiliate) {
            return response()->json(['enrolled' => false]);
        }

        if (! $user->affiliate_code) {
            do {
                $code = strtoupper(Str::random(8));
            } while (User::where('affiliate_code', $code)->exists());
            $user->update(['affiliate_code' => $code]);
        }

        $referrals = User::where('referred_by', $user->id)->get();
        $paying = $referrals->whereNotNull('stripe_subscription_id')->count();

        // Real money, straight from recorded commissions (not an estimate).
        $commissions = $user->commissions();
        $totalEarned = (int) $commissions->sum('commission_cents');
        $pending     = (int) (clone $commissions)->where('status', 'pending')->sum('commission_cents');
        $paidOut     = (int) (clone $commissions)->where('status', 'paid')->sum('commission_cents');

        return response()->json([
            'enrolled'          => true,
            'affiliate_code'    => $user->affiliate_code,
            'affiliate_link'    => rtrim(config('app.frontend_url', config('app.url')), '/') . '/register?ref=' . $user->affiliate_code,
            'referrals_count'   => $referrals->count(),
            'paying_referrals'  => $paying,
            'rate'              => 0.20,
            'total_earned'      => round($totalEarned / 100, 2),
            'pending_payout'    => round($pending / 100, 2),
            'paid_out'          => round($paidOut / 100, 2),
        ]);
    }
}
