<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'email',
        'phone',
        'address',
        'store_logo',
        'is_active',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($vendor) {
            $vendor->slug = Vendor::slug($vendor->name);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Products::class);
    }

    public function getLogoUrlAttribute(): string
    {
        return $this->store_logo ? asset('storage/' . $this->store_logo) : asset('images/default_logo.png');
    }
}
