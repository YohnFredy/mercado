<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Session;

class CartService
{
    private const SESSION_KEY = 'shopping_cart';

    /**
     * Add a product to the cart.
     */
    public function add(Product $product, int $quantity = 1): void
    {
        $cart = $this->getCart();

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $quantity;
        } else {
            $profit = $product->selling_price_incl_vat - $product->cost_price_excl_vat;
            $step1 = $profit * 0.50; // del profit se obtiene el 50%
            $step2 = $step1 / 1.19;  // se le saca el 19% de IVA

            // Formula: $saldo = $subtotal * (1 - 0.05 - 0.32); $pts = round(($saldo / 4000) * 0.30, 2);
            $saldo = $step2 * (1 - 0.05 - 0.32);
            $pts = round(($saldo / 4000) * 0.30, 2);

            $cart[$product->id] = [
                'id' => $product->id,
                'title' => $product->title,
                'price' => $product->selling_price_incl_vat,
                'image' => $product->images->first() ? asset('storage/' . $product->images->first()->path) : null,
                'pts' => max(0, $pts),
                'quantity' => $quantity,
            ];
        }

        Session::put(self::SESSION_KEY, $cart);
    }

    /**
     * Remove a product from the cart.
     */
    public function remove(int $productId): void
    {
        $cart = $this->getCart();

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            Session::put(self::SESSION_KEY, $cart);
        }
    }

    /**
     * Update product quantity.
     */
    public function updateQuantity(int $productId, int $quantity): void
    {
        $cart = $this->getCart();

        if (isset($cart[$productId])) {
            if ($quantity <= 0) {
                unset($cart[$productId]);
            } else {
                $cart[$productId]['quantity'] = $quantity;
            }
            Session::put(self::SESSION_KEY, $cart);
        }
    }

    /**
     * Get all items in the cart.
     */
    public function getItems(): array
    {
        return collect($this->getCart())->values()->toArray();
    }

    /**
     * Get total quantity of items.
     */
    public function getCount(): int
    {
        return collect($this->getCart())->sum('quantity');
    }

    /**
     * Get total price of items.
     */
    public function getTotal(): float
    {
        return collect($this->getCart())->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
    }

    /**
     * Get total points (pts) of items.
     */
    public function getTotalPts(): float
    {
        return collect($this->getCart())->sum(function ($item) {
            return ($item['pts'] ?? 0) * $item['quantity'];
        });
    }

    /**
     * Clear the cart.
     */
    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    /**
     * Retrieve the cart from the session.
     */
    private function getCart(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }
}
