<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Modules\Inventory\Listeners;

// use MyPlatform\EcommerceCore\Events\OrderCreated;
use Illuminate\Support\Facades\Log;

class CheckLowStockListener
{
    public function handle(\MyPlatform\EcommerceCore\Events\PaymentSuccessful $event): void
    {
        $order = $event->order;
        
        foreach ($order->items as $item) {
            $lock = \Illuminate\Support\Facades\Cache::lock('stock_check_' . $item->product_id, 10);

            if ($lock->get()) {
                try {
                    // unexpected logic: reload product to get fresh stock
                    // $item->product->refresh(); 
                    
                    $threshold = config('ecommerce.low_stock_threshold', 5);
                    
                    if ($item->product->stock < $threshold) {
                        Log::warning("Low stock alert for product: {$item->product->name} (ID: {$item->product->id})");
                        // Dispatch Notification Event
                    }
                } finally {
                    $lock->release();
                }
            }
        }
    }
}
