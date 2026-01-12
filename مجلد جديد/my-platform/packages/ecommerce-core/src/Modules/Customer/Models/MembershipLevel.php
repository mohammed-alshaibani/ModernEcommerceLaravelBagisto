<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Modules\Customer\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipLevel extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'min_points',
        'discount_percentage',
    ];
}
