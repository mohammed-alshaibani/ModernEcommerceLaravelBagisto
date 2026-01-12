<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Modules\Order\Services;

use MyPlatform\EcommerceCore\Modules\Order\Repositories\OrderRepository;
use MyPlatform\EcommerceCore\Modules\Order\Models\Order;
use Exception;

class OrderService
{
    public function __construct(
        protected OrderRepository $orderRepository,
        protected \MyPlatform\EcommerceCore\Modules\Payment\Factories\PaymentFactory $paymentFactory,
        protected TaxService $taxService
    ) {}

    public function checkout(array $userData, array $cartItems, string $paymentMethod): Order
    {
        // 1. Calculate Subtotal
        $subtotal = 0;
        $itemsData = [];
        
        foreach ($cartItems as $item) {
            $lineTotal = $item['price'] * $item['quantity'];
            $subtotal += $lineTotal;
            
            $itemsData[] = [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'total' => $lineTotal,
            ];
        }

        // 2. Calculate Tax & Total
        $taxAmount = $this->taxService->calculate($subtotal);
        $total = $subtotal + $taxAmount;

        // 3. Process Payment
        $paymentProvider = $this->paymentFactory->make($paymentMethod);
        $paymentSuccess = $paymentProvider->charge($total, [
            'email' => $userData['email'] ?? 'guest@example.com',
            'description' => 'Order for User ' . ($userData['id'] ?? 'Guest'),
        ]);

        if (!$paymentSuccess) {
            throw new Exception("Payment Failed via {$paymentMethod}");
        }

        // 4. Create Order
        $orderData = [
            'user_id' => $userData['id'] ?? null,
            'status' => 'processing', // Paid
            'total_amount' => $total,
            'currency' => config('ecommerce.currency', 'SAR'),
            'payment_method' => $paymentMethod,
        ];

        $order = $this->orderRepository->createOrderWithItems($orderData, $itemsData);

        // 5. Trigger Events
        event(new \MyPlatform\EcommerceCore\Events\PaymentSuccessful($order, 'TRANS-DUMMY-' . uniqid()));
        // event(new \MyPlatform\EcommerceCore\Events\OrderCreated($order));

        return $order;
    }
}
