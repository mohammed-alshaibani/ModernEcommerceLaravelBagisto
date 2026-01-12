<?php

namespace App\Services\DeliveryFee;

interface DeliveryFeeStrategyInterface
{
    public function calculate(float $distance, float $weight, array $options = []): float;
}