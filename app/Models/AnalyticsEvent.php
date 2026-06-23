<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['page_id', 'type', 'page_link_id', 'country', 'device', 'value', 'referrer', 'session_id', 'visitor_id'])]
class AnalyticsEvent extends Model
{
    public $updated_at = false;

    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}
