<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'delivery_person_id',
        'status',
        'pickup_time',
        'delivery_time',
    ];

    public function order()
    {
        return $this->belongsTo(Orders::class);
    }

    public function deliveryPerson()
    {
        return $this->belongsTo(DeliveryPerson::class);
    }
}