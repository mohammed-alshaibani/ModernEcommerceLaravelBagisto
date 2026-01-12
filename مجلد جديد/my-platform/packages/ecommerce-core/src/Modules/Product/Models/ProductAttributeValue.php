<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAttributeValue extends Model
{
    protected $fillable = [
        'attribute_id',
        'value',
        'label',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(ProductAttribute::class, 'attribute_id');
    }

    public function variants(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            ProductVariant::class,
            'product_variant_attributes',
            'attribute_value_id',
            'variant_id'
        );
    }
}
