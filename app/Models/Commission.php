<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['affiliate_id', 'referred_user_id', 'stripe_invoice_id', 'amount_paid_cents', 'commission_cents', 'currency', 'rate', 'status'])]
class Commission extends Model
{
    protected function casts(): array
    {
        return [
            'amount_paid_cents' => 'integer',
            'commission_cents'  => 'integer',
            'rate'              => 'decimal:4',
        ];
    }

    public function affiliate()
    {
        return $this->belongsTo(User::class, 'affiliate_id');
    }

    public function referredUser()
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }
}
