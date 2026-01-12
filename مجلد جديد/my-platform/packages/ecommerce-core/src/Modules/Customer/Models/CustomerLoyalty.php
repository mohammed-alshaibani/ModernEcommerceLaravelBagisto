<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Modules\Customer\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class CustomerLoyalty extends Model
{
    protected $table = 'customer_loyalty';

    protected $fillable = [
        'user_id',
        'points_balance',
        'membership_level_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function membershipLevel(): BelongsTo
    {
        return $this->belongsTo(MembershipLevel::class);
    }
}
