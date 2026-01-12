<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Modules\Payment\Strategies;

use MyPlatform\EcommerceCore\Modules\Payment\Contracts\PaymentProviderInterface;
use Illuminate\Support\Facades\Http;
use Exception;

class MoyasarPaymentStrategy implements PaymentProviderInterface
{
    public function charge(float $amount, array $details): bool
    {
        // Simulate Moyasar API call
        // In production: Http::withBasicAuth(config('services.moyasar.key'), '')...
        
        if ($amount <= 0) {
            throw new Exception("Invalid amount for Moyasar");
        }

        // Mock success
        return true;
    }
}
