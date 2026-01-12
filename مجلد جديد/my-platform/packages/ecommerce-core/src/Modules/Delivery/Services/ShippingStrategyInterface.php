<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Modules\Delivery\Services;

interface ShippingStrategyInterface
{
    public function calculateRate(float $weight, string $city): float;
    public function createShipment(array $orderData): string;
}
