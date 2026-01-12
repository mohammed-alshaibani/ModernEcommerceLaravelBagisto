<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Modules\Payment\Factories;

use MyPlatform\EcommerceCore\Modules\Payment\Contracts\PaymentProviderInterface;
use MyPlatform\EcommerceCore\Modules\Payment\Strategies\StripePaymentStrategy;
use MyPlatform\EcommerceCore\Modules\Payment\Strategies\MoyasarPaymentStrategy;
use InvalidArgumentException;

class PaymentFactory
{
    public function __construct(
        protected \MyPlatform\EcommerceCore\Modules\Integration\Services\IntegrationService $integrationService
    ) {}

    public function make(?string $driver = null): PaymentProviderInterface
    {
        $driver = $driver ?? $this->integrationService->getActiveProvider('payment');

        if (!$driver) {
            throw new InvalidArgumentException("No active payment provider configured.");
        }

        return match ($driver) {
            'stripe' => new StripePaymentStrategy(),
            'moyasar' => new MoyasarPaymentStrategy(),
            default => throw new InvalidArgumentException("Unsupported payment driver: {$driver}"),
        };
    }
}
