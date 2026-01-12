<?php

use Illuminate\Contracts\Console\Kernel;
use MyPlatform\EcommerceCore\Modules\Product\Repositories\Contracts\ProductRepositoryInterface;
use MyPlatform\EcommerceCore\Modules\Auth\Services\OTPService;
use MyPlatform\EcommerceCore\Modules\Delivery\Services\ShippingStrategyInterface;
use MyPlatform\EcommerceCore\Services\Branding\BrandManager;

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

echo "--- Starting Functional Verification ---\n";

// 1. DI Binding Check
echo "\n[1] DI Binding Verification:\n";
$repo = app(ProductRepositoryInterface::class);
echo "Bound Repository Class: " . get_class($repo) . "\n";
if (get_class($repo) === 'App\Repositories\CustomProductRepository') {
    echo "✅ SUCCESS: CustomProductRepository is correctly swapped.\n";
} else {
    echo "❌ FAILURE: Expected CustomProductRepository, got " . get_class($repo) . "\n";
}

// 2. OTP Check
echo "\n[2] OTP Service Verification:\n";
$otpService = app(OTPService::class);
$identifier = 'user@example.com';
$otp = $otpService->generate($identifier);
echo "Generated OTP: $otp\n";
$isValid = $otpService->verify($identifier, $otp);
if ($isValid) {
    echo "✅ SUCCESS: OTP verified successfully.\n";
} else {
    echo "❌ FAILURE: OTP verification failed.\n";
}

// 3. Shipping Strategy Check
echo "\n[3] Shipping Strategy Verification:\n";
$shipping = app(ShippingStrategyInterface::class);
echo "Strategy Class: " . get_class($shipping) . "\n";
$rate = $shipping->calculateRate(5.0, 'Riyadh');
echo "Calculated Rate (5kg, Riyadh): $rate\n";
if ($rate > 0) {
    echo "✅ SUCCESS: Shipping rate calculated.\n";
} else {
    echo "❌ FAILURE: Shipping rate calculation failed.\n";
}

// 4. Branding Check
echo "\n[4] Branding Logic Verification:\n";
$brandManager = app(BrandManager::class);
echo "Brand Name: " . $brandManager->getBrandName() . "\n";
echo "Font: " . $brandManager->getFontFamily() . "\n";
echo "Primary Color (500): " . $brandManager->getPrimaryColor()[500] . "\n";

if ($brandManager->getBrandName() === 'Alpha Store (Arabic)' && $brandManager->getFontFamily() === 'Almarai') {
    echo "✅ SUCCESS: Brand Config correctly loaded.\n";
} else {
    echo "❌ FAILURE: Brand Config mismatch.\n";
}

echo "\n--- Verification Complete ---\n";
