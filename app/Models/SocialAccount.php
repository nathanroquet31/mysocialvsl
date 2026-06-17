<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;

#[Fillable(['user_id', 'platform', 'username', 'external_id', 'access_token', 'token_expires_at', 'tags', 'stats', 'last_synced_at'])]
#[Hidden(['access_token'])]
class SocialAccount extends Model
{
    protected function casts(): array
    {
        return [
            'tags'             => 'array',
            'stats'            => 'array',
            'token_expires_at' => 'datetime',
            'last_synced_at'   => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
