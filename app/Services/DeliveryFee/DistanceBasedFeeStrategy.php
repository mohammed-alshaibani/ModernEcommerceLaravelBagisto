<?php

namespace App\Services\DeliveryFee;

class DistanceBasedFeeStrategy implements DeliveryFeeStrategyInterface
{
    public function calculate(float $distance, float $weight, array $options = []): float
    {
        $rate = $options['rate'] ?? 2.0; // Default rate
        return $distance * $rate;
    }
}