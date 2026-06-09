<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Stevebauman\Location\Facades\Location;

class PageController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            $request->user()->pages()->with('links')->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'model_name'    => 'required|string|max:100',
            'group_name'    => 'nullable|string|max:100',
            'page_type'     => 'nullable|in:landing,direct',
            'direct_url'    => 'nullable|url|required_if:page_type,direct',
            'model_handle'  => 'nullable|string|max:100',
            'bio'           => 'nullable|string|max:500',
            'video_url'     => 'nullable|url',
            'avatar_url'    => 'nullable|url',
            'bg_image_url'  => 'nullable|url',
            'bg_color'      => 'nullable|string|max:7',
            'btn_color'     => 'nullable|string|max:7',
            'template'      => 'nullable|string|max:50',
            'verified_badge'=> 'boolean',
            'show_avatar'   => 'boolean',
            'age_gate'      => 'boolean',
            'online_status' => 'boolean',
            'location'      => 'nullable|string|max:100',
            'response_time' => 'nullable|string|max:50',
            'countdown_end' => 'nullable|date',
            'promo_text'    => 'nullable|string|max:200',
            'bot_protection'=> 'boolean',
            'deep_link_enabled'    => 'boolean',
            'strict_deep_link'     => 'boolean',
            'link_preview_enabled' => 'boolean',
            'custom_domain' => 'nullable|string|max:255',
            'links'         => 'nullable|array',
            'links.*.type'  => 'required|string|max:50',
            'links.*.label' => 'required|string|max:100',
            'links.*.url'   => 'required|url',
            'links.*.icon'  => 'nullable|string|max:50',
            'links.*.image_url' => 'nullable|url',
            'links.*.btn_color' => 'nullable|string|max:7',
        ]);

        if (!$request->user()->canCreatePage()) {
            $limit = $request->user()->pageLimit();
            return response()->json([
                'error' => 'plan_limit',
                'message' => "Votre plan ne permet pas plus de {$limit} page(s). Passez à un plan supérieur.",
                'plan'  => $request->user()->plan,
                'limit' => $limit,
            ], 403);
        }

        $slug = Str::slug($request->model_name) . '-' . Str::random(5);

        $page = $request->user()->pages()->create([
            'slug'          => $slug,
            'group_name'    => $request->group_name,
            'page_type'     => $request->page_type ?? 'landing',
            'direct_url'    => $request->direct_url,
            'template'      => $request->template ?? 'classic',
            'model_name'    => $request->model_name,
            'model_handle'  => $request->model_handle,
            'bio'           => $request->bio,
            'video_url'     => $request->video_url,
            'avatar_url'    => $request->avatar_url,
            'bg_image_url'  => $request->bg_image_url,
            'bg_color'      => $request->bg_color ?? '#0d0d0d',
            'btn_color'     => $request->btn_color ?? '#00aff0',
            'verified_badge'=> $request->verified_badge ?? false,
            'show_avatar'   => $request->show_avatar ?? true,
            'age_gate'      => $request->age_gate ?? true,
            'online_status' => $request->online_status ?? false,
            'location'      => $request->location,
            'response_time' => $request->response_time,
            'countdown_end' => $request->countdown_end,
            'promo_text'    => $request->promo_text,
            'bot_protection'=> $request->bot_protection ?? false,
            'deep_link_enabled'    => $request->deep_link_enabled ?? true,
            'strict_deep_link'     => $request->strict_deep_link ?? false,
            'link_preview_enabled' => $request->link_preview_enabled ?? true,
            'custom_domain' => $request->custom_domain,
        ]);

        foreach ($request->links ?? [] as $i => $link) {
            $page->links()->create([
                'type'      => $link['type'],
                'label'     => $link['label'],
                'url'       => $link['url'],
                'icon'      => $link['icon'] ?? null,
                'image_url' => $link['image_url'] ?? null,
                'btn_color' => $link['btn_color'] ?? null,
                'order'     => $i,
            ]);
        }

        return response()->json($page->load('links'), 201);
    }

    public function show(Request $request, string $id)
    {
        $page = $request->user()->pages()->with('links')->findOrFail($id);
        return response()->json($page);
    }

    public function showPublic(Request $request, string $slug)
    {
        $page = Page::where('slug', $slug)
            ->where('is_active', true)
            ->with(['links', 'geoRules'])
            ->firstOrFail();

        // Résolution IP
        $ua = $request->userAgent() ?? '';
        $device = preg_match('/Mobile|Android|iPhone/i', $ua) ? 'mobile' : 'desktop';

        // Shield Protection™ — intercept bots server-side before any JS loads
        if ($page->bot_protection) {
            $isMetaBot = preg_match('/facebookexternalhit|Facebot|facebookcatalog|FacebookBot|meta-externalagent/i', $ua);
            $isBot = $isMetaBot || preg_match('/bot|crawl|spider|slurp|Googlebot|bingbot|YandexBot|Applebot|LinkedInBot|Twitterbot|WhatsApp|Slackbot|TelegramBot|Discordbot|Pinterest|AhrefsBot|SemrushBot/i', $ua);
            if ($isBot) {
                $page->analytics()->create(['type' => 'bot_blocked', 'device' => $device, 'country' => null]);
                return response('<html><head><meta name="robots" content="noindex,nofollow"><title>My Links</title></head><body><p>Check out my latest content.</p></body></html>', 200)
                    ->header('Content-Type', 'text/html');
            }
        }

        $country = null;
        try {
            $position = Location::get($request->ip());
            $country = $position ? $position->countryCode : null;
        } catch (\Exception $e) {}

        // Geo-routing — redirige si une règle correspond au pays
        if ($country && $page->geoRules->count()) {
            $rule = $page->geoRules->firstWhere('country_code', $country);
            if ($rule) {
                $page->analytics()->create(['type' => 'page_view', 'device' => $device, 'country' => $country]);
                return redirect()->away($rule->redirect_url);
            }
        }

        // Direct link — redirect immédiat
        if ($page->page_type === 'direct' && $page->direct_url) {
            $page->analytics()->create(['type' => 'page_view', 'device' => $device, 'country' => $country]);
            return redirect()->away($page->direct_url);
        }

        return response()->json($page);
    }

    public function update(Request $request, string $id)
    {
        $page = $request->user()->pages()->findOrFail($id);

        $request->validate([
            'model_name'    => 'sometimes|string|max:100',
            'slug'          => 'sometimes|string|max:100|alpha_dash|unique:pages,slug,' . $page->id,
            'page_type'     => 'nullable|in:landing,direct',
            'direct_url'    => 'nullable|url|required_if:page_type,direct',
            'model_handle'  => 'nullable|string|max:100',
            'bio'           => 'nullable|string|max:500',
            'video_url'     => 'nullable|url',
            'avatar_url'    => 'nullable|url',
            'bg_image_url'  => 'nullable|url',
            'bg_color'      => 'nullable|string|max:7',
            'btn_color'     => 'nullable|string|max:7',
            'template'      => 'nullable|string|max:50',
            'verified_badge'=> 'boolean',
            'show_avatar'   => 'boolean',
            'age_gate'      => 'boolean',
            'online_status' => 'boolean',
            'location'      => 'nullable|string|max:100',
            'response_time' => 'nullable|string|max:50',
            'countdown_end' => 'nullable|date',
            'promo_text'    => 'nullable|string|max:200',
            'bot_protection'=> 'boolean',
            'deep_link_enabled'    => 'boolean',
            'strict_deep_link'     => 'boolean',
            'link_preview_enabled' => 'boolean',
            'custom_domain' => 'nullable|string|max:255',
            'is_active'     => 'boolean',
            'links'         => 'nullable|array',
            'links.*.type'  => 'required|string|max:50',
            'links.*.label' => 'required|string|max:100',
            'links.*.url'   => 'required|url',
            'links.*.icon'  => 'nullable|string|max:50',
            'links.*.image_url' => 'nullable|url',
            'links.*.btn_color' => 'nullable|string|max:7',
        ]);

        $page->update($request->only([
            'model_name', 'slug', 'group_name', 'page_type', 'direct_url', 'model_handle', 'bio', 'video_url', 'avatar_url', 'bg_image_url',
            'bg_color', 'btn_color', 'template', 'verified_badge', 'show_avatar',
            'age_gate', 'is_active', 'online_status', 'location', 'response_time',
            'countdown_end', 'promo_text', 'bot_protection', 'deep_link_enabled',
            'strict_deep_link', 'link_preview_enabled', 'custom_domain',
        ]));

        if ($request->has('links')) {
            $page->links()->delete();
            foreach ($request->links as $i => $link) {
                $page->links()->create([
                    'type'      => $link['type'],
                    'label'     => $link['label'],
                    'url'       => $link['url'],
                    'icon'      => $link['icon'] ?? null,
                    'image_url' => $link['image_url'] ?? null,
                    'btn_color' => $link['btn_color'] ?? null,
                    'order'     => $i,
                ]);
            }
        }

        return response()->json($page->load('links'));
    }

    public function checkSlug(Request $request, string $slug)
    {
        $exists = Page::where('slug', $slug)
            ->where('id', '!=', $request->query('exclude'))
            ->exists();

        return response()->json(['available' => ! $exists]);
    }

    public function destroy(Request $request, string $id)
    {
        $request->user()->pages()->findOrFail($id)->delete();
        return response()->json(['message' => 'Page supprimée.']);
    }
}
