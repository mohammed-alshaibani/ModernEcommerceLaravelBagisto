<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Modules\Order\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MyPlatform\EcommerceCore\Modules\Product\Models\Product; // Assuming relationship

use MyPlatform\EcommerceCore\Contracts\ModuleInterface;

class Order extends Model implements ModuleInterface
{
    protected $fillable = [
        'user_id',
        'status', // pending, paid, shipped, cancelled
        'total_amount',
        'currency',
        'shipping_address',
        'payment_method',
        'transaction_id',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'shipping_address' => 'array',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    // ModuleInterface Implementation

    public static function getModuleName(): string
    {
        return 'Orders';
    }

    public static function getModuleFields(): array
    {
        return [
            'status' => 'select:pending,processing,completed,cancelled',
            'total_amount' => 'money',
            'currency' => 'text',
            'payment_method' => 'text',
            'created_at' => 'date',
        ];
    }
}
