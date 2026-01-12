<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'stock',
        'options', // JSON
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'options' => 'array',
    ];

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function attributeValues(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            ProductAttributeValue::class,
            'product_variant_attributes',
            'variant_id',
            'attribute_value_id'
        )->withTimestamps();
    }

    /**
     * Get formatted options for display (e.g., "Color: Red, Size: M")
     */
    protected function formattedOptions(): Attribute
    {
        return Attribute::make(
            get: function () {
                $options = [];
                foreach ($this->attributeValues as $value) {
                    $attributeName = $value->attribute->name ?? 'Unknown';
                    $options[] = "{$attributeName}: {$value->value}";
                }
                return implode(', ', $options);
            }
        );
    }

    /**
     * Check if variant has sufficient stock
     */
    public function hasStock(int $quantity = 1): bool
    {
        return $this->stock >= $quantity;
    }

    /**
     * Decrease stock quantity
     */
    public function decreaseStock(int $quantity): void
    {
        $this->decrement('stock', $quantity);
    }

    /**
     * Increase stock quantity
     */
    public function increaseStock(int $quantity): void
    {
        $this->increment('stock', $quantity);
    }
}
