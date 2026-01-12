<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Modules\Delivery\Services;

class ShippingRateService
{
    public function __construct(
        protected ShippingStrategyInterface $strategy
    ) {}

    public function getRate(float $weight, string $city, bool $isFlatRate = false): float
    {
        if ($isFlatRate) {
            return 25.0; // Fixed flat rate
        }

        return $this->strategy->calculateRate($weight, $city);
    }
}
