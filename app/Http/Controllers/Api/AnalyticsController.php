<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsEvent;
use App\Models\Page;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Stevebauman\Location\Facades\Location;

class AnalyticsController extends Controller
{
    // POST /p/{slug}/event — enregistre un événement (public, appelé par la page fan)
    public function track(Request $request, string $slug)
    {
        $page = Page::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $request->validate([
            'type'         => 'required|in:page_view,link_click,age_confirmed',
            'page_link_id' => 'nullable|integer|exists:page_links,id',
        ]);

        $ua = $request->userAgent() ?? '';
        $device = preg_match('/Mobile|Android|iPhone/i', $ua) ? 'mobile' : 'desktop';

        $country = null;
        try {
            $position = Location::get($request->ip());
            $country = $position ? $position->countryCode : null;
        } catch (\Exception $e) {
            // silently fail
        }

        AnalyticsEvent::create([
            'page_id'      => $page->id,
            'type'         => $request->type,
            'page_link_id' => $request->page_link_id,
            'country'      => $country,
            'device'       => $device,
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

        $stats = [
            'period'        => $days,
            'page_views'    => (clone $events)->where('type', 'page_view')->count(),
            'age_confirmed' => (clone $events)->where('type', 'age_confirmed')->count(),
            'link_clicks'   => (clone $events)->where('type', 'link_click')->count(),
            'by_device'     => (clone $events)->selectRaw('device, count(*) as total')
                                    ->groupBy('device')->pluck('total', 'device'),
            'by_link'       => (clone $events)->where('type', 'link_click')
                                    ->selectRaw('page_link_id, count(*) as total')
                                    ->groupBy('page_link_id')->pluck('total', 'page_link_id'),
            'daily'         => (clone $events)->where('type', 'page_view')
                                    ->selectRaw('DATE(created_at) as date, count(*) as total')
                                    ->groupBy('date')
                                    ->orderBy('date')
                                    ->pluck('total', 'date'),
            'by_country'    => (clone $events)->whereNotNull('country')
                                    ->selectRaw('country, count(*) as total')
                                    ->groupBy('country')
                                    ->orderByDesc('total')
                                    ->pluck('total', 'country'),
        ];

        $stats['ctr'] = $stats['page_views'] > 0
            ? round(($stats['link_clicks'] / $stats['page_views']) * 100, 1)
            : 0;

        return response()->json($stats);
    }
}
