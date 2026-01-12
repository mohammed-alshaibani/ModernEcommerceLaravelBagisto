<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Modules\Product\Services;

use MyPlatform\EcommerceCore\Modules\Product\Models\Product;
use MyPlatform\EcommerceCore\Modules\Product\Models\ProductVariant;
use MyPlatform\EcommerceCore\Modules\Product\Models\ProductAttribute;
use MyPlatform\EcommerceCore\Modules\Product\Models\ProductAttributeValue;
use Illuminate\Support\Facades\DB;

class ProductVariantService
{
    /**
     * Generate all possible variants from attribute combinations
     *
     * @param Product $product
     * @param array $attributeData Format: ['Color' => ['Red', 'Blue'], 'Size' => ['M', 'L']]
     * @return array Created variants
     */
    public function generateVariants(Product $product, array $attributeData): array
    {
        $combinations = $this->generateCombinations($attributeData);
        $createdVariants = [];

        DB::beginTransaction();
        try {
            foreach ($combinations as $combination) {
                $variant = $this->createVariantFromCombination($product, $combination);
                $createdVariants[] = $variant;
            }

            DB::commit();
            return $createdVariants;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Create a single variant from attribute combination
     */
    protected function createVariantFromCombination(Product $product, array $combination): ProductVariant
    {
        // Generate SKU from product SKU + attributes
        $sku = $this->generateVariantSku($product, $combination);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => $sku,
            'price' => $product->price,
            'stock' => 0,
            'options' => $combination,
        ]);

        // Attach attribute values
        foreach ($combination as $attributeName => $valueName) {
            $attribute = ProductAttribute::where('name', $attributeName)->first();
            if ($attribute) {
                $attributeValue = ProductAttributeValue::firstOrCreate([
                    'attribute_id' => $attribute->id,
                    'value' => $valueName,
                ]);

                $variant->attributeValues()->attach($attributeValue->id);
            }
        }

        return $variant;
    }

    /**
     * Generate all combinations from attribute data
     */
    protected function generateCombinations(array $attributes): array
    {
        if (empty($attributes)) {
            return [[]];
        }

        $result = [[]];

        foreach ($attributes as $attributeName => $values) {
            $temp = [];
            foreach ($result as $combination) {
                foreach ($values as $value) {
                    $newCombination = $combination;
                    $newCombination[$attributeName] = $value;
                    $temp[] = $newCombination;
                }
            }
            $result = $temp;
        }

        return $result;
    }

    /**
     * Generate unique SKU for variant
     */
    protected function generateVariantSku(Product $product, array $combination): string
    {
        $baseSku = $product->sku ?? 'PROD-' . $product->id;
        $suffix = implode('-', array_map(function ($value) {
            return strtoupper(substr($value, 0, 3));
        }, $combination));

        return "{$baseSku}-{$suffix}";
    }

    /**
     * Update stock for a specific variant
     */
    public function updateStock(int $variantId, int $quantity, string $operation = 'set'): ProductVariant
    {
        $variant = ProductVariant::findOrFail($variantId);

        switch ($operation) {
            case 'set':
                $variant->stock = $quantity;
                break;
            case 'increase':
                $variant->increaseStock($quantity);
                break;
            case 'decrease':
                $variant->decreaseStock($quantity);
                break;
        }

        $variant->save();

        // Fire low stock event if needed
        if ($variant->stock <= 5) {
            event(new \MyPlatform\EcommerceCore\Events\LowStockAlert($variant));
        }

        return $variant->fresh();
    }

    /**
     * Check if variant has available stock
     */
    public function checkAvailability(int $variantId, int $requestedQuantity = 1): bool
    {
        $variant = ProductVariant::find($variantId);

        if (!$variant) {
            return false;
        }

        return $variant->hasStock($requestedQuantity);
    }

    /**
     * Get all variants for a product with their attribute values
     */
    public function getProductVariants(int $productId): \Illuminate\Database\Eloquent\Collection
    {
        return ProductVariant::where('product_id', $productId)
            ->with(['attributeValues.attribute'])
            ->get();
    }
}
