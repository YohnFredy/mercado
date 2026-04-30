<?php

use Livewire\Volt\Component;
use App\Services\CartService;
use Livewire\Attributes\On;

new class extends Component {
    public array $items = [];
    public float $total = 0;
    public float $totalPts = 0;
    public int $count = 0;

    public function mount(CartService $cart)
    {
        $this->loadCart($cart);
    }

    #[On('cart-updated')]
    public function loadCart(CartService $cart)
    {
        $this->items = $cart->getItems();
        $this->total = $cart->getTotal();
        $this->totalPts = $cart->getTotalPts();
        $this->count = $cart->getCount();
    }

    public function removeItem(int $productId, CartService $cart)
    {
        $cart->remove($productId);
        $this->loadCart($cart);
        $this->dispatch('cart-updated');
    }

    public function updateQuantity(int $productId, int $quantity, CartService $cart)
    {
        $cart->updateQuantity($productId, $quantity);
        $this->loadCart($cart);
        $this->dispatch('cart-updated');
    }
}; ?>

<div class="ml-3">


    <flux:modal.trigger name="cart-flyout">
        <div class="text-3xl relative px-3 cursor-pointer hover:scale-105 transition-transform">
            <i class="fas fa-cart-arrow-down text-gray-900 pt-2"></i>

            @if ($count > 0)
            <div
                class="absolute -right-2 -top-1 bg-acento border-2 border-secondary w-7 h-7 rounded-full text-xs font-black flex items-center justify-center text-white shadow-md animate-bounce">
                {{ $count > 99 ? '99+' : $count }}
            </div>
            @endif
        </div>
    </flux:modal.trigger>

    <flux:modal name="cart-flyout" flyout variant="floating" class="md:w-lg p-0! h-[100dvh]">
        <div class="flex flex-col h-full">

            <!-- Header -->
            <div class="p-5 border-b border-gray-200 flex items-center justify-between bg-white shadow-sm shrink-0">
                <div>
                    <flux:heading size="lg" class="font-black text-gray-900">
                        Carrito de Compras
                    </flux:heading>
                    <flux:text class="text-xs font-bold text-gray-500">
                        {{ $count }} {{ $count === 1 ? 'producto' : 'productos' }}
                    </flux:text>
                </div>
            </div>

            <!-- Body -->
            <div class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-4 custom-scrollbar">
                @forelse($items as $item)
                <div
                    class="flex gap-4 p-3 bg-white border border-gray-100/50 rounded-2xl shadow-sm hover:shadow-md transition-shadow">

                    <!-- Image -->
                    <div
                        class="w-20 h-20 shrink-0 bg-gray-50 rounded-xl border border-gray-100 flex items-center justify-center p-1.5 overflow-hidden">
                        <img src="{{ $item['image'] ?? 'https://exitocol.vtexassets.com/arquivos/ids/28955924/Arroz-Diana-500-gr-445117_a.jpg?v=638864002494600000' }}"
                            alt="{{ $item['title'] }}" class="w-full h-full object-contain mix-blend-multiply">
                    </div>

                    <!-- Info -->
                    <div class="flex-1 flex flex-col justify-between py-1 min-w-0">

                        <div class="flex items-start justify-between gap-2">
                            <h3 class="text-xs sm:text-sm font-bold text-gray-900 leading-tight line-clamp-2">
                                {{ $item['title'] }}
                            </h3>

                            <button wire:click="removeItem({{ $item['id'] }})"
                                class="p-1 hover:bg-red-50 text-gray-400 hover:text-acento rounded-lg transition-colors shrink-0">
                                <flux:icon.trash class="size-4" />
                            </button>
                        </div>

                        <div class="flex items-end justify-between mt-2">

                            <!-- Quantity -->
                            <div class="flex items-center bg-gray-50 rounded-lg p-0.5 border border-gray-200">
                                <button
                                    wire:click="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] - 1 }})"
                                    class="size-6 sm:size-7 flex items-center justify-center bg-white rounded-md shadow-sm text-gray-700 hover:text-primary transition-colors cursor-pointer">
                                    <flux:icon.minus class="size-3" />
                                </button>

                                <span class="w-8 text-center text-xs font-black text-gray-900">
                                    {{ $item['quantity'] }}
                                </span>

                                <button
                                    wire:click="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] + 1 }})"
                                    class="size-6 sm:size-7 flex items-center justify-center bg-white rounded-md shadow-sm text-gray-700 hover:text-primary transition-colors cursor-pointer">
                                    <flux:icon.plus class="size-3" />
                                </button>
                            </div>

                            <!-- Price -->
                            <div class="text-right flex flex-col items-end">
                                <span class="text-sm sm:text-base font-black text-primary tracking-tighter leading-none">
                                    ${{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                </span>
                                @if(isset($item['pts']) && $item['pts'] > 0)
                                <span class="text-[10px] sm:text-xs font-bold text-secondary mt-1 tracking-tight">
                                    +{{ number_format($item['pts'] * $item['quantity'], 2) }} pts
                                </span>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>

                @empty
                <div
                    class="h-full flex flex-col items-center justify-center text-center space-y-4 text-gray-500 opacity-60">
                    <div class="p-6 bg-gray-100 rounded-full shadow-inner">
                        <flux:icon.shopping-cart class="size-16 stroke-1 border-t-0 border-current" />
                    </div>
                    <div>
                        <h3 class="font-black text-gray-800 text-lg">
                            Tu carrito está vacío
                        </h3>
                        <p class="text-sm mt-1">
                            ¡Explora nuestro catálogo y descubre <br> productos increíbles!
                        </p>
                    </div>
                </div>
                @endforelse
            </div>

            <!-- Footer -->
            @if ($count > 0)
            <div class="mt-auto p-5 bg-white border-t border-gray-200 shadow-md shadow-gray-500 shrink-0 space-y-4">
                <div class="flex items-center justify-between text-gray-900">
                    <span class="text-sm font-black uppercase tracking-widest text-gray-500">
                        Total a pagar:
                    </span>
                    <span class="text-2xl font-black text-primary tracking-tighter">
                        ${{ number_format($total, 0, ',', '.') }}
                    </span>
                </div>
                <div class="flex items-center justify-between gap-3 text-gray-900 border border-secondary/30 bg-secondary/5 rounded-xl p-3 shadow-inner">
                    <div class="flex items-center gap-2">
                        <flux:icon.sparkles variant="solid" class="size-5 text-secondary" />
                        <span class="text-xs font-black uppercase tracking-widest text-primary">
                            Puntos a recibir:
                        </span>
                    </div>
                    <span class="text-base font-black text-primary tracking-tighter">
                        +{{ number_format($totalPts, 2) }} pts
                    </span>
                </div>

                <a href="{{ route('checkout') }}" wire:navigate
                    @click="$dispatch('flux:modal:close', { name: 'cart-flyout' })"
                    class="w-full bg-primary hover:bg-primary/95 text-fondo py-4 rounded-xl font-black text-sm uppercase tracking-widest transition-all shadow-xl shadow-primary/20 active:scale-[0.98] flex items-center justify-center gap-3">
                    <flux:icon.check-circle variant="solid" class="size-5" />
                    Continuar con el pago
                </a>
            </div>
            @endif

            <style>
                .custom-scrollbar::-webkit-scrollbar {
                    width: 4px;
                }

                .custom-scrollbar::-webkit-scrollbar-track {
                    background: transparent;
                }

                .custom-scrollbar::-webkit-scrollbar-thumb {
                    background: #d4d4d4;
                    border-radius: 10px;
                }

                .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                    background: #00A63D;
                }
            </style>

        </div>
    </flux:modal>



</div>