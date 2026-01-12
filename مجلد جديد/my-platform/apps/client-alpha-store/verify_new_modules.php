<?php

use Illuminate\Contracts\Console\Kernel;
use MyPlatform\EcommerceCore\Modules\Integration\Models\IntegrationConfig;
use MyPlatform\EcommerceCore\Modules\Customer\Services\CustomerRewardService;
use MyPlatform\EcommerceCore\Modules\Customer\Models\MembershipLevel;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use MyPlatform\EcommerceCore\Events\PaymentSuccessful;
use MyPlatform\EcommerceCore\Modules\Order\Models\Order;

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

echo "--- Verifying New Modules ---\n";

// 1. Integration Service/Config Verification
echo "\n[1] Integration Config Verification:\n";
$config = IntegrationConfig::updateOrCreate(
    ['module' => 'payment', 'provider' => 'stripe', 'key_name' => 'secret_key'],
    ['encrypted_value' => 'sk_test_123', 'is_active' => true]
);
echo "Config Created/Updated.\n";
$decrypted = $config->encrypted_value;
if ($decrypted === 'sk_test_123') {
    echo "✅ SUCCESS: Encryption/Decryption works.\n";
} else {
    echo "❌ FAILURE: Encryption/Decryption mismatch. Got: $decrypted\n";
}

// 2. Loyalty & Membership Verification
echo "\n[2] Loyalty & Membership Verification:\n";
$silverLevel = MembershipLevel::firstOrCreate(
    ['slug' => 'silver'],
    ['name' => 'Silver', 'min_points' => 10, 'discount_percentage' => 5]
);
echo "Membership Level 'Silver' Created.\n";

$email = 'loyalty' . uniqid() . '@example.com';
$user = User::create(['email' => $email, 'name' => 'Loyalty User', 'password' => 'secret']);
$rewardService = app(CustomerRewardService::class);
$rewardService->addPoints($user, 15);
echo "Added 15 points to user.\n";

$user->load('loyalty.membershipLevel');
echo "User Points: " . ($user->loyalty->points_balance ?? 0) . "\n";
echo "User Level: " . ($user->loyalty->membershipLevel->name ?? 'None') . "\n";

if ($user->loyalty->points_balance === 15 && $user->loyalty->membership_level_id === $silverLevel->id) {
    echo "✅ SUCCESS: Points and Level upgrade work.\n";
} else {
    echo "❌ FAILURE: Loyalty logic mismatch.\n";
}

// 3. Loyalty Listener Verification
echo "\n[3] Loyalty Listener Verification:\n";
$order = new Order();
$order->user_id = $user->id;
$order->total_amount = 100; // Should award 10 points
$order->status = 'paid';
$order->save();

Event::dispatch(new PaymentSuccessful($order, 'TXN-LOYALTY'));
echo "PaymentSuccessful Event Dispatched for 100 SAR order.\n";

$user->loyalty()->first()->refresh();
echo "New Points Balance: " . $user->loyalty->points_balance . "\n";

if ($user->loyalty->points_balance === 25) {
    echo "✅ SUCCESS: Listener awarded points correctly.\n";
} else {
    echo "❌ FAILURE: Listener didn't award points correctly.\n";
}

echo "\n--- Verification Complete ---\n";
