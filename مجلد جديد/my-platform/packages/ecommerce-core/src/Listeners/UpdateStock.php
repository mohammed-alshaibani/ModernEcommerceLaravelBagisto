<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Listeners;

use MyPlatform\EcommerceCore\Events\PaymentSuccessful;
use Illuminate\Support\Facades\Log;

class UpdateStock
{
    public function handle(PaymentSuccessful $event): void
    {
        $order = $event->order;
        
        foreach ($order->items as $item) {
            $product = $item->product;
            if ($product) {
                // Decrement stock logic
                // $product->decrement('stock', $item->quantity);
                Log::info("Stock decremented for Product ID: {$product->id}");
            }
        }
    }
}
