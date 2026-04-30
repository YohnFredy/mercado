<?php

namespace App\Livewire\Checkout;

use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Confirmación de Pedido')]
class CheckoutConfirmation extends Component
{
    public Order $order;

    public function mount(Order $order): void
    {
        $this->order = $order->load('items');
    }

    public function render()
    {
        return view('livewire.checkout.checkout-confirmation');
    }
}
