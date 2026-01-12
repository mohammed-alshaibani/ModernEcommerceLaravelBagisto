<?php

use Illuminate\Contracts\Console\Kernel;
use MyPlatform\EcommerceCore\Modules\Analytics\Models\Visit;
use MyPlatform\EcommerceCore\Modules\Analytics\Models\Conversion;
use MyPlatform\EcommerceCore\Modules\Analytics\Services\AnalyticsService;
use MyPlatform\EcommerceCore\Modules\Order\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

echo "--- Verifying Analytics Engine ---\n";

$analyticsService = app(\MyPlatform\EcommerceCore\Modules\Analytics\Services\AnalyticsService::class);

// 1. Simulate Visit
echo "\n[1] Simulating Visit:\n";
$request = Request::create('http://127.0.0.1:8000/products/test', 'GET');
$request->headers->set('User-Agent', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1');

$analyticsService->trackVisit($request);
$visit = Visit::latest('id')->first();

if ($visit && $visit->device_type === 'mobile') {
    echo "✅ SUCCESS: Visit tracked correctly (Mobile detected).\n";
} else {
    echo "❌ FAILURE: Visit not tracked correctly. Device: " . ($visit->device_type ?? 'none') . "\n";
}

// 2. Simulate Event
echo "\n[2] Simulating Custom Event:\n";
$analyticsService->trackEvent('add_to_cart', ['product_id' => 45]);
$event = \MyPlatform\EcommerceCore\Modules\Analytics\Models\AnalyticEvent::latest('id')->first();

if ($event && $event->event_name === 'add_to_cart' && ($event->event_data['product_id'] ?? 0) == 45) {
    echo "✅ SUCCESS: Custom event tracked correctly.\n";
} else {
    echo "❌ FAILURE: Event data mismatch.\n";
}

// 3. Simulate Conversion
echo "\n[3] Simulating Conversion:\n";
$order = Order::first();
if (!$order) {
    $order = Order::create(['total_amount' => 1500, 'status' => 'paid', 'currency' => 'SAR']);
}

$analyticsService->trackConversion($order, 'google');
$conversion = Conversion::latest('id')->first();

if ($conversion && $conversion->amount == $order->total_amount && $conversion->source === 'google') {
    echo "✅ SUCCESS: Conversion tracked correctly.\n";
} else {
    echo "❌ FAILURE: Conversion mismatch.\n";
}

echo "\n--- Verification Complete ---\n";
