<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Modules\Payment\Contracts;

interface PaymentProviderInterface
{
    public function charge(float $amount, array $details): bool;
}
