<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Modules\Marketing\Services;

use MyPlatform\EcommerceCore\Modules\Marketing\Models\Coupon;
use Exception;

class CouponService
{
    public function applyCoupon(string $code, float $totalAmount): float
    {
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon || !$coupon->isValid()) {
            throw new Exception("Invalid or expired coupon.");
        }

        $discount = 0;
        if ($coupon->type === 'percentage') {
            $discount = $totalAmount * ($coupon->value / 100);
        } else {
            $discount = $coupon->value;
        }

        return max(0, $totalAmount - $discount);
    }
}
