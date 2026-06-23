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
            'type'         => 'required|in:page_view,link_click,age_confirmed,video_play,video_progress,video_position,video_unmute,bot_blocked,time_on_page,heartbeat',
            'page_link_id' => 'nullable|integer|exists:page_links,id',
            'value'        => 'nullable|numeric|min:0',
            'referrer'     => 'nullable|string|max:500',
            'session_id'   => 'nullable|string|max:40',
        ]);

        $ua = $request->userAgent() ?? '';

        // Keep bots/crawlers/link-preview fetchers out of analytics entirely, so
        // views/plays/retention reflect real humans (what Plausible/Fathom do).
        if ($this->isBotUserAgent($ua)) {
            return response()->json(['ok' => true]);
        }

        $device = preg_match('/Mobile|Android|iPhone/i', $ua) ? 'mobile' : 'desktop';

        $country = null;
        try {
            $position = Location::get($request->ip());
            $country = $position ? $position->countryCode : null;
        } catch (\Throwable $e) {
            // Geo lookup is best-effort; never break analytics on it.
            // Catch Throwable (not Exception): location drivers can raise \TypeError/\Error.
        }

        // video_position carries the furthest second watched. We don't store it as
        // an event (that would append a row every second); we keep the MAX in place
        // on the session's presence row → exact per-second retention, 1 row/session.
        if ($request->type === 'video_position') {
            if ($request->session_id) {
                $this->touchPresence($page->id, $request->session_id, $country, $device, (int) $request->value);
            }
            return response()->json(['ok' => true]);
        }

        // Live presence: page_view / heartbeat keep the session's row alive so the
        // dashboard can show who's on the page right now and since when.
        if ($request->session_id && in_array($request->type, ['page_view', 'heartbeat'], true)) {
            $this->touchPresence($page->id, $request->session_id, $country, $device);
        }
        if ($request->type === 'heartbeat') {
            return response()->json(['ok' => true]);
        }

        AnalyticsEvent::create([
            'page_id'      => $page->id,
            'type'         => $request->type,
            'page_link_id' => $request->page_link_id,
            'country'      => $country,
            'device'       => $device,
            'value'        => $request->value,
            'referrer'     => $request->referrer ? parse_url($request->referrer, PHP_URL_HOST) : null,
            'session_id'   => $request->session_id,
        ]);

        return response()->json(['ok' => true]);
    }

    /** Known bots / crawlers / link-preview fetchers we never want in analytics. */
    private function isBotUserAgent(string $ua): bool
    {
        if ($ua === '') return true; // no UA = almost always a script/bot
        return (bool) preg_match(
            '/bot|crawl|spider|slurp|facebookexternalhit|whatsapp|telegrambot|discordbot|slackbot|twitterbot|linkedinbot|embedly|preview|headless|phantom|puppeteer|playwright|python-requests|curl|wget|axios|okhttp|go-http|java\/|libwww|scrapy|semrush|ahrefs|mj12|dotbot|bytespider|petalbot/i',
            $ua
        );
    }

    /**
     * Upsert the live-presence row for a visitor session. started_at is set once;
     * last_seen_at and max_second move forward (max_second only ever grows — the
     * furthest second the visit reached, for the retention curve).
     */
    private function touchPresence(int $pageId, string $sessionId, ?string $country, string $device, ?int $second = null): void
    {
        $now = Carbon::now();
        $row = DB::table('page_presence')
            ->where('page_id', $pageId)->where('session_id', $sessionId)->first();

        if ($row) {
            $update = ['last_seen_at' => $now, 'country' => $country, 'device' => $device];
            if ($second !== null && $second > $row->max_second) {
                $update['max_second'] = $second;
            }
            DB::table('page_presence')
                ->where('page_id', $pageId)->where('session_id', $sessionId)
                ->update($update);
        } else {
            DB::table('page_presence')->insert([
                'page_id'      => $pageId,
                'session_id'   => $sessionId,
                'country'      => $country,
                'device'       => $device,
                'max_second'   => max(0, (int) $second),
                'started_at'   => $now,
                'last_seen_at' => $now,
            ]);
        }
    }

    // GET /api/pages/{id}/analytics?period=7 — stats pour le dashboard manager
    public function stats(Request $request, string $id)
    {
        $page = $request->user()->pages()->findOrFail($id);

        $days = in_array((int) $request->query('period'), [7, 30, 90]) ? (int) $request->query('period') : 30;
        $from = Carbon::now()->subDays($days)->startOfDay();

        $events = $page->analytics()->where('created_at', '>=', $from);

        // Distinct visitors (sessions), not raw events — see visitCountExpr().
        $pageViews  = $this->countVisits((clone $events)->where('type', 'page_view'));
        $linkClicks = $this->countVisits((clone $events)->where('type', 'link_click'));
        $videoPlays = $this->countVisits((clone $events)->where('type', 'video_play'));

        // VSL milestones: % of video plays that reached each checkpoint
        $milestones = [];
        foreach ([25, 50, 75, 100] as $pct) {
            $count = $this->countVisits((clone $events)->where('type', 'video_progress')->where('value', $pct));
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
            'by_device'     => (clone $events)->selectRaw('device, ' . $this->visitCountExpr() . ' as total')
                                    ->groupBy('device')->pluck('total', 'device'),
            'by_link'       => (clone $events)->where('type', 'link_click')
                                    ->selectRaw('page_link_id, ' . $this->visitCountExpr() . ' as total')
                                    ->groupBy('page_link_id')->pluck('total', 'page_link_id'),
            'daily'         => (clone $events)->where('type', 'page_view')
                                    ->selectRaw($this->periodExpr('day') . ' as date, ' . $this->visitCountExpr() . ' as total')
                                    ->groupBy('date')
                                    ->orderBy('date')
                                    ->pluck('total', 'date'),
            'by_country'    => (clone $events)->whereNotNull('country')
                                    ->selectRaw('country, ' . $this->visitCountExpr() . ' as total')
                                    ->groupBy('country')
                                    ->orderByDesc('total')
                                    ->pluck('total', 'country'),
        ];

        $stats['ctr'] = $pageViews > 0
            ? round(($linkClicks / $pageViews) * 100, 1)
            : 0;

        $stats['by_referrer'] = (clone $events)->where('type', 'page_view')->whereNotNull('referrer')
            ->selectRaw('referrer, ' . $this->visitCountExpr() . ' as total')
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
                'clicks_30m'   => 0, 'top_country' => null, 'events' => [], 'visitors' => [],
            ]);
        }

        $linkFilter = array_filter((array) $request->query('link_ids', []));
        $pageIds    = $linkFilter ? $allIds->intersect($linkFilter) : $allIds;

        return response()->json($this->buildLive($pageIds, $user->isPaid()));
    }

    // GET /api/analytics/dashboard — aggregated cross-page view for the main dashboard
    public function dashboard(Request $request)
    {
        $user    = $request->user();
        $allIds  = $user->pages()->pluck('id');

        if ($allIds->isEmpty()) {
            return response()->json($this->emptyDashboard($user->isPaid()));
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

        // Real visitors, not raw events: distinct sessions. CTR then reads as the
        // honest "% of visitors who clicked", never >100% from multi-click visits.
        $pageViews   = $this->countVisits((clone $base)->where('type', 'page_view'));
        $linkClicks  = $this->countVisits((clone $base)->where('type', 'link_click'));
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
            'live'               => $this->buildLive($pageIds, $user->isPaid()),
            'per_link'           => $this->buildPerLink($pageIds, $from, $to, $country),
            'vsl'                => $this->buildVslEngagement($base, $pageViews),
            // Retention curve + detailed live presence are Pro/Agency features.
            'is_paid'            => $user->isPaid(),
            'retention'          => $user->isPaid() ? $this->buildRetention($pageIds, $from, $to, $country) : null,
            'pages'              => $user->pages()->get(['id', 'model_name', 'slug'])->toArray(),
        ]);
    }

    /** Hard cap on retention-curve length (seconds), to bound payload + chart size. */
    private const RETENTION_MAX_SECONDS = 600;

    /**
     * Per-second audience-retention curve, ONE SERIES PER VIDEO (page). It's a
     * survival curve: for each second s, the % of a video's viewing sessions that
     * watched at least up to s. Early seconds matter most — that's where you see
     * if the hook lands in the first 3s.
     *
     * Cheaply per-second: each visit's furthest second is kept in place on its
     * page_presence row (max_second), so one value per session reconstructs the
     * whole curve — no per-second event flood. Per-video on purpose (a 20s and a
     * 45s VSL can't share one curve). Capped to the 6 most-watched videos.
     */
    private function buildRetention($pageIds, $from, $to, ?string $country): array
    {
        $q = AnalyticsEvent::whereIn('page_id', $pageIds);
        if ($from)    $q->where('created_at', '>=', $from);
        if ($to)      $q->where('created_at', '<=', $to);
        if ($country) $q->where('country', $country);

        // Denominator per page: distinct sessions that actually started the video.
        $plays = (clone $q)->where('type', 'video_play')->whereNotNull('session_id')
            ->selectRaw('page_id, count(distinct session_id) as sessions')
            ->groupBy('page_id')->pluck('sessions', 'page_id');

        if ($plays->isEmpty()) {
            return ['pages' => []];
        }

        // Furthest second reached per session — read in place from presence rows
        // (one per session). Filtered by when the visit started + country.
        $pres = DB::table('page_presence')->whereIn('page_id', $pageIds)->where('max_second', '>', 0);
        if ($from)    $pres->where('started_at', '>=', $from);
        if ($to)      $pres->where('started_at', '<=', $to);
        if ($country) $pres->where('country', $country);
        $maxima = $pres->get(['page_id', 'max_second'])->groupBy('page_id');

        // Clicks per exact second watched (value = real seconds), per page.
        $clicks = (clone $q)->where('type', 'link_click')->whereNotNull('value')
            ->get(['page_id', 'value'])
            ->groupBy('page_id')
            ->map(fn ($rows) => $rows->groupBy(fn ($e) => (int) round($e->value))->map->count());

        $names = Page::whereIn('id', $plays->keys())->pluck('model_name', 'id');

        $series = [];
        foreach ($plays as $pid => $playCount) {
            $playCount = (int) $playCount;
            if ($playCount === 0) continue;

            $pageClicks = $clicks->get($pid, collect());

            // Histogram of furthest-second-reached, then a suffix sum turns it into
            // survival counts: survival[s] = sessions whose max >= s.
            $histo  = [];
            $maxSec = 0;
            foreach ($maxima->get($pid, collect()) as $row) {
                $m = min((int) $row->max_second, self::RETENTION_MAX_SECONDS);
                if ($m <= 0) continue;
                $histo[$m] = ($histo[$m] ?? 0) + 1;
                if ($m > $maxSec) $maxSec = $m;
            }

            // s = 0 is everyone who played (100%).
            $points  = [['sec' => 0, 'pct' => 100, 'sessions' => $playCount, 'clicks' => (int) $pageClicks->get(0, 0)]];
            $running = 0;
            $survival = [];
            for ($s = $maxSec; $s >= 1; $s--) {
                $running += $histo[$s] ?? 0;
                $survival[$s] = $running;
            }
            for ($s = 1; $s <= $maxSec; $s++) {
                $points[] = [
                    'sec'      => $s,
                    // Clamp: a session can have a max_second without its video_play
                    // event landing (beacon loss), which would push pct over 100.
                    'pct'      => min(100, round($survival[$s] / $playCount * 100, 1)),
                    'sessions' => min($survival[$s], $playCount),
                    'clicks'   => (int) $pageClicks->get($s, 0),
                ];
            }

            $series[] = [
                'id'     => $pid,
                'name'   => $names->get($pid) ?: 'Untitled',
                'plays'  => $playCount,
                'points' => $points,
            ];
        }

        // Most-watched videos first, capped so the chart stays readable.
        usort($series, fn ($a, $b) => $b['plays'] <=> $a['plays']);

        return ['pages' => array_slice($series, 0, 6)];
    }

    /**
     * VSL watch engagement aggregated across the filtered pages: play rate,
     * watch-depth milestones (% of plays reaching each), and average watch /
     * time-on-page. Drives the "VSL Watch Funnel" card.
     */
    private function buildVslEngagement($base, int $pageViews): array
    {
        $plays = $this->countVisits((clone $base)->where('type', 'video_play'));

        $milestones = [];
        foreach ([25, 50, 75, 100] as $pct) {
            $count = $this->countVisits((clone $base)->where('type', 'video_progress')->where('value', $pct));
            $milestones[$pct] = $plays > 0 ? round($count / $plays * 100, 1) : 0;
        }

        $avgWatch = (clone $base)->where('type', 'link_click')->whereNotNull('value')->avg('value');
        $avgTime  = (clone $base)->where('type', 'time_on_page')->whereNotNull('value')->avg('value');

        return [
            'plays'                  => $plays,
            'play_rate'              => $pageViews > 0 ? round($plays / $pageViews * 100, 1) : 0,
            'milestones'             => $milestones,
            'avg_watch_before_click' => $avgWatch ? round($avgWatch, 1) : null,
            'avg_time_on_page'       => $avgTime ? round($avgTime) : null,
        ];
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

        $visits = $this->visitCountExpr();

        $views = (clone $base)->where('type', 'page_view')
            ->selectRaw("$expr as period, $visits as total")
            ->groupBy('period')->orderBy('period')
            ->pluck('total', 'period');

        $clicks = (clone $base)->where('type', 'link_click')
            ->selectRaw("$expr as period, $visits as total")
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
            ->selectRaw('page_id, ' . $this->visitCountExpr() . ' as total')->groupBy('page_id')
            ->pluck('total', 'page_id');

        $clicks = (clone $q)->where('type', 'link_click')
            ->selectRaw('page_id, ' . $this->visitCountExpr() . ' as total')->groupBy('page_id')
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
            ->selectRaw('country, ' . $this->visitCountExpr() . ' as total')
            ->groupBy('country')->orderByDesc('total')
            ->pluck('total', 'country')->all();
    }

    private function buildByDevice($base): array
    {
        return (clone $base)
            ->selectRaw('device, ' . $this->visitCountExpr() . ' as total')
            ->groupBy('device')->pluck('total', 'device')->all();
    }

    private function buildByReferrer($base): array
    {
        return (clone $base)->where('type', 'page_view')->whereNotNull('referrer')
            ->selectRaw('referrer, ' . $this->visitCountExpr() . ' as total')
            ->groupBy('referrer')->orderByDesc('total')
            ->limit(10)->pluck('total', 'referrer')->all();
    }

    private function buildHourly($pageIds, $from, $to): array
    {
        $q = AnalyticsEvent::whereIn('page_id', $pageIds)->where('type', 'page_view');
        if ($from) $q->where('created_at', '>=', $from);
        if ($to)   $q->where('created_at', '<=', $to);

        $rows = $q->selectRaw($this->hourExpr() . ' as hour, ' . $this->visitCountExpr() . ' as total')
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

    /**
     * SQL counting REAL visits, not raw events: dedupe by session_id so one
     * visitor firing ~10 events (page_view, video_play, 4× progress, heartbeats,
     * clicks…) counts ONCE. That's the whole difference between "1700 events" and
     * "the ~45 real people who actually showed up". Legacy rows that predate
     * session tracking have no session_id, so they fall back to their row id and
     * still count once each — no data lost, just deduped where we can.
     */
    private function visitCountExpr(): string
    {
        $idAsText = in_array($this->dbDriver(), ['mysql', 'mariadb'], true)
            ? 'CAST(id AS CHAR)'
            : 'CAST(id AS TEXT)';
        return "COUNT(DISTINCT COALESCE(session_id, $idAsText))";
    }

    /** Run the distinct-visit count over a query. Clone the query first if reused. */
    private function countVisits($query): int
    {
        $row = $query->selectRaw($this->visitCountExpr() . ' as aggregate')->first();
        return (int) ($row->aggregate ?? 0);
    }

    private function buildLive($pageIds, bool $detailed = false): array
    {
        $cutoff = Carbon::now()->subMinutes(30);
        $q      = AnalyticsEvent::whereIn('page_id', $pageIds)
                    ->where('created_at', '>=', $cutoff);

        $views30  = $this->countVisits((clone $q)->where('type', 'page_view'));
        $clicks30 = $this->countVisits((clone $q)->where('type', 'link_click'));

        // "Visitors now" = distinct sessions whose heartbeat fired in the last
        // 30s (a real presence ping every ~12s on the open page), so it reflects
        // who is genuinely on a page right now — not page loads in a 5-min window.
        $liveCutoff = Carbon::now()->subSeconds(30);
        $presence   = DB::table('page_presence')->whereIn('page_id', $pageIds)
            ->where('last_seen_at', '>=', $liveCutoff);

        $visitorsNow = (clone $presence)->distinct('session_id')->count('session_id');

        // Detailed live visitors: country, device, and how long they've been on
        // the page (drives the "who's watching right now" list, ligne.me-style).
        // The list itself is a Pro/Agency feature; the raw count stays visible.
        $now      = Carbon::now();
        $visitors = $detailed
            ? (clone $presence)->orderByDesc('started_at')->limit(30)
                ->get(['session_id', 'country', 'device', 'started_at'])
                ->map(fn ($v) => [
                    'country' => $v->country,
                    'device'  => $v->device,
                    'seconds' => (int) Carbon::parse($v->started_at)->diffInSeconds($now),
                ])->all()
            : [];

        $topCountry = (clone $q)->whereNotNull('country')
            ->selectRaw('country, ' . $this->visitCountExpr() . ' as total')
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
            'visitors'     => $visitors,
        ];
    }

    private function buildPerLink($pageIds, $from, $to, ?string $country): array
    {
        $q = AnalyticsEvent::whereIn('page_id', $pageIds);
        if ($from)    $q->where('created_at', '>=', $from);
        if ($to)      $q->where('created_at', '<=', $to);
        if ($country) $q->where('country', $country);

        $views  = (clone $q)->where('type', 'page_view')
            ->selectRaw('page_id, ' . $this->visitCountExpr() . ' as total')->groupBy('page_id')
            ->pluck('total', 'page_id');
        $clicks = (clone $q)->where('type', 'link_click')
            ->selectRaw('page_id, ' . $this->visitCountExpr() . ' as total')->groupBy('page_id')
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

    private function emptyDashboard(bool $isPaid = false): array
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
            'live'               => ['visitors_now' => 0, 'views_30m' => 0, 'clicks_30m' => 0, 'top_country' => null, 'events' => [], 'visitors' => []],
            'per_link'           => [],
            'vsl'                => ['plays' => 0, 'play_rate' => 0, 'milestones' => [25 => 0, 50 => 0, 75 => 0, 100 => 0], 'avg_watch_before_click' => null, 'avg_time_on_page' => null],
            'is_paid'            => $isPaid,
            'retention'          => null,
            'pages'              => [],
        ];
    }
}
