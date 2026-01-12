<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Listeners;

use MyPlatform\EcommerceCore\Events\PaymentSuccessful;
use MyPlatform\EcommerceCore\Modules\Analytics\Services\AnalyticsService;

class TrackAnalytics
{
    public function __construct(
        protected AnalyticsService $analyticsService
    ) {}

    public function handle(PaymentSuccessful $event): void
    {
        $this->analyticsService->trackConversion($event->order);
        $this->analyticsService->trackEvent('purchase_completed', [
            'order_id' => $event->order->id,
            'total' => $event->order->total_amount,
        ]);
    }
}
