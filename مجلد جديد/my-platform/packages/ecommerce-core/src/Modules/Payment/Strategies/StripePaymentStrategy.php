<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Modules\Payment\Strategies;

use MyPlatform\EcommerceCore\Modules\Payment\Contracts\PaymentProviderInterface;

class StripePaymentStrategy implements PaymentProviderInterface
{
    public function charge(float $amount, array $details): bool
    {
        // Stripe integration logic
        return true;
    }
}
