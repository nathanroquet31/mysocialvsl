<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['page_id', 'type', 'label', 'title', 'subtitle', 'url', 'icon', 'image_url', 'meta', 'btn_color', 'order', 'is_visible'])]
class PageLink extends Model
{
    protected function casts(): array
    {
        return [
            'meta'       => 'array',
            'is_visible' => 'boolean',
        ];
    }

    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}
