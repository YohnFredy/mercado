<div class="max-w-5xl mx-auto sm:px-6 lg:px-8 py-8 sm:py-12">

    <!-- Header -->
    <div class="mb-8">
        <!-- Progress Steps -->
        <div class="flex items-center justify-center gap-2 sm:gap-4 mb-8">
            <div class="flex items-center gap-2">
                <div class="size-9 rounded-full bg-primary text-white flex items-center justify-center font-black text-sm shadow-lg shadow-primary/30">1</div>
                <span class="text-sm font-bold text-primary hidden sm:inline">Resumen</span>
            </div>
            <div class="w-8 sm:w-16 h-0.5 bg-gray-200 rounded-full"></div>
            <div class="flex items-center gap-2">
                <div class="size-9 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center font-black text-sm">2</div>
                <span class="text-sm font-bold text-gray-400 hidden sm:inline">Envío</span>
            </div>
            <div class="w-8 sm:w-16 h-0.5 bg-gray-200 rounded-full"></div>
            <div class="flex items-center gap-2">
                <div class="size-9 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center font-black text-sm">3</div>
                <span class="text-sm font-bold text-gray-400 hidden sm:inline">Confirmación</span>
            </div>
        </div>

        <h1 class="text-2xl sm:text-3xl font-black text-primary tracking-tight text-center">Resumen del Pedido</h1>
        <p class="text-gray-500 font-medium text-center mt-1">Revisa tus productos antes de continuar</p>
    </div>

    @if ($count > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Left: Cart Items -->
            <div class="lg:col-span-2 space-y-4">
                @foreach ($items as $item)
                    <div wire:key="item-{{ $item['id'] }}" class="bg-white rounded-2xl shadow-md shadow-gray-200/60 border border-gray-100 p-4 sm:p-5 flex gap-4 sm:gap-6 group hover:border-primary/20 transition-all duration-300">

                        <!-- Image -->
                        <div class="relative size-20 sm:size-24 shrink-0 bg-gray-50 rounded-xl border border-gray-100 flex items-center justify-center p-2 overflow-hidden">
                            <img src="{{ $item['image'] ?? 'https://exitocol.vtexassets.com/arquivos/ids/28955924/Arroz-Diana-500-gr-445117_a.jpg?v=638864002494600000' }}"
                                 alt="{{ $item['title'] }}"
                                 class="max-h-full max-w-full object-contain">
                        </div>

                        <!-- Info -->
                        <div class="flex-1 min-w-0 flex flex-col justify-between">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="text-sm sm:text-base font-bold text-gray-900 leading-tight line-clamp-2">{{ $item['title'] }}</h3>
                                <button wire:click="removeItem({{ $item['id'] }})"
                                    class="p-1.5 hover:bg-red-50 text-gray-300 hover:text-acento rounded-lg transition-colors shrink-0 cursor-pointer">
                                    <flux:icon.trash class="size-4" />
                                </button>
                            </div>

                            <div class="flex flex-wrap items-end justify-between gap-3 mt-3">
                                <!-- Quantity -->
                                <div class="flex items-center bg-gray-100 rounded-xl p-0.5 border border-gray-200">
                                    <button wire:click="updateQuantity({{ $item['id'] }}, {{ max(1, $item['quantity'] - 1) }})"
                                        class="size-8 flex items-center justify-center bg-white rounded-lg shadow-sm border border-gray-200 text-gray-700 hover:text-primary active:scale-95 transition-all cursor-pointer">
                                        <flux:icon.minus class="size-3.5" />
                                    </button>
                                    <span class="w-10 text-center text-sm font-black text-gray-900">{{ $item['quantity'] }}</span>
                                    <button wire:click="updateQuantity({{ $item['id'] }}, {{ min(999, $item['quantity'] + 1) }})"
                                        class="size-8 flex items-center justify-center bg-white rounded-lg shadow-sm border border-gray-200 text-gray-700 hover:text-primary active:scale-95 transition-all cursor-pointer">
                                        <flux:icon.plus class="size-3.5" />
                                    </button>
                                </div>

                                <!-- Price -->
                                <div class="text-right">
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">${{ number_format($item['price'], 0, ',', '.') }} c/u</p>
                                    <p class="text-lg font-black text-primary tracking-tighter">${{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Back to store -->
                <a href="{{ route('home') }}" wire:navigate class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-primary transition-colors mt-4">
                    <flux:icon.arrow-left class="size-4" />
                    Seguir Comprando
                </a>
            </div>

            <!-- Right: Summary Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-xl shadow-primary/5 border border-primary/10 p-6 sm:p-8 sticky top-8 space-y-6">
                    <h2 class="font-black text-lg text-primary">Resumen de Compra</h2>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 font-medium">Productos ({{ $count }})</span>
                            <span class="font-bold text-gray-900">${{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 font-medium flex items-center gap-1.5">
                                Envío
                                <flux:badge size="sm" color="green" class="text-[9px] uppercase font-black">Gratis</flux:badge>
                            </span>
                            <span class="font-bold text-secondary">$0</span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100">
                        <div class="flex justify-between items-end">
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total</p>
                                <p class="text-2xl sm:text-3xl font-black text-primary tracking-tighter">${{ number_format($total, 0, ',', '.') }}</p>
                            </div>
                            @if($totalPts > 0)
                            <div class="text-right">
                                <p class="text-[10px] font-black text-secondary uppercase tracking-widest">Puntos</p>
                                <p class="text-xl sm:text-2xl font-black text-secondary tracking-tighter">+{{ number_format($totalPts, 2, ',', '.') }} pts</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <button wire:click="proceedToShipping"
                        class="w-full bg-primary hover:bg-primary/95 text-white py-4 rounded-2xl font-black text-sm uppercase tracking-widest transition-all shadow-xl shadow-primary/25 active:scale-[0.98] flex items-center justify-center gap-3 cursor-pointer">
                        <flux:icon.credit-card variant="solid" class="size-5" />
                        Iniciar Pago
                    </button>

                    <div class="flex items-center justify-center gap-4 pt-2">
                        <div class="flex flex-col items-center gap-1 opacity-40">
                            <flux:icon.shield-check variant="solid" class="size-5" />
                            <span class="text-[8px] font-bold uppercase tracking-wider">Seguro</span>
                        </div>
                        <div class="w-px h-6 bg-gray-200"></div>
                        <div class="flex flex-col items-center gap-1 opacity-40">
                            <flux:icon.truck variant="solid" class="size-5" />
                            <span class="text-[8px] font-bold uppercase tracking-wider">Envío Gratis</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Empty Cart -->
        <div class="py-20 text-center bg-white rounded-3xl shadow-xl border border-dashed border-gray-200">
            <flux:icon.shopping-cart class="size-20 text-gray-200 mx-auto mb-6" />
            <h2 class="text-2xl font-black text-gray-900 mb-2">Tu carrito está vacío</h2>
            <p class="text-gray-500 mb-8 max-w-xs mx-auto">Agrega algunos productos para continuar con tu compra.</p>
            <a href="{{ route('home') }}" wire:navigate
                class="inline-flex items-center gap-2 bg-primary hover:bg-primary/95 text-white px-8 py-3 rounded-xl font-black text-sm uppercase tracking-widest transition-all shadow-lg shadow-primary/20 active:scale-[0.98]">
                <flux:icon.shopping-bag variant="solid" class="size-5" />
                Ir a la Tienda
            </a>
        </div>
    @endif
</div>
