<?php

namespace App\Services\DeliveryFee;

class WeightBasedFeeStrategy implements DeliveryFeeStrategyInterface
{
    public function calculate(float $distance, float $weight, array $options = []): float
    {
        $rate = $options['rate'] ?? 5.0; // Default rate
        return $weight * $rate;
    }
}