<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\ResolvesPublicPages;
use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    use ResolvesPublicPages;

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
            'direct_url'    => 'nullable|url:http,https|required_if:page_type,direct',
            'model_handle'  => 'nullable|string|max:100',
            'bio'           => 'nullable|string|max:500',
            'video_url'     => 'nullable|url:http,https',
            'avatar_url'    => 'nullable|url:http,https',
            'bg_image_url'  => 'nullable|url:http,https',
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
            'video_fit'     => 'nullable|in:cover,contain',
            'overlay_opacity' => 'nullable|numeric|min:0|max:1',
            'card_position' => 'nullable|in:bottom,center',
            'vsl_enabled'   => 'boolean',
            'vsl_position'  => 'nullable|in:top,middle,bottom',
            'fomo_enabled'  => 'boolean',
            'fomo_title'    => 'nullable|string|max:255',
            'fomo_message'  => 'nullable|string|max:500',
            'fomo_cta_label'=> 'nullable|string|max:100',
            'fomo_delay_seconds' => 'nullable|integer|min:0|max:300',
            'meta_title'    => 'nullable|string|max:120',
            'meta_description' => 'nullable|string|max:300',
            'og_image_url'  => 'nullable|url:http,https',
            'utm_passthrough' => 'boolean',
            'links'         => 'nullable|array',
            'links.*.type'  => 'required|string|max:50',
            'links.*.label' => 'nullable|string|max:100',
            'links.*.title' => 'nullable|string|max:255',
            'links.*.subtitle' => 'nullable|string|max:255',
            'links.*.url'   => 'nullable|url:http,https',
            'links.*.icon'  => 'nullable|string|max:50',
            'links.*.image_url' => 'nullable|url:http,https',
            'links.*.meta'  => 'nullable|array',
            'links.*.btn_color' => 'nullable|string|max:7',
            'links.*.is_visible' => 'boolean',
        ]);

        $isDirectLink = $request->input('page_type') === 'direct';
        if ($isDirectLink) {
            if (!$request->user()->canCreateLink()) {
                $limit = $request->user()->linkLimit();
                return response()->json([
                    'error'   => 'plan_limit',
                    'message' => "Your plan doesn't allow more than {$limit} direct link(s). Add a pack or upgrade to a higher plan.",
                    'plan'    => $request->user()->plan,
                    'limit'   => $limit,
                ], 403);
            }
        } else {
            if (!$request->user()->canCreatePage()) {
                $limit = $request->user()->pageLimit();
                return response()->json([
                    'error'   => 'plan_limit',
                    'message' => "Your plan doesn't allow more than {$limit} page(s). Add a pack or upgrade to a higher plan.",
                    'plan'    => $request->user()->plan,
                    'limit'   => $limit,
                ], 403);
            }
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
            'video_fit'     => $request->video_fit ?? 'contain',
            'overlay_opacity' => $request->overlay_opacity ?? 0.6,
            'card_position' => $request->card_position ?? 'bottom',
            'vsl_enabled'   => $request->vsl_enabled ?? true,
            'vsl_position'  => $request->vsl_position ?? 'top',
            'fomo_enabled'  => $request->fomo_enabled ?? false,
            'fomo_title'    => $request->fomo_title,
            'fomo_message'  => $request->fomo_message,
            'fomo_cta_label'=> $request->fomo_cta_label,
            'fomo_delay_seconds' => $request->fomo_delay_seconds ?? 5,
            'meta_title'    => $request->meta_title,
            'meta_description' => $request->meta_description,
            'og_image_url'  => $request->og_image_url,
            'utm_passthrough' => $request->utm_passthrough ?? true,
        ]);

        foreach ($request->links ?? [] as $i => $link) {
            $page->links()->create([
                'type'      => $link['type'],
                'label'     => $link['label'] ?? '',
                'title'     => $link['title'] ?? null,
                'subtitle'  => $link['subtitle'] ?? null,
                'url'       => $link['url'] ?? '',
                'icon'      => $link['icon'] ?? null,
                'image_url' => $link['image_url'] ?? null,
                'meta'      => $link['meta'] ?? null,
                'btn_color' => $link['btn_color'] ?? null,
                'order'     => $i,
                'is_visible'=> $link['is_visible'] ?? true,
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
        $page = $this->resolvePublicPage($slug);

        if ($guard = $this->publicPageGuards($page, $request)) {
            return $guard;
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
            'direct_url'    => 'nullable|url:http,https|required_if:page_type,direct',
            'model_handle'  => 'nullable|string|max:100',
            'bio'           => 'nullable|string|max:500',
            'video_url'     => 'nullable|url:http,https',
            'avatar_url'    => 'nullable|url:http,https',
            'bg_image_url'  => 'nullable|url:http,https',
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
            'video_fit'     => 'nullable|in:cover,contain',
            'overlay_opacity' => 'nullable|numeric|min:0|max:1',
            'card_position' => 'nullable|in:bottom,center',
            'vsl_enabled'   => 'boolean',
            'vsl_position'  => 'nullable|in:top,middle,bottom',
            'fomo_enabled'  => 'boolean',
            'fomo_title'    => 'nullable|string|max:255',
            'fomo_message'  => 'nullable|string|max:500',
            'fomo_cta_label'=> 'nullable|string|max:100',
            'fomo_delay_seconds' => 'nullable|integer|min:0|max:300',
            'meta_title'    => 'nullable|string|max:120',
            'meta_description' => 'nullable|string|max:300',
            'og_image_url'  => 'nullable|url:http,https',
            'utm_passthrough' => 'boolean',
            'links'         => 'nullable|array',
            'links.*.type'  => 'required|string|max:50',
            'links.*.label' => 'nullable|string|max:100',
            'links.*.title' => 'nullable|string|max:255',
            'links.*.subtitle' => 'nullable|string|max:255',
            'links.*.url'   => 'nullable|url:http,https',
            'links.*.icon'  => 'nullable|string|max:50',
            'links.*.image_url' => 'nullable|url:http,https',
            'links.*.meta'  => 'nullable|array',
            'links.*.btn_color' => 'nullable|string|max:7',
            'links.*.is_visible' => 'boolean',
        ]);

        $page->update($request->only([
            'model_name', 'slug', 'group_name', 'page_type', 'direct_url', 'model_handle', 'bio', 'video_url', 'avatar_url', 'bg_image_url',
            'bg_color', 'btn_color', 'template', 'verified_badge', 'show_avatar',
            'age_gate', 'is_active', 'online_status', 'location', 'response_time',
            'countdown_end', 'promo_text', 'bot_protection', 'deep_link_enabled',
            'strict_deep_link', 'link_preview_enabled', 'custom_domain',
            'video_fit', 'overlay_opacity', 'card_position',
            'vsl_enabled', 'vsl_position',
            'fomo_enabled', 'fomo_title', 'fomo_message', 'fomo_cta_label', 'fomo_delay_seconds',
            'meta_title', 'meta_description', 'og_image_url', 'utm_passthrough',
        ]));

        if ($request->has('links')) {
            $page->links()->delete();
            foreach ($request->links as $i => $link) {
                $page->links()->create([
                    'type'      => $link['type'],
                    'label'     => $link['label'] ?? '',
                    'title'     => $link['title'] ?? null,
                    'subtitle'  => $link['subtitle'] ?? null,
                    'url'       => $link['url'] ?? '',
                    'icon'      => $link['icon'] ?? null,
                    'image_url' => $link['image_url'] ?? null,
                    'meta'      => $link['meta'] ?? null,
                    'btn_color' => $link['btn_color'] ?? null,
                    'order'     => $i,
                    'is_visible'=> $link['is_visible'] ?? true,
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
        return response()->json(['message' => 'Page deleted.']);
    }
}
