<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Modules\Analytics\Models;

use Illuminate\Database\Eloquent\Model;
use MyPlatform\EcommerceCore\Modules\Order\Models\Order;

class Conversion extends Model
{
    protected $table = 'analytics_conversions';
    public $timestamps = false;

    protected $fillable = [
        'session_id',
        'order_id',
        'amount',
        'source',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
