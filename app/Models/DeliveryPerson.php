<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryPerson extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'vehicle_type',
        'is_available',
    ];

    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }
}