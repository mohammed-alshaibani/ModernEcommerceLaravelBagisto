<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'type', // Add type
        'price',
        'sku',
        'description',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function variants(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function media(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductMedia::class)->orderBy('sort_order');
    }
}
