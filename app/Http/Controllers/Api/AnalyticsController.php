<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsEvent;
use App\Models\Page;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Stevebauman\Location\Facades\Location;

class AnalyticsController extends Controller
{
    // POST /p/{slug}/event — records an event (public, called by the fan page)
    public function track(Request $request, string $slug)
    {
        $page = Page::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $request->validate([
            'type'         => 'required|in:page_view,link_click,age_confirmed,video_play,video_progress,video_unmute,bot_blocked,time_on_page',
            'page_link_id' => 'nullable|integer|exists:page_links,id',
            'value'        => 'nullable|numeric|min:0',
            'referrer'     => 'nullable|string|max:500',
        ]);

        $ua = $request->userAgent() ?? '';
        $device = preg_match('/Mobile|Android|iPhone/i', $ua) ? 'mobile' : 'desktop';

        $country = null;
        try {
            $position = Location::get($request->ip());
            $country = $position ? $position->countryCode : null;
        } catch (\Throwable $e) {
            // Geo lookup is best-effort; never break analytics on it.
            // Catch Throwable (not Exception): location drivers can raise \TypeError/\Error.
        }

        AnalyticsEvent::create([
            'page_id'      => $page->id,
            'type'         => $request->type,
            'page_link_id' => $request->page_link_id,
            'country'      => $country,
            'device'       => $device,
            'value'        => $request->value,
            'referrer'     => $request->referrer ? parse_url($request->referrer, PHP_URL_HOST) : null,
        ]);

        return response()->json(['ok' => true]);
    }

    // GET /api/pages/{id}/analytics?period=7 — stats pour le dashboard manager
    public function stats(Request $request, string $id)
    {
        $page = $request->user()->pages()->findOrFail($id);

        $days = in_array((int) $request->query('period'), [7, 30, 90]) ? (int) $request->query('period') : 30;
        $from = Carbon::now()->subDays($days)->startOfDay();

        $events = $page->analytics()->where('created_at', '>=', $from);

        $pageViews  = (clone $events)->where('type', 'page_view')->count();
        $linkClicks = (clone $events)->where('type', 'link_click')->count();
        $videoPlays = (clone $events)->where('type', 'video_play')->count();

        // VSL milestones: % of video plays that reached each checkpoint
        $milestones = [];
        foreach ([25, 50, 75, 100] as $pct) {
            $count = (clone $events)->where('type', 'video_progress')->where('value', $pct)->count();
            $milestones[$pct] = $videoPlays > 0 ? round(($count / $videoPlays) * 100, 1) : 0;
        }

        // Avg watch seconds before link click
        $avgWatch = (clone $events)->where('type', 'link_click')->whereNotNull('value')->avg('value');

        $stats = [
            'period'        => $days,
            'page_views'    => $pageViews,
            'age_confirmed' => (clone $events)->where('type', 'age_confirmed')->count(),
            'link_clicks'   => $linkClicks,
            'video_plays'   => $videoPlays,
            'play_rate'     => $pageViews > 0 ? round(($videoPlays / $pageViews) * 100, 1) : 0,
            'milestones'    => $milestones,
            'avg_watch_before_click' => $avgWatch ? round($avgWatch, 1) : null,
            'by_device'     => (clone $events)->selectRaw('device, count(*) as total')
                                    ->groupBy('device')->pluck('total', 'device'),
            'by_link'       => (clone $events)->where('type', 'link_click')
                                    ->selectRaw('page_link_id, count(*) as total')
                                    ->groupBy('page_link_id')->pluck('total', 'page_link_id'),
            'daily'         => (clone $events)->where('type', 'page_view')
                                    ->selectRaw($this->periodExpr('day') . ' as date, count(*) as total')
                                    ->groupBy('date')
                                    ->orderBy('date')
                                    ->pluck('total', 'date'),
            'by_country'    => (clone $events)->whereNotNull('country')
                                    ->selectRaw('country, count(*) as total')
                                    ->groupBy('country')
                                    ->orderByDesc('total')
                                    ->pluck('total', 'country'),
        ];

        $stats['ctr'] = $pageViews > 0
            ? round(($linkClicks / $pageViews) * 100, 1)
            : 0;

        $stats['by_referrer'] = (clone $events)->where('type', 'page_view')->whereNotNull('referrer')
            ->selectRaw('referrer, count(*) as total')
            ->groupBy('referrer')->orderByDesc('total')
            ->pluck('total', 'referrer');

        $avgTime = (clone $events)->where('type', 'time_on_page')->whereNotNull('value')->avg('value');
        $stats['avg_time_on_page'] = $avgTime ? round($avgTime) : null;

        return response()->json($stats);
    }

    // GET /api/analytics/live — real-time data only (poll 8s)
    public function live(Request $request)
    {
        $user    = $request->user();
        $allIds  = $user->pages()->pluck('id');

        if ($allIds->isEmpty()) {
            return response()->json([
                'visitors_now' => 0, 'views_30m' => 0,
                'clicks_30m'   => 0, 'top_country' => null, 'events' => [],
            ]);
        }

        $linkFilter = array_filter((array) $request->query('link_ids', []));
        $pageIds    = $linkFilter ? $allIds->intersect($linkFilter) : $allIds;

        return response()->json($this->buildLive($pageIds));
    }

    // GET /api/analytics/dashboard — aggregated cross-page view for the main dashboard
    public function dashboard(Request $request)
    {
        $user    = $request->user();
        $allIds  = $user->pages()->pluck('id');

        if ($allIds->isEmpty()) {
            return response()->json($this->emptyDashboard());
        }

        $preset      = $request->query('preset', '30d');
        $customStart = $request->query('custom_start');
        $customEnd   = $request->query('custom_end');
        [$from, $to] = $this->resolveRange($preset, $customStart, $customEnd);

        // Optional: filter to specific pages
        $linkFilter = array_filter((array) $request->query('link_ids', []));
        $pageIds    = $linkFilter
            ? $allIds->intersect($linkFilter)
            : $allIds;

        $country = $request->query('country');

        $base = AnalyticsEvent::whereIn('page_id', $pageIds);
        if ($from) $base->where('created_at', '>=', $from);
        if ($to)   $base->where('created_at', '<=', $to);
        if ($country) $base->where('country', $country);

        $pageViews   = (clone $base)->where('type', 'page_view')->count();
        $linkClicks  = (clone $base)->where('type', 'link_click')->count();
        $ctr         = $pageViews > 0 ? round($linkClicks / $pageViews * 100, 1) : 0;

        return response()->json([
            'page_views'         => $pageViews,
            'visits_with_clicks' => $linkClicks,
            'ctr'                => $ctr,
            'series'             => $this->buildDailySeries($base, $from, $to, $preset),
            'top_links'          => $this->buildTopLinks($pageIds, $from, $to, $country),
            'by_country'         => $this->buildByCountry($base),
            'by_device'          => $this->buildByDevice($base),
            'by_referrer'        => $this->buildByReferrer($base),
            'hourly'             => $this->buildHourly($pageIds, $from, $to),
            'live'               => $this->buildLive($pageIds),
            'per_link'           => $this->buildPerLink($pageIds, $from, $to, $country),
            'pages'              => $user->pages()->get(['id', 'model_name', 'slug'])->toArray(),
        ]);
    }

    // ─── Helpers ────────────────────────────────────────────────────────────

    private function resolveRange(string $preset, ?string $customStart, ?string $customEnd): array
    {
        $now = Carbon::now();
        return match ($preset) {
            'today'     => [$now->copy()->startOfDay(), $now],
            'yesterday' => [$now->copy()->subDay()->startOfDay(), $now->copy()->subDay()->endOfDay()],
            '24h'       => [$now->copy()->subHours(24), $now],
            '7d'        => [$now->copy()->subDays(7)->startOfDay(), $now],
            '30d'       => [$now->copy()->subDays(30)->startOfDay(), $now],
            '90d'       => [$now->copy()->subDays(90)->startOfDay(), $now],
            '6m'        => [$now->copy()->subMonths(6)->startOfDay(), $now],
            '12m'       => [$now->copy()->subMonths(12)->startOfDay(), $now],
            'all'       => [null, null],
            'custom'    => [
                $customStart ? Carbon::parse($customStart)->startOfDay() : null,
                $customEnd   ? Carbon::parse($customEnd)->endOfDay()     : null,
            ],
            default     => [$now->copy()->subDays(30)->startOfDay(), $now],
        };
    }

    private function buildDailySeries($base, $from, $to, string $preset): array
    {
        $granularity = match ($preset) {
            'today', '24h' => 'hour',
            '7d', '30d'    => 'day',
            '90d'          => 'week',
            default        => 'month',
        };

        $expr = $this->periodExpr($granularity);

        $views = (clone $base)->where('type', 'page_view')
            ->selectRaw("$expr as period, count(*) as total")
            ->groupBy('period')->orderBy('period')
            ->pluck('total', 'period');

        $clicks = (clone $base)->where('type', 'link_click')
            ->selectRaw("$expr as period, count(*) as total")
            ->groupBy('period')->orderBy('period')
            ->pluck('total', 'period');

        $labels = $views->keys()->merge($clicks->keys())->unique()->sort()->values();

        return [
            'labels' => $labels->all(),
            'views'  => $labels->map(fn ($l) => $views->get($l, 0))->all(),
            'clicks' => $labels->map(fn ($l) => $clicks->get($l, 0))->all(),
        ];
    }

    private function buildTopLinks($pageIds, $from, $to, ?string $country): array
    {
        $q = AnalyticsEvent::whereIn('page_id', $pageIds);
        if ($from)    $q->where('created_at', '>=', $from);
        if ($to)      $q->where('created_at', '<=', $to);
        if ($country) $q->where('country', $country);

        $views  = (clone $q)->where('type', 'page_view')
            ->selectRaw('page_id, count(*) as total')->groupBy('page_id')
            ->pluck('total', 'page_id');

        $clicks = (clone $q)->where('type', 'link_click')
            ->selectRaw('page_id, count(*) as total')->groupBy('page_id')
            ->pluck('total', 'page_id');

        return Page::whereIn('id', $pageIds)->get(['id', 'model_name', 'slug'])
            ->map(function ($p) use ($views, $clicks) {
                $v = $views->get($p->id, 0);
                $c = $clicks->get($p->id, 0);
                return ['id' => $p->id, 'name' => $p->model_name, 'slug' => $p->slug,
                        'views' => $v, 'clicks' => $c,
                        'ctr'   => $v > 0 ? round($c / $v * 100, 1) : 0];
            })->sortByDesc('clicks')->take(10)->values()->all();
    }

    private function buildByCountry($base): array
    {
        return (clone $base)->whereNotNull('country')
            ->selectRaw('country, count(*) as total')
            ->groupBy('country')->orderByDesc('total')
            ->pluck('total', 'country')->all();
    }

    private function buildByDevice($base): array
    {
        return (clone $base)
            ->selectRaw('device, count(*) as total')
            ->groupBy('device')->pluck('total', 'device')->all();
    }

    private function buildByReferrer($base): array
    {
        return (clone $base)->where('type', 'page_view')->whereNotNull('referrer')
            ->selectRaw('referrer, count(*) as total')
            ->groupBy('referrer')->orderByDesc('total')
            ->limit(10)->pluck('total', 'referrer')->all();
    }

    private function buildHourly($pageIds, $from, $to): array
    {
        $q = AnalyticsEvent::whereIn('page_id', $pageIds)->where('type', 'page_view');
        if ($from) $q->where('created_at', '>=', $from);
        if ($to)   $q->where('created_at', '<=', $to);

        $rows = $q->selectRaw($this->hourExpr() . ' as hour, count(*) as total')
            ->groupBy('hour')->pluck('total', 'hour');

        // Normalize keys to int (drivers return '00'..'23' or 0..23) so the lookup hits.
        $byHour = [];
        foreach ($rows as $hour => $total) {
            $byHour[(int) $hour] = $total;
        }

        return array_map(fn ($h) => $byHour[$h] ?? 0, range(0, 23));
    }

    // ─── Cross-driver SQL helpers (SQLite local, Postgres prod, MySQL fallback) ──

    private function dbDriver(): string
    {
        return DB::connection()->getDriverName();
    }

    /** SQL expression formatting created_at into a sortable period-bucket string. */
    private function periodExpr(string $granularity): string
    {
        return match ($this->dbDriver()) {
            'sqlite' => match ($granularity) {
                'hour'  => "strftime('%Y-%m-%d %H:00', created_at)",
                'day'   => "strftime('%Y-%m-%d', created_at)",
                'week'  => "strftime('%Y-W%W', created_at)",
                'month' => "strftime('%Y-%m', created_at)",
            },
            'pgsql' => match ($granularity) {
                'hour'  => "to_char(created_at, 'YYYY-MM-DD HH24\":00\"')",
                'day'   => "to_char(created_at, 'YYYY-MM-DD')",
                'week'  => "to_char(created_at, 'IYYY\"-W\"IW')",
                'month' => "to_char(created_at, 'YYYY-MM')",
            },
            default => match ($granularity) { // mysql / mariadb
                'hour'  => "DATE_FORMAT(created_at, '%Y-%m-%d %H:00')",
                'day'   => "DATE_FORMAT(created_at, '%Y-%m-%d')",
                'week'  => "DATE_FORMAT(created_at, '%x-W%v')",
                'month' => "DATE_FORMAT(created_at, '%Y-%m')",
            },
        };
    }

    /** SQL expression extracting the hour-of-day (0..23) from created_at. */
    private function hourExpr(): string
    {
        return match ($this->dbDriver()) {
            'sqlite' => "CAST(strftime('%H', created_at) AS INTEGER)",
            'pgsql'  => 'EXTRACT(HOUR FROM created_at)',
            default  => 'HOUR(created_at)',
        };
    }

    private function buildLive($pageIds): array
    {
        $cutoff = Carbon::now()->subMinutes(30);
        $q      = AnalyticsEvent::whereIn('page_id', $pageIds)
                    ->where('created_at', '>=', $cutoff);

        $views30  = (clone $q)->where('type', 'page_view')->count();
        $clicks30 = (clone $q)->where('type', 'link_click')->count();

        $visitorsNow = AnalyticsEvent::whereIn('page_id', $pageIds)
            ->where('created_at', '>=', Carbon::now()->subMinutes(5))->count();

        $topCountry = (clone $q)->whereNotNull('country')
            ->selectRaw('country, count(*) as total')
            ->groupBy('country')->orderByDesc('total')->value('country');

        $recentEvents = (clone $q)->orderByDesc('created_at')->limit(20)
            ->get(['type', 'country', 'device', 'page_id', 'created_at'])
            ->map(fn ($e) => [
                'type'       => $e->type,
                'country'    => $e->country,
                'device'     => $e->device,
                'page_id'    => $e->page_id,
                'created_at' => $e->created_at,
            ])->all();

        return [
            'visitors_now' => $visitorsNow,
            'views_30m'    => $views30,
            'clicks_30m'   => $clicks30,
            'top_country'  => $topCountry,
            'events'       => $recentEvents,
        ];
    }

    private function buildPerLink($pageIds, $from, $to, ?string $country): array
    {
        $q = AnalyticsEvent::whereIn('page_id', $pageIds);
        if ($from)    $q->where('created_at', '>=', $from);
        if ($to)      $q->where('created_at', '<=', $to);
        if ($country) $q->where('country', $country);

        $views  = (clone $q)->where('type', 'page_view')
            ->selectRaw('page_id, count(*) as total')->groupBy('page_id')
            ->pluck('total', 'page_id');
        $clicks = (clone $q)->where('type', 'link_click')
            ->selectRaw('page_id, count(*) as total')->groupBy('page_id')
            ->pluck('total', 'page_id');

        return Page::whereIn('id', $pageIds)->get(['id', 'model_name', 'slug', 'group_name'])
            ->map(function ($p) use ($views, $clicks) {
                $v = $views->get($p->id, 0);
                $c = $clicks->get($p->id, 0);
                return ['id' => $p->id, 'name' => $p->model_name, 'slug' => $p->slug,
                        'group' => $p->group_name, 'views' => $v, 'clicks' => $c,
                        'ctr'   => $v > 0 ? round($c / $v * 100, 1) : 0];
            })->sortByDesc('views')->values()->all();
    }

    private function emptyDashboard(): array
    {
        return [
            'page_views'         => 0,
            'visits_with_clicks' => 0,
            'ctr'                => 0,
            'series'             => ['labels' => [], 'views' => [], 'clicks' => []],
            'top_links'          => [],
            'by_country'         => [],
            'by_device'          => [],
            'by_referrer'        => [],
            'hourly'             => array_fill(0, 24, 0),
            'live'               => ['visitors_now' => 0, 'views_30m' => 0, 'clicks_30m' => 0, 'top_country' => null, 'events' => []],
            'per_link'           => [],
            'pages'              => [],
        ];
    }
}
