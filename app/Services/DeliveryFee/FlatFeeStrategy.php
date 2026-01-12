<?php

namespace App\Services\DeliveryFee;

class FlatFeeStrategy implements DeliveryFeeStrategyInterface
{
    public function calculate(float $distance, float $weight, array $options = []): float
    {
        $amount = $options['amount'] ?? 0.00;
        return $amount; //Returns configured flat amount
    }
}