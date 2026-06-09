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
    'bot_protection', 'deep_link_enabled', 'strict_deep_link', 'link_preview_enabled', 'custom_domain',
])]
class Page extends Model
{
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
