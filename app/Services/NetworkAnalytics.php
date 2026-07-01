<?php

namespace App\Services;

use App\Models\AnalyticsEvent;
use App\Models\Page;
use App\Models\User;
use App\Support\AnalyticsSql;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Network-wide (cross-agency) analytics engine — the founder-facing counterpart
 * to the per-manager AnalyticsController. It aggregates the ENTIRE platform:
 *
 *  - overview volume (pages, visitors, clicks, plays, engaged CTR)
 *  - benchmarks: the distribution of per-page CTR / play-rate / watch across the
 *    network (p25 / median / p75 / p90 top-decile) — the "you vs the network" base
 *  - performance by page shape (template, video, deeplink, plan) — form → outcome
 *  - top pages, growth series, feature adoption, geo/device split
 *
 * Read-only. Reuses AnalyticsSql so "a visit" / "a person" / a time bucket are
 * computed exactly like the manager dashboard. This is the data foundation for
 * later exposing private aggregate benchmarks to agencies.
 */
class NetworkAnalytics
{
    use AnalyticsSql;

    /** A page needs at least this many visits in-range to enter the benchmark distribution. */
    private const BENCHMARK_MIN_VIEWS = 20;

    /** @return array<string, mixed> */
    public function snapshot(?Carbon $from = null, ?Carbon $to = null): array
    {
        // Per-page visit maps are the shared raw material for benchmarks, form
        // breakdowns and top pages — compute once, pass around.
        $viewsByPage  = $this->perPageVisits($from, $to, 'page_view');
        $clicksByPage = $this->perPageVisits($from, $to, 'link_click');
        $playsByPage  = $this->perPageVisits($from, $to, 'video_play');

        return [
            'range'            => [
                'from' => $from?->toIso8601String(),
                'to'   => $to?->toIso8601String(),
            ],
            'overview'         => $this->overview($from, $to),
            'benchmarks'       => $this->benchmarks($from, $to, $viewsByPage, $clicksByPage, $playsByPage),
            'by_shape'         => $this->byShape($viewsByPage, $clicksByPage, $playsByPage),
            'top_pages'        => $this->topPages($from, $to, $viewsByPage, $clicksByPage),
            'growth'           => $this->growth($from, $to),
            'feature_adoption' => $this->featureAdoption(),
            'by_country'       => $this->byCountry($from, $to),
            'by_device'        => $this->byDevice($from, $to),
            'generated_at'     => now()->toIso8601String(),
        ];
    }

    // ─── Building blocks ────────────────────────────────────────────────────

    private function base(?Carbon $from, ?Carbon $to)
    {
        $q = AnalyticsEvent::query();
        if ($from) $q->where('created_at', '>=', $from);
        if ($to)   $q->where('created_at', '<=', $to);
        return $q;
    }

    /** page_id => distinct visits, for one event type, across the whole network. */
    private function perPageVisits(?Carbon $from, ?Carbon $to, string $type)
    {
        return $this->base($from, $to)->where('type', $type)
            ->selectRaw('page_id, ' . $this->visitCountExpr() . ' as total')
            ->groupBy('page_id')
            ->pluck('total', 'page_id');
    }

    private function overview(?Carbon $from, ?Carbon $to): array
    {
        $views  = $this->countVisits((clone $this->base($from, $to))->where('type', 'page_view'));
        $clicks = $this->countVisits((clone $this->base($from, $to))->where('type', 'link_click'));
        $plays  = $this->countVisits((clone $this->base($from, $to))->where('type', 'video_play'));
        $people = $this->countPeople((clone $this->base($from, $to))->where('type', 'page_view'));

        // Engaged CTR (network): of sessions that started a video, the % that clicked.
        $watcherSessions = (clone $this->base($from, $to))->where('type', 'video_play')
            ->whereNotNull('session_id')->select('session_id');
        $watchers = (clone $this->base($from, $to))->where('type', 'video_play')
            ->whereNotNull('session_id')->distinct()->count('session_id');
        $engagedClicks = (clone $this->base($from, $to))->where('type', 'link_click')
            ->whereNotNull('session_id')->whereIn('session_id', $watcherSessions)
            ->distinct()->count('session_id');

        return [
            'active_pages'    => Page::where('is_active', true)->count(),
            'total_pages'     => Page::count(),
            'pages_created'   => $this->rangeCount(Page::query(), $from, $to),
            'total_users'     => User::count(),
            'paying_users'    => User::whereNotNull('stripe_subscription_id')->count(),
            'signups'         => $this->rangeCount(User::query(), $from, $to),
            'page_views'      => $views,
            'unique_visitors' => $people,
            'link_clicks'     => $clicks,
            'video_plays'     => $plays,
            'ctr'             => $views > 0 ? round($clicks / $views * 100, 1) : 0,
            'play_rate'       => $views > 0 ? round($plays / $views * 100, 1) : 0,
            'engaged_ctr'     => $watchers > 0 ? round($engagedClicks / $watchers * 100, 1) : 0,
            'watchers'        => $watchers,
            'countries'       => (clone $this->base($from, $to))->whereNotNull('country')->distinct()->count('country'),
            'total_events'    => (clone $this->base($from, $to))->count(),
        ];
    }

    private function rangeCount($query, ?Carbon $from, ?Carbon $to): int
    {
        if ($from) $query->where('created_at', '>=', $from);
        if ($to)   $query->where('created_at', '<=', $to);
        return $query->count();
    }

    /**
     * Distribution of per-page metrics across the network. Only pages with enough
     * volume (BENCHMARK_MIN_VIEWS) count, so a page with 2 views can't skew the
     * curve. Returns p25 / median / p75 / p90 (top decile) for each metric — this
     * is what powers "you convert at X, the top 10% convert at Y".
     */
    private function benchmarks(?Carbon $from, ?Carbon $to, $viewsByPage, $clicksByPage, $playsByPage): array
    {
        $avgWatchByPage = (clone $this->base($from, $to))->where('type', 'link_click')
            ->whereNotNull('value')
            ->selectRaw('page_id, avg(value) as a')
            ->groupBy('page_id')->pluck('a', 'page_id');

        $ctrs = [];
        $playRates = [];
        $watches = [];

        foreach ($viewsByPage as $pid => $views) {
            $views = (int) $views;
            if ($views < self::BENCHMARK_MIN_VIEWS) continue;

            $ctrs[]      = round((int) ($clicksByPage[$pid] ?? 0) / $views * 100, 1);
            $playRates[] = round((int) ($playsByPage[$pid] ?? 0) / $views * 100, 1);
            if (isset($avgWatchByPage[$pid])) {
                $watches[] = round((float) $avgWatchByPage[$pid], 1);
            }
        }

        return [
            'min_views_threshold' => self::BENCHMARK_MIN_VIEWS,
            'sample_pages'        => count($ctrs),
            'ctr'                 => $this->percentiles($ctrs),
            'play_rate'           => $this->percentiles($playRates),
            'avg_watch_seconds'   => $this->percentiles($watches),
        ];
    }

    /** @return array{p25:?float,median:?float,p75:?float,p90:?float} */
    private function percentiles(array $values): array
    {
        if (empty($values)) {
            return ['p25' => null, 'median' => null, 'p75' => null, 'p90' => null];
        }
        sort($values);
        return [
            'p25'    => $this->percentile($values, 25),
            'median' => $this->percentile($values, 50),
            'p75'    => $this->percentile($values, 75),
            'p90'    => $this->percentile($values, 90),
        ];
    }

    /** Linear-interpolation percentile over an already-sorted array. */
    private function percentile(array $sorted, int $p): float
    {
        $n = count($sorted);
        if ($n === 1) return (float) $sorted[0];

        $rank = ($p / 100) * ($n - 1);
        $lo   = (int) floor($rank);
        $hi   = (int) ceil($rank);
        if ($lo === $hi) return (float) $sorted[$lo];

        $frac = $rank - $lo;
        return round($sorted[$lo] + ($sorted[$hi] - $sorted[$lo]) * $frac, 1);
    }

    /**
     * Performance pooled by page shape: template, video presence, deeplink,
     * and plan. Pooled (sum clicks ÷ sum views), not average-of-ratios, so
     * high-traffic pages weigh correctly. This is the "form → outcome" read:
     * which page structures actually convert better across the network.
     */
    private function byShape($viewsByPage, $clicksByPage, $playsByPage): array
    {
        $pages = Page::with('user:id,plan')
            ->get(['id', 'template', 'page_type', 'video_url', 'deep_link_enabled', 'user_id']);

        return [
            'by_template' => $this->pooled($pages, $viewsByPage, $clicksByPage, $playsByPage,
                fn ($p) => $p->page_type === 'direct' ? 'direct' : ($p->template ?: 'classic')),
            'by_video'    => $this->pooled($pages, $viewsByPage, $clicksByPage, $playsByPage,
                fn ($p) => $p->video_url ? 'with_video' : 'no_video'),
            'by_deeplink' => $this->pooled($pages, $viewsByPage, $clicksByPage, $playsByPage,
                fn ($p) => $p->deep_link_enabled ? 'deeplink_on' : 'deeplink_off'),
            'by_plan'     => $this->pooled($pages, $viewsByPage, $clicksByPage, $playsByPage,
                fn ($p) => $p->user->plan ?? 'free'),
        ];
    }

    private function pooled($pages, $views, $clicks, $plays, callable $keyFn): array
    {
        $agg = [];
        foreach ($pages as $p) {
            $key = $keyFn($p);
            if ($key === null) continue;
            $agg[$key] ??= ['pages' => 0, 'views' => 0, 'clicks' => 0, 'plays' => 0];
            $agg[$key]['pages']++;
            $agg[$key]['views']  += (int) ($views[$p->id] ?? 0);
            $agg[$key]['clicks'] += (int) ($clicks[$p->id] ?? 0);
            $agg[$key]['plays']  += (int) ($plays[$p->id] ?? 0);
        }

        $rows = [];
        foreach ($agg as $key => $a) {
            $rows[] = [
                'key'       => $key,
                'pages'     => $a['pages'],
                'views'     => $a['views'],
                'clicks'    => $a['clicks'],
                'ctr'       => $a['views'] > 0 ? round($a['clicks'] / $a['views'] * 100, 1) : 0,
                'play_rate' => $a['views'] > 0 ? round($a['plays'] / $a['views'] * 100, 1) : 0,
            ];
        }

        usort($rows, fn ($x, $y) => $y['views'] <=> $x['views']);
        return $rows;
    }

    /** Highest-converting pages network-wide (by clicks), with their owner. */
    private function topPages(?Carbon $from, ?Carbon $to, $viewsByPage, $clicksByPage): array
    {
        $topIds = collect($clicksByPage)->sortDesc()->take(15)->keys();
        if ($topIds->isEmpty()) return [];

        $pages = Page::with('user:id,name,email')
            ->whereIn('id', $topIds)
            ->get(['id', 'model_name', 'slug', 'template', 'user_id'])
            ->keyBy('id');

        return $topIds->map(function ($pid) use ($pages, $viewsByPage, $clicksByPage) {
            $p = $pages->get($pid);
            if (! $p) return null;
            $v = (int) ($viewsByPage[$pid] ?? 0);
            $c = (int) ($clicksByPage[$pid] ?? 0);
            return [
                'id'       => $p->id,
                'name'     => $p->model_name,
                'slug'     => $p->slug,
                'template' => $p->template,
                'owner'    => $p->user?->name ?: $p->user?->email,
                'views'    => $v,
                'clicks'   => $c,
                'ctr'      => $v > 0 ? round($c / $v * 100, 1) : 0,
            ];
        })->filter()->values()->all();
    }

    /** Network growth series over the range: pages created, visits, clicks. */
    private function growth(?Carbon $from, ?Carbon $to): array
    {
        $granularity = $this->pickGranularity($from, $to);
        $expr        = $this->periodExpr($granularity);
        $visits      = $this->visitCountExpr();

        $views = (clone $this->base($from, $to))->where('type', 'page_view')
            ->selectRaw("$expr as period, $visits as total")
            ->groupBy('period')->orderBy('period')->pluck('total', 'period');

        $clicks = (clone $this->base($from, $to))->where('type', 'link_click')
            ->selectRaw("$expr as period, $visits as total")
            ->groupBy('period')->orderBy('period')->pluck('total', 'period');

        $pagesQ = Page::query();
        if ($from) $pagesQ->where('created_at', '>=', $from);
        if ($to)   $pagesQ->where('created_at', '<=', $to);
        $pagesCreated = $pagesQ->selectRaw("$expr as period, count(*) as total")
            ->groupBy('period')->orderBy('period')->pluck('total', 'period');

        $labels = collect($views->keys())
            ->merge($clicks->keys())->merge($pagesCreated->keys())
            ->unique()->sort()->values();

        return [
            'granularity'   => $granularity,
            'labels'        => $labels->all(),
            'views'         => $labels->map(fn ($l) => (int) $views->get($l, 0))->all(),
            'clicks'        => $labels->map(fn ($l) => (int) $clicks->get($l, 0))->all(),
            'pages_created' => $labels->map(fn ($l) => (int) $pagesCreated->get($l, 0))->all(),
        ];
    }

    private function pickGranularity(?Carbon $from, ?Carbon $to): string
    {
        if (! $from) return 'month';
        $days = ($to ?? now())->diffInDays($from);
        return match (true) {
            $days <= 2   => 'hour',
            $days <= 31  => 'day',
            $days <= 120 => 'week',
            default      => 'month',
        };
    }

    /** Share of active pages using each growth/optimization feature. */
    private function featureAdoption(): array
    {
        $total = Page::where('is_active', true)->count();
        if ($total === 0) return ['total_pages' => 0, 'features' => []];

        $share = fn (\Closure $filter) => round($filter(Page::where('is_active', true))->count() / $total * 100, 1);

        return [
            'total_pages' => $total,
            'features'    => [
                'video'         => $share(fn ($q) => $q->whereNotNull('video_url')),
                'deeplink'      => $share(fn ($q) => $q->where('deep_link_enabled', true)),
                'popup'         => $share(fn ($q) => $q->where('template', 'vsl-popup')),
                'bandeau'       => $share(fn ($q) => $q->where('template', 'vsl-bandeau')),
                'cta_reveal'    => $share(fn ($q) => $q->whereNotNull('cta_reveal_at')),
                'bot_protection' => $share(fn ($q) => $q->where('bot_protection', true)),
                'custom_domain' => $share(fn ($q) => $q->whereNotNull('custom_domain')),
                'white_label'   => $share(fn ($q) => $q->where('show_branding', false)),
            ],
        ];
    }

    private function byCountry(?Carbon $from, ?Carbon $to): array
    {
        return (clone $this->base($from, $to))->where('type', 'page_view')->whereNotNull('country')
            ->selectRaw('country, ' . $this->visitCountExpr() . ' as total')
            ->groupBy('country')->orderByDesc('total')->limit(20)
            ->pluck('total', 'country')->all();
    }

    private function byDevice(?Carbon $from, ?Carbon $to): array
    {
        return (clone $this->base($from, $to))->where('type', 'page_view')->whereNotNull('device')
            ->selectRaw('device, ' . $this->visitCountExpr() . ' as total')
            ->groupBy('device')->pluck('total', 'device')->all();
    }
}
