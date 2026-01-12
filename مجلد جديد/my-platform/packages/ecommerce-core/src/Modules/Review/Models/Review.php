<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Modules\Review\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MyPlatform\EcommerceCore\Modules\Product\Models\Product;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'rating',
        'comment',
        'is_approved',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
