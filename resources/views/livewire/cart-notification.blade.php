<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;

new class extends Component {
    public bool $visible = false;
    public string $productName = '';
    public string $productImage = '';
    public float $productPrice = 0;
    public int $quantity = 1;

    #[On('add-to-cart')] 
    public function handleAddToCart(array $payload): void
    {
        $this->productName = $payload['name'] ?? '';
        $this->productImage = $payload['image'] ?? '';
        $this->productPrice = $payload['price'] ?? 0;
        $this->quantity = $payload['quantity'] ?? 1;
        $this->visible = true;
    }

    public function dismiss(): void
    {
        $this->visible = false;
    }
}; ?>

<script>
    (function() {
        const registerCartNotification = () => {
            if (window.cartNotificationRegistered || !window.Alpine) return;
            window.cartNotificationRegistered = true;

            Alpine.data('cartNotification', (entangledVisible) => ({
                show: entangledVisible,
                productName: '',
                productImage: '',
                productPrice: 0,
                quantity: 1,
                timer: null,
                progressTimer: null,

                init() {
                    this.$watch('show', (value) => {
                        if (value) {
                            this.startAutoClose();
                        } else {
                            this.stopTimers();
                        }
                    });

                    // Listener para capturar el evento y actualizar datos instantáneamente
                    window.addEventListener('add-to-cart', (event) => {
                        const payload = Array.isArray(event.detail) ? event.detail[0] : event.detail;
                        
                        this.productName = payload.name || '';
                        this.productImage = payload.image || '';
                        this.productPrice = payload.price || 0;
                        this.quantity = payload.quantity || 1;
                        
                        this.show = true;
                    });

                    if (this.show) {
                        this.startAutoClose();
                    }
                },

                formatCurrency(value) {
                    return '$' + new Intl.NumberFormat('de-DE').format(Math.round(value));
                },

                stopTimers() {
                    clearTimeout(this.timer);
                    clearInterval(this.progressTimer);
                },

                startAutoClose() {
                    this.stopTimers();

                    const duration = 4000;
                    let elapsed = 0;
                    const interval = 50;

                    if (this.$refs.progressBar) {
                        this.$refs.progressBar.style.width = '100%';
                        this.$refs.progressBar.style.transition = 'none';
                    }

                    this.progressTimer = setInterval(() => {
                        elapsed += interval;
                        const pct = Math.max(0, 100 - (elapsed / duration) * 100);
                        if (this.$refs.progressBar) {
                            this.$refs.progressBar.style.width = pct + '%';
                        }
                        if (elapsed >= duration) {
                            clearInterval(this.progressTimer);
                        }
                    }, interval);

                    this.timer = setTimeout(() => {
                        this.show = false;
                    }, duration);
                },
            }));
        };

        if (window.Alpine) {
            registerCartNotification();
        } else {
            document.addEventListener('alpine:init', registerCartNotification);
        }
    })();
</script>

<div
    x-data="cartNotification(@entangle('visible'))"
    x-show="show"
    x-cloak
    x-transition:enter="transition ease-out duration-400"
    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:translate-x-8"
    x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
    x-transition:leave="transition ease-in duration-250"
    x-transition:leave-start="opacity-100 translate-y-0 sm:translate-x-0"
    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:translate-x-8"
    class="fixed bottom-24 left-4 right-4 sm:bottom-8 sm:left-auto sm:right-6 sm:w-[380px] z-[9999] pointer-events-none"
    aria-live="polite"
    aria-atomic="true">
    <div class="pointer-events-auto bg-white rounded-3xl shadow-xl shadow-black border border-green-400 overflow-hidden">

        {{-- Progress bar --}}
        <div class="h-1 bg-gray-100 relative overflow-hidden">
            <div
                x-ref="progressBar"
                class="h-full bg-primary absolute left-0 top-0"
                style="width: 100%"></div>
        </div>

        <div class="p-4 flex items-start gap-4">

            {{-- Success icon + product image --}}
            <div class="relative shrink-0">
                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gray-50 rounded-2xl border border-gray-100 overflow-hidden flex items-center justify-center">
                    <template x-if="productImage">
                        <img
                            :src="productImage"
                            :alt="productName"
                            class="w-full h-full object-contain p-1.5 mix-blend-multiply"
                        >
                    </template>
                    <template x-if="!productImage">
                        <flux:icon.shopping-bag class="size-8 text-gray-300" />
                    </template>
                </div>
                <div class="absolute -top-1.5 -right-1.5 bg-primary text-white rounded-full w-5 h-5 flex items-center justify-center shadow-md shadow-primary/40">
                    <flux:icon.check class="size-3" style="stroke-width: 3" />
                </div>
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0 pt-0.5">
                <p class="text-[10px] font-black text-primary uppercase tracking-widest mb-0.5">
                    ¡Agregado al carrito!
                </p>
                <p x-text="productName" class="text-sm font-bold text-gray-900 leading-snug line-clamp-2 mb-2">
                    Cargando...
                </p>
                <div class="flex items-center gap-2 flex-wrap">
                    <span x-text="quantity + (quantity === 1 ? ' unidad' : ' unidades')" class="text-xs font-black text-gray-500">
                    </span>
                    <span class="text-gray-200 text-xs">·</span>
                    <span x-text="formatCurrency(productPrice * quantity)" class="text-sm font-black text-gray-900 tracking-tighter">
                    </span>
                </div>
            </div>

            {{-- Close --}}
            <button
                @click="show = false"
                class="shrink-0 w-7 h-7 flex items-center justify-center rounded-full bg-gray-50 hover:bg-gray-100 text-gray-400 hover:text-gray-700 transition-colors cursor-pointer"
                aria-label="Cerrar">
                <flux:icon.x-mark class="size-4" />
            </button>
        </div>

        {{-- CTA Footer --}}
        <div class="px-4 pb-4">
            <button
                @click="Flux.modal('cart-flyout').show(); show = false"
                class="w-full bg-primary hover:bg-primary/90 text-white py-3 rounded-2xl font-black text-xs uppercase tracking-widest transition-all shadow-lg shadow-primary/25 active:scale-[0.98] flex items-center justify-center gap-2 cursor-pointer">
                <flux:icon.shopping-cart variant="solid" class="size-4" />
                Ver Carrito
            </button>
        </div>
    </div>
</div>