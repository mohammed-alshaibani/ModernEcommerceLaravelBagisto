<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Modules\Order\Services;

use Illuminate\Support\Facades\Session;
use MyPlatform\EcommerceCore\Modules\Product\Models\Product;

class CartService
{
    const SESSION_KEY = 'cart_items';

    public function add(Product $product, int $quantity = 1): void
    {
        $cart = $this->getCart();
        $id = $product->id;

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $quantity;
        } else {
            $cart[$id] = [
                'product_id' => $id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $quantity,
            ];
        }

        Session::put(self::SESSION_KEY, $cart);
    }

    public function getCart(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }
}
