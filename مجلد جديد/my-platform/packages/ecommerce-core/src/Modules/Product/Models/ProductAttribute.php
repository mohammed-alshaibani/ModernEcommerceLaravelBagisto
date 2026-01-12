<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductAttribute extends Model
{
    protected $table = 'attributes';

    protected $fillable = [
        'name',
        'slug',
        'type',
        'is_global',
    ];

    public function values(): HasMany
    {
        return $this->hasMany(AttributeValue::class, 'attribute_id');
    }
}
