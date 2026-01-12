<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'total_amount',
        'shipping_address',
        'billing_address',
        'payment_method',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->belongsToMany(Products::class, 'order_items')
            ->withPivot('quantity', 'price')
            ->withTimestamps();
    }

    public function delivery()
    {
        return $this->hasOne(Delivery::class);
    }
}