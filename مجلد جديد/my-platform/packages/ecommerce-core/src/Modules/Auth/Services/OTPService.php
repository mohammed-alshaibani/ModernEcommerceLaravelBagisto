<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Modules\Auth\Services;

use Illuminate\Support\Facades\Cache;
use Exception;

class OTPService
{
    public function generate(string $identifier): string
    {
        $otp = (string) random_int(100000, 999999);
        // Cache for 10 minutes
        Cache::put("otp_{$identifier}", $otp, now()->addMinutes(10));
        return $otp;
    }

    public function verify(string $identifier, string $otp): bool
    {
        $cachedOtp = Cache::get("otp_{$identifier}");

        if ($cachedOtp === $otp) {
            Cache::forget("otp_{$identifier}");
            return true;
        }

        return false;
    }
}
