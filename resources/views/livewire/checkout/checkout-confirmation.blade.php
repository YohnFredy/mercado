<div class="max-w-5xl mx-auto sm:px-6 lg:px-8 py-8 sm:py-12">

    <!-- Progress Steps -->
    <div class="flex items-center justify-center gap-2 sm:gap-4 mb-8">
        <div class="flex items-center gap-2">
            <div class="size-9 rounded-full bg-primary/20 text-primary flex items-center justify-center font-black text-sm">
                <flux:icon.check class="size-5" />
            </div>
            <span class="text-sm font-bold text-primary hidden sm:inline">Resumen</span>
        </div>
        <div class="w-8 sm:w-16 h-0.5 bg-primary rounded-full"></div>
        <div class="flex items-center gap-2">
            <div class="size-9 rounded-full bg-primary/20 text-primary flex items-center justify-center font-black text-sm">
                <flux:icon.check class="size-5" />
            </div>
            <span class="text-sm font-bold text-primary hidden sm:inline">Envío</span>
        </div>
        <div class="w-8 sm:w-16 h-0.5 bg-primary rounded-full"></div>
        <div class="flex items-center gap-2">
            <div class="size-9 rounded-full bg-primary text-white flex items-center justify-center font-black text-sm shadow-lg shadow-primary/30">
                <flux:icon.check class="size-5" />
            </div>
            <span class="text-sm font-bold text-primary hidden sm:inline">Confirmación</span>
        </div>
    </div>

    <!-- Success Banner -->
    <div class="bg-primary/5 border border-primary/20 rounded-2xl p-6 sm:p-8 text-center mb-8">
        <div class="size-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
            <flux:icon.check-circle variant="solid" class="size-10 text-primary" />
        </div>
        <h1 class="text-2xl sm:text-3xl font-black text-primary tracking-tight">¡Pedido Recibido!</h1>
        <p class="text-gray-600 font-medium mt-2 max-w-md mx-auto">Tu pedido <span class="font-black text-primary">{{ $order->order_number }}</span> ha sido registrado exitosamente.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        <!-- Left: Bank Details -->
        <div class="space-y-6">
            <!-- Payment Instructions -->
            <div class="bg-white rounded-2xl shadow-xl shadow-secondary/5 border-2 border-secondary/20 p-6 sm:p-8 space-y-6">
                <div class="flex items-center gap-3">
                    <div class="size-12 bg-secondary/10 rounded-xl flex items-center justify-center">
                        <flux:icon.banknotes variant="solid" class="size-6 text-secondary" />
                    </div>
                    <div>
                        <h2 class="font-black text-lg text-gray-900">Datos para Transferencia</h2>
                        <p class="text-xs text-gray-500 font-medium">Realiza tu pago a la siguiente cuenta</p>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-xl p-5 space-y-4">
                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                        <span class="text-sm font-bold text-gray-500 uppercase tracking-wider">Banco</span>
                        <span class="text-sm font-black text-gray-900">Nequi</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                        <span class="text-sm font-bold text-gray-500 uppercase tracking-wider">Tipo</span>
                        <span class="text-sm font-black text-gray-900">Cuenta de Ahorros</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                        <span class="text-sm font-bold text-gray-500 uppercase tracking-wider">Número</span>
                        <span class="text-sm font-black text-gray-900 tracking-wider">317 6198366</span>
                    </div>
                    {{-- <div class="flex justify-between items-center py-2 border-b border-gray-200">
                        <span class="text-sm font-bold text-gray-500 uppercase tracking-wider">Titular</span>
                        <span class="text-sm font-black text-gray-900">Mercado Distribuciones S.A.S</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                        <span class="text-sm font-bold text-gray-500 uppercase tracking-wider">NIT</span>
                        <span class="text-sm font-black text-gray-900">900.123.456-7</span>
                    </div> --}}
                    <div class="flex justify-between items-end py-2">
                        <span class="text-sm font-bold text-gray-500 uppercase tracking-wider">Valor a Pagar</span>
                        <span class="text-xl font-black text-primary tracking-tighter">${{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="bg-secondary/5 border border-secondary/20 rounded-xl p-4">
                    <p class="text-sm font-bold text-gray-700 flex items-start gap-2">
                        <flux:icon.information-circle variant="solid" class="size-5 text-secondary shrink-0 mt-0.5" />
                        <span>Una vez realizado el pago, envía el comprobante de transferencia a nuestro WhatsApp <span class="text-primary font-black">320 629 6235</span> indicando tu número de pedido.</span>
                    </p>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="bg-white rounded-2xl shadow-md shadow-gray-200/60 border border-gray-100 p-6 sm:p-8 space-y-4">
                <h3 class="font-black text-primary flex items-center gap-2">
                    <flux:icon.user-circle variant="solid" class="size-5 text-secondary" />
                    Datos del Cliente
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-wider">Nombre</p>
                        <p class="text-sm font-bold text-gray-900">{{ $order->customer_name }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-wider">Celular</p>
                        <p class="text-sm font-bold text-gray-900">{{ $order->customer_phone }}</p>
                    </div>
                    @if ($order->customer_email)
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-wider">Email</p>
                            <p class="text-sm font-bold text-gray-900">{{ $order->customer_email }}</p>
                        </div>
                    @endif
                    @if ($order->customer_document)
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-wider">Cédula / NIT</p>
                            <p class="text-sm font-bold text-gray-900">{{ $order->customer_document }}</p>
                        </div>
                    @endif
                </div>

                <h3 class="font-black text-primary flex items-center gap-2 pt-2">
                    <flux:icon.map-pin variant="solid" class="size-5 text-secondary" />
                    Envío
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-wider">País</p>
                        <p class="text-sm font-bold text-gray-900">{{ $order->shipping_country }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-wider">Departamento</p>
                        <p class="text-sm font-bold text-gray-900">{{ $order->shipping_department }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-wider">Ciudad</p>
                        <p class="text-sm font-bold text-gray-900">{{ $order->shipping_city }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-wider">Dirección</p>
                        <p class="text-sm font-bold text-gray-900">{{ $order->shipping_address }}</p>
                    </div>
                </div>
                @if ($order->shipping_notes)
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-wider">Notas</p>
                        <p class="text-sm font-bold text-gray-900">{{ $order->shipping_notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right: Order Items -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-md shadow-gray-200/60 border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50 bg-gray-50/50">
                    <h3 class="font-black text-primary flex items-center gap-2">
                        <flux:icon.shopping-bag variant="solid" class="size-5 text-secondary" />
                        Productos del Pedido
                    </h3>
                </div>

                <div class="divide-y divide-gray-50">
                    @foreach ($order->items as $item)
                        <div class="p-5 flex gap-4">
                            <div class="size-16 shrink-0 bg-gray-50 rounded-xl border border-gray-100 flex items-center justify-center p-1.5">
                                <img src="{{ $item->product_image ?? 'https://exitocol.vtexassets.com/arquivos/ids/28955924/Arroz-Diana-500-gr-445117_a.jpg?v=638864002494600000' }}"
                                     alt="{{ $item->product_title }}" class="max-h-full max-w-full object-contain">
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-900 line-clamp-2">{{ $item->product_title }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $item->quantity }} × ${{ number_format($item->unit_price, 0, ',', '.') }}</p>
                            </div>
                            <span class="text-sm font-black text-primary shrink-0">${{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="p-6 bg-gray-50/50 border-t border-gray-100 space-y-3">
                    <div class="flex justify-between text-gray-600 font-medium">
                        <span>Subtotal</span>
                        <span>${{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600 font-medium">
                        <span>Envío</span>
                        @if($order->shipping_cost > 0)
                            <span class="text-gray-900">${{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                        @else
                            <span class="text-secondary font-bold">Gratis</span>
                        @endif
                    </div>
                    <div class="pt-3 border-t border-gray-200 flex justify-between items-end">
                        <span class="text-sm font-black text-gray-400 uppercase tracking-widest">Total</span>
                        <span class="text-2xl font-black text-primary tracking-tighter">${{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Back to store -->
            <div class="text-center">
                <a href="{{ route('home') }}" wire:navigate
                    class="inline-flex items-center gap-2 bg-primary hover:bg-primary/95 text-white px-8 py-3 rounded-xl font-black text-sm uppercase tracking-widest transition-all shadow-lg shadow-primary/20 active:scale-[0.98]">
                    <flux:icon.shopping-bag variant="solid" class="size-5" />
                    Seguir Comprando
                </a>
            </div>
        </div>
    </div>
</div>
