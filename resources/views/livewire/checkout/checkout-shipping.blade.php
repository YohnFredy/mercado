<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">

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
            <div class="size-9 rounded-full bg-primary text-white flex items-center justify-center font-black text-sm shadow-lg shadow-primary/30">2</div>
            <span class="text-sm font-bold text-primary hidden sm:inline">Envío</span>
        </div>
        <div class="w-8 sm:w-16 h-0.5 bg-gray-200 rounded-full"></div>
        <div class="flex items-center gap-2">
            <div class="size-9 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center font-black text-sm">3</div>
            <span class="text-sm font-bold text-gray-400 hidden sm:inline">Confirmación</span>
        </div>
    </div>

    <h1 class="text-2xl sm:text-3xl font-black text-primary tracking-tight text-center mb-2">Datos de Envío</h1>
    <p class="text-gray-500 font-medium text-center mb-8">Completa tus datos para procesar el pedido</p>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Left: Form -->
        <div class="lg:col-span-2">
            <form wire:submit="placeOrder" class="space-y-6">
                <div class="bg-white rounded-2xl shadow-md shadow-gray-200/60 border border-gray-100 p-6 sm:p-8 space-y-6">

                    <h2 class="font-black text-lg text-primary flex items-center gap-2">
                        <flux:icon.user-circle variant="solid" class="size-6 text-secondary" />
                        Datos Personales
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <flux:field>
                            <flux:label>Nombre Completo *</flux:label>
                            <flux:input wire:model="name" placeholder="Ej: Juan Pérez" />
                            <flux:error name="name" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Cédula / NIT</flux:label>
                            <flux:input wire:model="document" placeholder="Ej: 1234567890" />
                            <flux:error name="document" />
                        </flux:field>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <flux:field>
                            <flux:label>Número de Celular *</flux:label>
                            <flux:input wire:model="phone" placeholder="Ej: 300 123 4567" />
                            <flux:error name="phone" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Correo Electrónico</flux:label>
                            <flux:input wire:model="email" type="email" placeholder="Ej: correo@ejemplo.com" />
                            <flux:error name="email" />
                        </flux:field>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-md shadow-gray-200/60 border border-gray-100 p-6 sm:p-8 space-y-6">

                    <h2 class="font-black text-lg text-primary flex items-center gap-2">
                        <flux:icon.truck variant="solid" class="size-6 text-secondary" />
                        Dirección de Envío
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <flux:field>
                            <flux:label>Departamento *</flux:label>
                            <flux:select wire:model.live="department_id" placeholder="Selecciona un departamento...">
                                <flux:select.option value="">Selecciona un departamento...</flux:select.option>
                                @foreach($this->departments as $department)
                                <flux:select.option value="{{ $department->id }}">{{ $department->name }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="department_id" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Ciudad *</flux:label>
                            <flux:select wire:model.live="city_id" placeholder="Selecciona una ciudad..." :disabled="!$department_id">
                                @foreach($this->cities as $city)
                                <flux:select.option value="{{ $city->id }}">{{ $city->name }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="city_id" />
                        </flux:field>
                    </div>

                    <div class="mt-5">
                        <flux:field>
                            <flux:label>Dirección Completa *</flux:label>
                            <flux:input wire:model="address" placeholder="Ej: Calle 5 # 10-20, Apto 101" />
                            <flux:error name="address" />
                        </flux:field>
                    </div>

                    <flux:field>
                        <flux:label>Notas Adicionales</flux:label>
                        <flux:textarea wire:model="notes" placeholder="Ej: Portería, edificio color blanco, dejar con el portero..." rows="3" />
                        <flux:error name="notes" />
                    </flux:field>
                </div>

                <!-- Mobile: Order Summary below form -->
                <div class="lg:hidden bg-white rounded-2xl shadow-md shadow-gray-200/60 border border-gray-100 p-6 space-y-4">
                    <h3 class="font-black text-primary">Tu Pedido ({{ $count }} productos)</h3>
                    @foreach ($items as $item)
                    <div wire:key="shipping-item-{{ $item['id'] }}" class="flex items-center gap-3 py-2 border-b border-gray-50 last:border-0">
                        <div class="size-12 shrink-0 bg-gray-50 rounded-lg border border-gray-100 flex items-center justify-center p-1">
                            <img src="{{ $item['image'] ?? 'https://exitocol.vtexassets.com/arquivos/ids/28955924/Arroz-Diana-500-gr-445117_a.jpg?v=638864002494600000' }}"
                                alt="{{ $item['title'] }}" class="max-h-full max-w-full object-contain">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-gray-900 truncate">{{ $item['title'] }}</p>
                            <p class="text-[10px] text-gray-500">{{ $item['quantity'] }} × ${{ number_format($item['price'], 0, ',', '.') }}</p>
                        </div>
                        <span class="text-sm font-black text-primary">${{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                    <div class="space-y-2 pt-3 border-t border-gray-100">
                        <div class="flex justify-between text-xs text-gray-500 font-medium">
                            <span>Subtotal</span>
                            <span>${{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 font-medium">
                            <span>Envío</span>
                            @if($shippingCost > 0)
                            <span>${{ number_format($shippingCost, 0, ',', '.') }}</span>
                            @else
                            <span class="text-secondary font-bold">Gratis</span>
                            @endif
                        </div>
                        <div class="pt-2 mt-2 border-t border-gray-100 flex justify-between items-end">
                            <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Total</span>
                            <span class="text-xl font-black text-primary tracking-tighter">${{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <a href="{{ route('checkout') }}" wire:navigate class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-primary transition-colors">
                        <flux:icon.arrow-left class="size-4" />
                        Volver al Resumen
                    </a>

                    <button type="submit"
                        class="w-full sm:w-auto bg-primary hover:bg-primary/95 text-white px-10 py-4 rounded-2xl font-black text-sm uppercase tracking-widest transition-all shadow-xl shadow-primary/25 active:scale-[0.98] flex items-center justify-center gap-3 cursor-pointer">
                        <flux:icon.check-circle variant="solid" class="size-5" />
                        Confirmar Pedido
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Sidebar (Desktop) -->
        <div class="hidden lg:block lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-xl shadow-primary/5 border border-primary/10 p-6 sticky top-8 space-y-5">
                <h3 class="font-black text-lg text-primary">Tu Pedido</h3>

                <div class="space-y-3 max-h-80 overflow-y-auto pr-1">
                    @foreach ($items as $item)
                    <div wire:key="sidebar-item-{{ $item['id'] }}" class="flex items-center gap-3 py-2 border-b border-gray-50 last:border-0">
                        <div class="size-14 shrink-0 bg-gray-50 rounded-xl border border-gray-100 flex items-center justify-center p-1.5">
                            <img src="{{ $item['image'] ?? 'https://exitocol.vtexassets.com/arquivos/ids/28955924/Arroz-Diana-500-gr-445117_a.jpg?v=638864002494600000' }}"
                                alt="{{ $item['title'] }}" class="max-h-full max-w-full object-contain">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-gray-900 line-clamp-2">{{ $item['title'] }}</p>
                            <p class="text-[10px] text-gray-500 mt-0.5">{{ $item['quantity'] }} × ${{ number_format($item['price'], 0, ',', '.') }}</p>
                        </div>
                        <span class="text-sm font-black text-primary shrink-0">${{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>

                <div class="space-y-3 pt-3 border-t border-gray-100">
                    <div class="flex justify-between text-gray-600 font-medium">
                        <span>Subtotal</span>
                        <span>${{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600 font-medium">
                        <span>Envío</span>
                        @if($shippingCost > 0)
                        <span>${{ number_format($shippingCost, 0, ',', '.') }}</span>
                        @else
                        <span class="text-secondary font-bold">Gratis</span>
                        @endif
                    </div>
                    <div class="pt-3 border-t border-gray-100 flex justify-between items-end">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total</p>
                        <p class="text-2xl font-black text-primary tracking-tighter">${{ number_format($total, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>