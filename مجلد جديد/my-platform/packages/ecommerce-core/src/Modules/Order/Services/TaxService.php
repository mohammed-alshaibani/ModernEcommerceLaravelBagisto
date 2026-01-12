<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Modules\Order\Services;

class TaxService
{
    public function calculate(float $amount): float
    {
        $taxRate = config('ecommerce.tax_rate', 0.15);
        return $amount * $taxRate;
    }
}
