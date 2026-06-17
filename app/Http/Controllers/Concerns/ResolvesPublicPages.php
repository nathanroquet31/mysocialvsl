<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Page;
use Illuminate\Http\Request;
use Stevebauman\Location\Facades\Location;
use Symfony\Component\HttpFoundation\Response;

/**
 * Shared logic for serving public VSL pages, used by both the API (JSON)
 * and the web (HTML) entrypoints so cloaking/geo/direct behaviour can never
 * diverge between the two.
 */
trait ResolvesPublicPages
{
    protected function resolvePublicPage(string $slug): Page
    {
        return Page::where('slug', $slug)
            ->where('is_active', true)
            ->with(['links', 'geoRules'])
            ->firstOrFail();
    }

    /**
     * Server-side guards that must run before any JS: bot cloaking,
     * geo-routing redirects and direct-link redirects.
     *
     * Returns a Response to short-circuit the request (decoy or redirect),
     * or null when the real page should be rendered.
     */
    protected function publicPageGuards(Page $page, Request $request): ?Response
    {
        $ua = $request->userAgent() ?? '';
        $device = preg_match('/Mobile|Android|iPhone/i', $ua) ? 'mobile' : 'desktop';

        // Shield Protection™ — intercept bots server-side before any JS loads.
        // NB: "Instagram" is deliberately NOT a bot — the in-app browser is a real human.
        if ($page->bot_protection) {
            $isMetaBot = preg_match('/facebookexternalhit|Facebot|facebookcatalog|FacebookBot|meta-externalagent/i', $ua);
            $isBot = $isMetaBot || preg_match('/bot|crawl|spider|slurp|LinkedInBot|Twitterbot|WhatsApp|Slackbot|TelegramBot|Discordbot|pinterest|Applebot|bingbot|Googlebot|YandexBot|DuckDuckBot|ia_archiver|AhrefsBot|SemrushBot|MJ12bot|DotBot|BLEXBot|DataForSeoBot|PetalBot|BaiduSpider/i', $ua);
            if ($isBot) {
                $page->analytics()->create(['type' => 'bot_blocked', 'device' => $device, 'country' => null]);

                return response(
                    '<html><head><meta name="robots" content="noindex,nofollow"><title>My Links</title></head><body><p>Check out my latest content.</p></body></html>',
                    200
                )->header('Content-Type', 'text/html');
            }
        }

        $country = null;
        try {
            $position = Location::get($request->ip());
            $country = $position ? $position->countryCode : null;
        } catch (\Exception $e) {
            // Geo lookup is best-effort; never block the page on it.
        }

        // Geo-routing — redirect if a rule matches the visitor's country.
        if ($country && $page->geoRules->count()) {
            $rule = $page->geoRules->firstWhere('country_code', $country);
            if ($rule) {
                $page->analytics()->create(['type' => 'page_view', 'device' => $device, 'country' => $country]);

                return redirect()->away($rule->redirect_url);
            }
        }

        // Direct link — immediate redirect.
        if ($page->page_type === 'direct' && $page->direct_url) {
            $page->analytics()->create(['type' => 'page_view', 'device' => $device, 'country' => $country]);

            return redirect()->away($page->direct_url);
        }

        return null;
    }
}
