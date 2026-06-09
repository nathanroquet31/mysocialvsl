<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['page_id', 'country_code', 'redirect_url', 'order'])]
class PageGeoRule extends Model
{
    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}
