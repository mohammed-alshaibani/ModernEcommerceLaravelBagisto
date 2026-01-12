<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Modules\Customer\Services;

use MyPlatform\EcommerceCore\Modules\Customer\Models\CustomerLoyalty;
use MyPlatform\EcommerceCore\Modules\Customer\Models\MembershipLevel;
use App\Models\User;

class CustomerRewardService
{
    /**
     * Add points to a customer's balance
     */
    public function addPoints(User $user, int $points): void
    {
        $loyalty = CustomerLoyalty::firstOrCreate(
            ['user_id' => $user->id],
            ['points_balance' => 0]
        );

        $loyalty->increment('points_balance', $points);
        
        $this->updateMembershipLevel($loyalty);
    }

    /**
     * Deduct points for a reward
     */
    public function deductPoints(User $user, int $points): bool
    {
        $loyalty = CustomerLoyalty::where('user_id', $user->id)->first();

        if (!$loyalty || $loyalty->points_balance < $points) {
            return false;
        }

        $loyalty->decrement('points_balance', $points);
        return true;
    }

    /**
     * Check and update membership level based on current points
     */
    protected function updateMembershipLevel(CustomerLoyalty $loyalty): void
    {
        $newLevel = MembershipLevel::where('min_points', '<=', $loyalty->points_balance)
            ->orderBy('min_points', 'desc')
            ->first();

        if ($newLevel && $loyalty->membership_level_id !== $newLevel->id) {
            $loyalty->membership_level_id = $newLevel->id;
            $loyalty->save();
        }
    }
}
