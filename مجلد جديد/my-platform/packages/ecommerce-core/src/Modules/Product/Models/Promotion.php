<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Promotion extends Model
{
    protected $fillable = [
        'name',
        'code',
        'type',
        'value',
        'min_purchase_amount',
        'max_uses',
        'uses_count',
        'applies_to',
        'is_active',
        'starts_at',
        'expires_at',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_purchase_amount' => 'decimal:2',
        'max_uses' => 'integer',
        'uses_count' => 'integer',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            });
    }

    public function scopeAvailable($query)
    {
        return $query->active()->where(function ($q) {
            $q->whereNull('max_uses')
                ->orWhereRaw('uses_count < max_uses');
        });
    }

    // Relationships
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'promotion_products');
    }

    // Helper methods
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->max_uses && $this->uses_count >= $this->max_uses) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $amount): float
    {
        if ($this->type === 'percentage') {
            return round(($amount * $this->value) / 100, 2);
        }

        return min($this->value, $amount);
    }
}
