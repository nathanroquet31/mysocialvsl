<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MonitoringDigest;
use App\Services\NetworkAnalytics;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Read-only, token-guarded snapshots for the founder's own tooling.
 *
 * Public routes gated by a shared secret (MONITORING_TOKEN) — the admin
 * endpoints need an authenticated session that a cron agent (or a Claude Code
 * session pulling data from the terminal) can't hold, so these are purpose-built
 * machine-readable twins. Never write.
 */
class MonitoringController extends Controller
{
    /** Business digest for the daily monitoring routine (Telegram). */
    public function digest(Request $request, MonitoringDigest $digest)
    {
        $this->assertToken($request);

        return response()->json($digest->snapshot());
    }

    /**
     * Cross-agency network analytics — the machine-readable twin of the admin
     * dashboard, so the founder can pull his live prod numbers into a Claude
     * Code session (via his own subscription, no external API key) for analysis.
     * Range via ?preset= (today/7d/30d/90d/12m/all; default 30d).
     */
    public function network(Request $request, NetworkAnalytics $engine)
    {
        $this->assertToken($request);

        [$from, $to] = $this->range((string) $request->query('preset', '30d'));

        return response()->json($engine->snapshot($from, $to));
    }

    /** Shared-secret gate (MONITORING_TOKEN). Aborts 403 when the token is missing/wrong. */
    private function assertToken(Request $request): void
    {
        $expected = config('services.monitoring.token');
        $given    = (string) ($request->query('token') ?? $request->bearerToken() ?? '');

        if (! $expected || ! hash_equals($expected, $given)) {
            abort(403);
        }
    }

    /** Resolve a preset key to a [from, to] window (null,null = all-time). */
    private function range(string $preset): array
    {
        $now = Carbon::now();
        return match ($preset) {
            'today' => [$now->copy()->startOfDay(), $now],
            '7d'    => [$now->copy()->subDays(7)->startOfDay(), $now],
            '30d'   => [$now->copy()->subDays(30)->startOfDay(), $now],
            '90d'   => [$now->copy()->subDays(90)->startOfDay(), $now],
            '12m'   => [$now->copy()->subMonths(12)->startOfDay(), $now],
            'all'   => [null, null],
            default => [$now->copy()->subDays(30)->startOfDay(), $now],
        };
    }
}
