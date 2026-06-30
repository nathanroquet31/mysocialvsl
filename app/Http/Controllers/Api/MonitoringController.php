<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MonitoringDigest;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    /**
     * Read-only business digest for the daily monitoring routine (Telegram).
     *
     * Public route, but gated by a shared secret (MONITORING_TOKEN) — the admin
     * metrics endpoint needs an authenticated session a cron agent can't hold, so
     * this is a purpose-built, read-only, token-guarded snapshot. Never writes.
     */
    public function digest(Request $request, MonitoringDigest $digest)
    {
        $expected = config('services.monitoring.token');
        $given    = (string) ($request->query('token') ?? $request->bearerToken() ?? '');

        if (! $expected || ! hash_equals($expected, $given)) {
            abort(403);
        }

        return response()->json($digest->snapshot());
    }
}
