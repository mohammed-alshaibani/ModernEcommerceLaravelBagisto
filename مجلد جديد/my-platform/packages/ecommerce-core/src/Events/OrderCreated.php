<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MyPlatform\EcommerceCore\Modules\Order\Models\Order;

class OrderCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Order $order
    ) {}
}
