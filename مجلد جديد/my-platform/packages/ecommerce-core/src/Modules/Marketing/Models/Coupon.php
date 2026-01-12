<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Modules\Marketing\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type', // fixed, percentage
        'value',
        'expires_at',
        'usage_limit',
        'used_count',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'value' => 'decimal:2',
    ];

    public function isValid(): bool
    {
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }
}
