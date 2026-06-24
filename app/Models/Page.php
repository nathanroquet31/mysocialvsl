<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'user_id', 'slug', 'group_name', 'template', 'page_type', 'direct_url',
    'model_name', 'model_handle', 'bio',
    'video_url', 'avatar_url', 'bg_image_url',
    'bg_color', 'btn_color',
    'verified_badge', 'show_avatar', 'age_gate', 'is_active',
    'online_status', 'location', 'response_time', 'countdown_end', 'promo_text',
    'bot_protection', 'deep_link_enabled', 'strict_deep_link', 'link_preview_enabled', 'show_branding', 'custom_domain',
    'video_fit', 'overlay_opacity', 'card_position',
    'vsl_enabled', 'vsl_position', 'cta_reveal_at',
    'fomo_enabled', 'fomo_title', 'fomo_message', 'fomo_cta_label', 'fomo_delay_seconds',
    'popup_delay_seconds', 'popup_image_url', 'popup_text', 'popup_title', 'popup_subtitle',
    'meta_title', 'meta_description', 'og_image_url', 'utm_passthrough',
])]
class Page extends Model
{
    protected function casts(): array
    {
        return [
            // Cast so the white-label flag is a real bool in JSON + comparisons
            // (the older toggles predate this and still ride as 0/1 — harmless).
            'show_branding' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function links()
    {
        return $this->hasMany(PageLink::class)->orderBy('order');
    }

    public function analytics()
    {
        return $this->hasMany(AnalyticsEvent::class);
    }

    public function geoRules()
    {
        return $this->hasMany(PageGeoRule::class)->orderBy('order');
    }
}
