<?php

namespace App\Services;

use App\Services\DeliveryFee\DeliveryFeeStrategyInterface;
use Illuminate\Support\Facades\App;

class DeliveryFeeCalculator
{
    private $strategy;

    public function __construct(DeliveryFeeStrategyInterface $strategy = null)
    {
        if ($strategy === null) {
            $defaultStrategy = config('delivery.default_strategy');
            $strategyConfig = config('delivery.strategies.' . $defaultStrategy);

            if ($strategyConfig) {
                 $this->strategy = App::make($strategyConfig['class']); // Resolving class from service container
            } else {
                throw new \Exception("Default delivery strategy not configured correctly.");
            }
        } else {
             $this->strategy = $strategy;
        }
    }

    public function setStrategy(DeliveryFeeStrategyInterface $strategy): void
    {
        $this->strategy = $strategy;
    }

    public function calculate(float $distance, float $weight): float
    {
        $strategyConfig = [];

        foreach (config('delivery.strategies') as $key => $config){
            $keyArray = explode('\\', $this->strategy::class);
            $className = end($keyArray);
            $configValue = explode('\\', $config['class']);
            $configName = end($configValue);

            if($className == $configName){
                 $strategyConfig = $config;
                 break;
            }
        }

        return $this->strategy->calculate($distance, $weight, $strategyConfig);
    }
}