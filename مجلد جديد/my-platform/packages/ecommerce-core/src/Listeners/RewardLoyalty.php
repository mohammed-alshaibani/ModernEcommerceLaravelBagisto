<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Listeners;

use MyPlatform\EcommerceCore\Events\PaymentSuccessful;
use MyPlatform\EcommerceCore\Modules\Customer\Services\CustomerRewardService;

class RewardLoyalty
{
    public function __construct(
        protected CustomerRewardService $rewardService
    ) {}

    public function handle(PaymentSuccessful $event): void
    {
        $order = $event->order;
        $user = $order->user;

        if ($user) {
            // Reward 1 point for every 10 SAR spent
            $points = (int) ($order->total_amount / 10);
            if ($points > 0) {
                $this->rewardService->addPoints($user, $points);
            }
        }
    }
}
