<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Products extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'stock_quantity',
        'product_image',
        'is_active',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            $product->slug = Str::slug($product->name);
        });
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function category()
    {
        return $this->belongsTo(Categories::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Orders::class, 'order_items')
            ->withPivot('quantity', 'price')
            ->withTimestamps();
    }

     public function getImageUrlAttribute(): string
    {
        return $this->product_image ? asset('storage/' . $this->product_image) : asset('images/default_product.png');
    }
}