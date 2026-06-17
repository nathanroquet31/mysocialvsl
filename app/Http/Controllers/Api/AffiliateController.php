<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AffiliateController extends Controller
{
    // GET /affiliate — code, lien, referrals et estimation
    public function show(Request $request)
    {
        $user = $request->user();

        if (! $user->affiliate_code) {
            do {
                $code = strtoupper(Str::random(8));
            } while (User::where('affiliate_code', $code)->exists());
            $user->update(['affiliate_code' => $code]);
        }

        $referrals = User::where('referred_by', $user->id)->get();
        $paying = $referrals->whereNotNull('stripe_subscription_id')->count();

        return response()->json([
            'affiliate_code'    => $user->affiliate_code,
            'affiliate_link'    => rtrim(config('app.frontend_url', config('app.url')), '/') . '/register?ref=' . $user->affiliate_code,
            'referrals_count'   => $referrals->count(),
            'paying_referrals'  => $paying,
            // 20% récurrent sur le plan Pro (12€/mois) — estimation simple
            'estimated_monthly_revenue' => round($paying * 12 * 0.20, 2),
            'payout_status'     => 'none',
        ]);
    }
}
