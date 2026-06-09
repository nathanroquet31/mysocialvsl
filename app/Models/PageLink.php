<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['page_id', 'type', 'label', 'url', 'icon', 'image_url', 'btn_color', 'order'])]
class PageLink extends Model
{
    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}
