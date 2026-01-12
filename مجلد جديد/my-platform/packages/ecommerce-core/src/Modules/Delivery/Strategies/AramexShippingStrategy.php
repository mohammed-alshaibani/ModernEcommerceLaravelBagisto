<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Modules\Delivery\Strategies;

use MyPlatform\EcommerceCore\Modules\Delivery\Services\ShippingStrategyInterface;

class AramexShippingStrategy implements ShippingStrategyInterface
{
    public function calculateRate(float $weight, string $city): float
    {
        // Mock calculation logic for Aramex
        // Base rate 30 + 2 per kg
        return 30.0 + ($weight * 2.0);
    }

    public function createShipment(array $orderData): string
    {
        // Mock API call to Aramex
        return 'ARAMEX-' . uniqid();
    }
}
