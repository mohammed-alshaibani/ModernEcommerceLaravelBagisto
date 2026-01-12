<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Modules\Product\Services;

use MyPlatform\EcommerceCore\Modules\Product\Models\Promotion;
use Carbon\Carbon;

class PromotionService
{
    /**
     * Apply promotion code to cart items
     *
     * @param string $code Promotion code
     * @param array $cartItems Array of items with ['product_id', 'quantity', 'price']
     * @return array ['success' => bool, 'discount' => float, 'message' => string]
     */
    public function applyPromotion(string $code, array $cartItems): array
    {
        $promotion = Promotion::where('code', strtoupper($code))->first();

        if (!$promotion) {
            return [
                'success' => false,
                'discount' => 0,
                'message' => 'Promotion code not found',
            ];
        }

        // Validate promotion
        $validation = $this->validatePromotion($promotion, $cartItems);
        if (!$validation['valid']) {
            return [
                'success' => false,
                'discount' => 0,
                'message' => $validation['message'],
            ];
        }

        // Calculate discount
        $cartTotal = $this->calculateCartTotal($cartItems);
        $discount = $promotion->calculateDiscount($cartTotal);

        return [
            'success' => true,
            'discount' => $discount,
            'message' => "Promotion applied successfully",
            'promotion_id' => $promotion->id,
        ];
    }

    /**
     * Validate if promotion can be applied
     */
    public function validatePromotion(Promotion $promotion, array $cartItems): array
    {
        // Check if active
        if (!$promotion->isValid()) {
            return ['valid' => false, 'message' => 'This promotion is not currently active'];
        }

        // Check minimum purchase amount
        $cartTotal = $this->calculateCartTotal($cartItems);
        if ($promotion->min_purchase_amount && $cartTotal < $promotion->min_purchase_amount) {
            return [
                'valid' => false,
                'message' => "Minimum purchase amount of \${$promotion->min_purchase_amount} required",
            ];
        }

        // Check if applies to specific products
        if ($promotion->applies_to === 'products') {
            $productIds = array_column($cartItems, 'product_id');
            $validProducts = $promotion->products()->pluck('id')->toArray();
            
            if (empty(array_intersect($productIds, $validProducts))) {
                return ['valid' => false, 'message' => 'This promotion does not apply to items in your cart'];
            }
        }

        // Check if applies to specific categories
        if ($promotion->applies_to === 'categories') {
            // TODO: Implement category check when categories are added
        }

        return ['valid' => true, 'message' => 'Promotion is valid'];
    }

    /**
     * Get all active promotions
     */
    public function getActivePromotions(): \Illuminate\Database\Eloquent\Collection
    {
        return Promotion::available()->get();
    }

    /**
     * Increment promotion usage count
     */
    public function recordUsage(int $promotionId): void
    {
        $promotion = Promotion::find($promotionId);
        if ($promotion) {
            $promotion->increment('uses_count');
        }
    }

    /**
     * Calculate cart total
     */
    protected function calculateCartTotal(array $cartItems): float
    {
        $total = 0;
        foreach ($cartItems as $item) {
            $total += ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
        }
        return round($total, 2);
    }

    /**
     * Create a new promotion
     */
    public function createPromotion(array $data): Promotion
    {
        return Promotion::create([
            'name' => $data['name'],
            'code' => strtoupper($data['code']),
            'type' => $data['type'],
            'value' => $data['value'],
            'min_purchase_amount' => $data['min_purchase_amount'] ?? null,
            'max_uses' => $data['max_uses'] ?? null,
            'applies_to' => $data['applies_to'] ?? 'all',
            'is_active' => $data['is_active'] ?? true,
            'starts_at' => $data['starts_at'] ?? now(),
            'expires_at' => $data['expires_at'] ?? null,
        ]);
    }
}
