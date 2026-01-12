<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Listeners;

use MyPlatform\EcommerceCore\Events\OrderCreated;
use Illuminate\Support\Facades\Log;

class SendNotification
{
    public function handle(OrderCreated $event): void
    {
        $order = $event->order;
        // Logic to send Email/SMS
        Log::info("Order Confirmation sent to User ID: {$order->user_id}");
    }
}
