<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Modules\Delivery\Strategies;

use MyPlatform\EcommerceCore\Modules\Delivery\Services\ShippingStrategyInterface;

class SMSAShippingStrategy implements ShippingStrategyInterface
{
    public function calculateRate(float $weight, string $city): float
    {
        // SMSA Tiered Pricing
        if ($weight <= 5) {
            return 45.0; // Base rate
        }
        
        return 45.0 + (($weight - 5) * 5.0); // 5 SAR per extra kg
    }

    public function createShipment(array $orderData): string
    {
        return 'SMSA-' . strtoupper(uniqid());
    }
}
