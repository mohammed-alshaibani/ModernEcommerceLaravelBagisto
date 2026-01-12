<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Modules\Order\Repositories;

use MyPlatform\EcommerceCore\Repositories\BaseRepository;
use MyPlatform\EcommerceCore\Modules\Order\Models\Order;

class OrderRepository extends BaseRepository
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    public function createOrderWithItems(array $orderData, array $items): Order
    {
        // Transaction logic should be ideally handled by Service or here
        $order = $this->create($orderData);
        
        foreach ($items as $item) {
            $order->items()->create($item);
        }

        return $order;
    }
}
