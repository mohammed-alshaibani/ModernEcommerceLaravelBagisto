<?php

use Illuminate\Contracts\Console\Kernel;
use MyPlatform\EcommerceCore\Modules\Order\Services\OrderService;
use MyPlatform\EcommerceCore\Modules\Order\Services\CartService;
use MyPlatform\EcommerceCore\Modules\Product\Models\Product;
use MyPlatform\EcommerceCore\Modules\Auth\Services\ProfileService;
use App\Models\User;
use Illuminate\Support\Facades\Event;

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

echo "--- Starting Full Flow Verification ---\n";

// 1. Setup Data
echo "\n[1] Setting up Data:\n";
$user = User::firstOrCreate(
    ['email' => 'test@example.com'],
    ['name' => 'Test User', 'password' => bcrypt('password')]
);
echo "User ID: {$user->id}\n";

$product = Product::create([
    'name' => 'Test Phone',
    'price' => 1000.00,
    'sku' => 'PHONE-' . uniqid(),
    'description' => 'A test phone',
]);
echo "Product Created: {$product->name} ({$product->price} SAR)\n";

// 2. Cart Flow
echo "\n[2] Shopping Cart:\n";
$cartService = new CartService(); // In real app, resolved via container
$cartService->add($product, 2);
$cart = $cartService->getCart();
echo "Cart Items: " . count($cart) . "\n";
echo "Item Quantity: " . $cart[$product->id]['quantity'] . "\n";

// 3. Checkout Flow
echo "\n[3] Checkout Process:\n";
$orderService = app(OrderService::class);
// Mock Session Cart Data passing to Checkout
$cartItems = array_values($cart); // Reformat for service if needed
try {
    $order = $orderService->checkout($user->toArray(), $cartItems, 'stripe');
    echo "✅ Order Created! ID: {$order->id}\n";
    echo "Total Amount: {$order->total_amount}\n";
    echo "Status: {$order->status}\n";
} catch (\Exception $e) {
    echo "❌ Order Creation Failed: " . $e->getMessage() . "\n";
    exit(1);
}

// 4. Payment Flow (Simulated)
echo "\n[4] Payment Simulation:\n";
// Manually firing event to test listener
Event::dispatch(new \MyPlatform\EcommerceCore\Events\PaymentSuccessful($order, 'TXN-123456'));
echo "Payment Event Dispatched.\n";

// 5. Verification
echo "\n[5] Final Verification:\n";
$order->refresh();
// Check if stock decremented (logic was stubbed but listener fired)
echo "Order Status (Manual Update check needed in real flow): {$order->status}\n";

// Trigger Shipping manually to test strategy
$shipping = app(\MyPlatform\EcommerceCore\Modules\Delivery\Services\ShippingRateService::class);
$rate = $shipping->getRate(10, 'Jeddah');
echo "Shipping Rate for 10kg to Jeddah: {$rate}\n";

echo "\n--- Full Flow Verification Complete ---\n";
