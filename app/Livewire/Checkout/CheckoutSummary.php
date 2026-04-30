<?php

namespace App\Livewire\Checkout;

use App\Services\CartService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Resumen del Pedido')]
class CheckoutSummary extends Component
{
    public function updateQuantity(int $productId, int $quantity, CartService $cart): void
    {
        $cart->updateQuantity($productId, $quantity);
        $this->dispatch('cart-updated');
    }

    public function removeItem(int $productId, CartService $cart): void
    {
        $cart->remove($productId);
        $this->dispatch('cart-updated');

        if ($cart->getCount() === 0) {
            $this->redirectRoute('home', navigate: true);
        }
    }

    public function proceedToShipping(CartService $cart): void
    {
        if ($cart->getCount() === 0) {
            $this->redirectRoute('home', navigate: true);

            return;
        }

        $this->redirectRoute('checkout.shipping', navigate: true);
    }

    public function render(CartService $cart)
    {
        return view('livewire.checkout.checkout-summary', [
            'items' => $cart->getItems(),
            'total' => $cart->getTotal(),
            'totalPts' => $cart->getTotalPts(),
            'count' => $cart->getCount(),
        ]);
    }
}
