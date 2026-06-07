<div>
    <div class="flex flex-col gap-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <flux:heading size="xl">{{ __('Gestión de Pedidos') }}</flux:heading>
                <flux:subheading>{{ __('Administra, valida y actualiza el estado de los pedidos.') }}</flux:subheading>
            </div>
            <div class="shrink-0">
                <flux:modal.trigger name="consolidated-report">
                    <flux:button icon="clipboard-document-list" variant="primary" class="bg-primary hover:bg-primary/95 text-white border-primary">
                        {{ __('Consolidado de Productos') }}
                    </flux:button>
                </flux:modal.trigger>
            </div>
        </div>

        {{-- Search & Filter --}}
        <div class="flex flex-col sm:flex-row gap-3">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass"
                placeholder="{{ __('Buscar por # pedido, cliente o email...') }}" class="w-full sm:max-w-sm" />
            <flux:select wire:model.live="statusFilter" class="w-full sm:w-48">
                <flux:select.option value="">{{ __('Todos los estados') }}</flux:select.option>
                @foreach ($statuses as $status)
                    <flux:select.option value="{{ $status }}">{{ $this->statusLabel($status) }}
                    </flux:select.option>
                @endforeach
            </flux:select>
        </div>

        {{-- Table --}}
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('# Pedido') }}</flux:table.column>
                <flux:table.column>{{ __('Cliente') }}</flux:table.column>
                <flux:table.column>{{ __('Total') }}</flux:table.column>
                <flux:table.column>{{ __('Estado') }}</flux:table.column>
                <flux:table.column>{{ __('Fecha') }}</flux:table.column>
                <flux:table.column>{{ __('Acciones') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($orders as $order)
                    <flux:table.row wire:key="{{ $order->id }}">
                        <flux:table.cell>
                            <span class="font-mono font-semibold text-sm text-gray-900 dark:text-white">
                                {{ $order->order_number }}
                            </span>
                        </flux:table.cell>

                        <flux:table.cell>
                            <div class="font-medium text-gray-900 dark:text-white">{{ $order->customer_name }}</div>
                            <div class="text-xs text-gray-500 dark:text-zinc-400">{{ $order->customer_email }}</div>
                        </flux:table.cell>

                        <flux:table.cell>
                            <span class="font-bold text-gray-900 dark:text-white">
                                ${{ number_format($order->total, 0, ',', '.') }}
                            </span>
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:badge color="{{ $this->statusColor($order->status) }}" size="sm"
                                class="uppercase text-[10px] font-bold">
                                {{ $this->statusLabel($order->status) }}
                            </flux:badge>
                        </flux:table.cell>

                        <flux:table.cell>
                            <span class="text-sm text-gray-500 dark:text-zinc-400">
                                {{ $order->created_at->format('d M Y') }}
                            </span>
                        </flux:table.cell>

                        <flux:table.cell>
                            <div class="flex items-center gap-1.5">
                                <flux:button size="sm" variant="subtle" icon="eye"
                                    wire:click="viewOrder({{ $order->id }})">
                                    {{ __('Ver') }}
                                </flux:button>

                                <flux:dropdown>
                                    <flux:button size="sm" variant="subtle" icon="printer" square aria-label="{{ __('Imprimir Remisión') }}" />
                                    <flux:menu class="min-w-36">
                                        <flux:menu.item href="{{ route('admin.orders.print', ['order' => $order->id, 'format' => 'letter']) }}" target="_blank" icon="document-text">
                                            {{ __('Imprimir Carta') }}
                                        </flux:menu.item>
                                        <flux:menu.item href="{{ route('admin.orders.print', ['order' => $order->id, 'format' => 'ticket']) }}" target="_blank" icon="ticket">
                                            {{ __('Imprimir Ticket') }}
                                        </flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6">
                            <div class="py-12 text-center text-gray-400 dark:text-zinc-500">
                                <flux:icon.inbox class="size-10 mx-auto mb-3 opacity-50" />
                                <p class="font-medium">{{ __('No se encontraron pedidos.') }}</p>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <div>{{ $orders->links() }}</div>

        {{-- Detail Flyout Modal --}}
        <flux:modal name="order-detail" class="md:w-[48rem]" variant="flyout">
            @if ($this->selectedOrder)
                @php $order = $this->selectedOrder; @endphp
                <div class="space-y-6">

                    {{-- Modal Header --}}
                    <div
                        class="flex items-start justify-between gap-4 bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/10 rounded-xl p-4">
                        <div>
                            <p
                                class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-widest font-bold mb-1">
                                {{ __('Pedido') }}</p>
                            <p class="text-lg font-black text-gray-900 dark:text-white font-mono">
                                {{ $order->order_number }}</p>
                            <p class="text-xs text-gray-500 dark:text-zinc-400 mt-1">
                                {{ __('Creado:') }} {{ $order->created_at->format('d M Y - h:i A') }}
                            </p>
                        </div>
                        <flux:badge color="{{ $this->statusColor($order->status) }}"
                            class="uppercase font-bold text-xs shrink-0">
                            {{ $this->statusLabel($order->status) }}
                        </flux:badge>
                    </div>

                    {{-- Payment Confirmation --}}
                    @if ($order->paid_at)
                        <div
                            class="flex items-center gap-3 bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/30 rounded-xl p-3">
                            <flux:icon.check-circle class="size-5 text-green-600 dark:text-green-400 shrink-0" />
                            <div>
                                <p class="text-sm font-bold text-green-800 dark:text-green-300">
                                    {{ __('Pago Confirmado') }}</p>
                                <p class="text-xs text-green-700 dark:text-green-400">
                                    {{ $order->paid_at->format('d M Y - h:i A') }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Customer + Payment Summary --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Customer Info --}}
                        <div class="space-y-2">
                            <h4 class="font-black text-gray-900 dark:text-white text-sm flex items-center gap-2">
                                <flux:icon.user class="size-4 text-primary shrink-0" />
                                {{ __('Cliente') }}
                            </h4>
                            <div
                                class="bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/10 rounded-xl p-3 text-sm text-gray-600 dark:text-zinc-300 space-y-1">
                                <p class="font-bold text-gray-900 dark:text-white">{{ $order->customer_name }}</p>
                                <p>{{ $order->customer_email }}</p>
                                <p>{{ $order->customer_phone }}</p>
                                @if ($order->customer_document)
                                    <p>Doc:
                                        {{ $order->customer_document }}</p>
                                @endif
                                <p>Pts:
                                    {{ $order->pts }}</p>
                            </div>
                        </div>

                        {{-- Shipping & Payment --}}
                        <div class="space-y-2">
                            <h4 class="font-black text-gray-900 dark:text-white text-sm flex items-center gap-2">
                                <flux:icon.map-pin class="size-4 text-primary shrink-0" />
                                {{ __('Destino y Pago') }}
                            </h4>
                            <div
                                class="bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/10 rounded-xl p-3 text-sm text-gray-600 dark:text-zinc-300 space-y-1">
                                <p>{{ $order->shipping_address }}</p>
                                @if ($order->shipping_notes)
                                    <p>{{ $order->shipping_notes }}</p>
                                @endif
                                <p>{{ $order->shipping_city }}, {{ $order->shipping_department }}</p>
                                <p>{{ $order->shipping_country }}</p>
                                <div class="pt-2 mt-2 border-t border-gray-200 dark:border-white/10 space-y-1">
                                    <div class="flex justify-between text-xs">
                                        <span>{{ __('Subtotal') }}</span>
                                        <span>${{ number_format($order->subtotal, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between text-xs">
                                        <span>{{ __('Envío') }}</span>
                                        <span>{{ $order->shipping_cost > 0 ? '$' . number_format($order->shipping_cost, 0, ',', '.') : 'Gratis' }}</span>
                                    </div>
                                    <div class="flex justify-between font-black text-gray-900 dark:text-white pt-1">
                                        <span>{{ __('Total') }}</span>
                                        <span
                                            class="text-primary">${{ number_format($order->total, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Status Update --}}
                    @can('orders:edit')
                        <div
                            class="bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/10 rounded-xl p-4 space-y-3">
                            <h4 class="font-black text-gray-900 dark:text-white text-sm flex items-center gap-2">
                                <flux:icon.arrow-path class="size-4 text-primary shrink-0" />
                                {{ __('Actualizar Estado del Pedido') }}
                            </h4>
                            <div class="flex flex-col sm:flex-row gap-3">
                                <flux:select wire:model="newStatus" class="flex-1">
                                    @foreach ($statuses as $status)
                                        <flux:select.option value="{{ $status }}">{{ $this->statusLabel($status) }}
                                        </flux:select.option>
                                    @endforeach
                                </flux:select>
                                <flux:button variant="primary" wire:click="updateStatus" wire:loading.attr="disabled"
                                    wire:target="updateStatus" class="shrink-0">
                                    <span wire:loading.remove wire:target="updateStatus">{{ __('Actualizar') }}</span>
                                    <span wire:loading wire:target="updateStatus">{{ __('Guardando...') }}</span>
                                </flux:button>
                            </div>
                            <p class="text-xs text-gray-400 dark:text-zinc-500">
                                {{ __('Al marcar como "Pagado" se registra automáticamente la fecha y hora del pago.') }}
                            </p>
                        </div>
                    @endcan

                    {{-- Products List --}}
                    <div class="space-y-2">
                        <h4 class="font-black text-gray-900 dark:text-white text-sm flex items-center gap-2">
                            <flux:icon.shopping-bag class="size-4 text-primary shrink-0" />
                            {{ __('Productos Comprados') }}
                            <flux:badge color="zinc" size="sm">{{ $order->items->count() }}</flux:badge>
                        </h4>
                        <div
                            class="divide-y divide-gray-100 dark:divide-white/10 border border-gray-100 dark:border-white/10 rounded-xl overflow-hidden">
                            @foreach ($order->items as $item)
                                <div class="flex gap-3 p-3 bg-white dark:bg-white/5 items-center"
                                    wire:key="{{ $item->id }}">
                                    <div
                                        class="size-12 bg-gray-50 dark:bg-white/10 rounded-lg border border-gray-100 dark:border-white/10 flex-shrink-0 p-1 overflow-hidden">
                                        <img src="{{ $item->product_image ?? 'https://via.placeholder.com/48' }}"
                                            alt="{{ $item->product_title }}" class="w-full h-full object-contain">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white line-clamp-1">
                                            {{ $item->product_title }}</p>
                                        <p class="text-xs text-gray-500 dark:text-zinc-400">
                                            {{ $item->quantity }} ×
                                            ${{ number_format($item->unit_price, 0, ',', '.') }}
                                        </p>
                                    </div>
                                    <div class="font-black text-sm text-primary shrink-0">
                                        ${{ number_format($item->subtotal, 0, ',', '.') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Shipping Notes --}}
                    @if ($order->shipping_notes)
                        <div
                            class="bg-yellow-50 dark:bg-yellow-500/10 border border-yellow-200 dark:border-yellow-500/30 rounded-xl p-3">
                            <p class="text-xs font-bold text-yellow-800 dark:text-yellow-300 mb-1">
                                {{ __('Notas de Envío') }}</p>
                            <p class="text-sm text-yellow-700 dark:text-yellow-400">{{ $order->shipping_notes }}</p>
                        </div>
                    @endif

                    {{-- Imprimir Remisión --}}
                    <div class="bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/10 rounded-xl p-4 space-y-3">
                        <h4 class="font-black text-gray-900 dark:text-white text-sm flex items-center gap-2">
                            <flux:icon.printer class="size-4 text-primary shrink-0" />
                            {{ __('Imprimir Remisión (Soporte)') }}
                        </h4>
                        <p class="text-xs text-gray-500 dark:text-zinc-400">
                            {{ __('Genera un comprobante de entrega para el cliente. Diseñado para ahorrar papel y espacio.') }}
                        </p>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <a href="{{ route('admin.orders.print', ['order' => $order->id, 'format' => 'letter']) }}" target="_blank"
                               class="flex-1 flex items-center justify-center gap-2 rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-gray-800 dark:text-zinc-200 py-2 text-xs font-bold transition">
                                <flux:icon.document-text class="size-4 shrink-0 text-primary" />
                                {{ __('Formato Carta') }}
                            </a>
                            <a href="{{ route('admin.orders.print', ['order' => $order->id, 'format' => 'ticket']) }}" target="_blank"
                               class="flex-1 flex items-center justify-center gap-2 rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-gray-800 dark:text-zinc-200 py-2 text-xs font-bold transition">
                                <flux:icon.ticket class="size-4 shrink-0 text-primary" />
                                {{ __('Formato Ticket (POS)') }}
                            </a>
                        </div>
                    </div>

                    {{-- Enviar a WhatsApp --}}
                    <div x-data="{ whatsappContact: '' }"
                        class="bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 rounded-xl p-4 space-y-3">
                        <h4 class="font-black text-emerald-900 dark:text-emerald-400 text-sm flex items-center gap-2">
                            <flux:icon.chat-bubble-bottom-center-text class="size-4 shrink-0" />
                            {{ __('Enviar a WhatsApp') }}
                        </h4>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <flux:select x-model="whatsappContact" placeholder="Selecciona un contacto..."
                                class="flex-1">
                                {{-- Añade aquí tus contactos reales --}}
                                <flux:select.option value="573145207814">Fornuvi</flux:select.option>
                                <flux:select.option value="573206296235">Ipermerca</flux:select.option>
                            </flux:select>

                            <flux:button type="button"
                                class="shrink-0 bg-emerald-600 hover:bg-emerald-700 text-white border-emerald-600 dark:bg-emerald-500 dark:hover:bg-emerald-600 dark:text-white"
                                x-on:click="if(whatsappContact) window.open('https://wa.me/' + whatsappContact + '?text={{ $this->whatsappMessage }}', '_blank')"
                                x-bind:disabled="!whatsappContact">
                                {{ __('Enviar') }}
                            </flux:button>
                        </div>
                        <p class="text-xs text-emerald-700 dark:text-emerald-500">
                            {{ __('Selecciona un contacto para enviarle el detalle completo del pedido por WhatsApp.') }}
                        </p>
                    </div>

                    {{-- Close --}}
                    <div class="flex justify-end pt-2 border-t border-gray-100 dark:border-white/10">
                        <flux:modal.close>
                            <flux:button variant="ghost">{{ __('Cerrar') }}</flux:button>
                        </flux:modal.close>
                    </div>
                </div>
            @endif
        </flux:modal>

        {{-- Consolidado Report Modal --}}
        <flux:modal name="consolidated-report" class="md:w-[48rem]" variant="flyout">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">{{ __('Consolidado de Productos') }}</flux:heading>
                    <flux:subheading>{{ __('Consolida todos los productos de los pedidos según los estados seleccionados.') }}</flux:subheading>
                </div>

                {{-- Status Selectors --}}
                <div class="space-y-3 bg-gray-50 dark:bg-zinc-800/50 border border-gray-100 dark:border-white/10 rounded-xl p-4">
                    <label class="block text-sm font-bold text-gray-900 dark:text-white">{{ __('Filtrar por Estados') }}</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach ($statuses as $status)
                            <flux:checkbox wire:model.live="consolidatedStatuses" value="{{ $status }}" label="{{ $this->statusLabel($status) }}" />
                        @endforeach
                    </div>
                </div>

                {{-- Consolidated Products Table --}}
                <div class="space-y-2">
                    <h4 class="font-black text-gray-900 dark:text-white text-sm flex items-center gap-2">
                        <flux:icon.shopping-bag class="size-4 text-primary shrink-0" />
                        {{ __('Productos Consolidados') }}
                    </h4>

                    @if ($this->consolidatedProducts->isNotEmpty())
                        <div class="border border-gray-200 dark:border-white/10 rounded-xl overflow-hidden">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-white text-xs font-bold uppercase border-b border-gray-200 dark:border-white/10">
                                        <th class="p-3">{{ __('Producto') }}</th>
                                        <th class="p-3 text-center">{{ __('Cantidad') }}</th>
                                        <th class="p-3 text-right">{{ __('Precio Unitario') }}</th>
                                        <th class="p-3 text-right">{{ __('Total') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-white/10 bg-white dark:bg-zinc-900 text-sm">
                                    @foreach ($this->consolidatedProducts as $item)
                                        <tr class="text-gray-900 dark:text-zinc-300 hover:bg-gray-50 dark:hover:bg-zinc-800/50">
                                            <td class="p-3 font-medium">{{ $item->product_title }}</td>
                                            <td class="p-3 text-center font-bold">{{ $item->total_quantity }}</td>
                                            <td class="p-3 text-right font-mono">${{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                            <td class="p-3 text-right font-mono font-bold text-primary">${{ number_format($item->total_amount, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-white font-black border-t border-gray-200 dark:border-white/10">
                                        <td colspan="3" class="p-3 text-right text-sm uppercase">{{ __('Valor Total al Público') }}</td>
                                        <td class="p-3 text-right font-mono text-base text-primary">${{ number_format($this->consolidatedTotal, 0, ',', '.') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="py-12 text-center text-gray-400 dark:text-zinc-500 border border-dashed border-gray-200 dark:border-white/10 rounded-xl bg-gray-50/50 dark:bg-zinc-900/50">
                            <flux:icon.shopping-bag class="size-10 mx-auto mb-3 opacity-50 text-gray-400" />
                            <p class="font-medium text-sm">{{ __('Selecciona al menos un estado con pedidos asociados para consolidar productos.') }}</p>
                        </div>
                    @endif
                </div>

                {{-- WhatsApp Share Section --}}
                @if ($this->consolidatedProducts->isNotEmpty())
                    <div x-data="{ contactType: 'general', selectedContact: '', customNumber: '' }"
                        class="bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 rounded-xl p-4 space-y-4">
                        <h4 class="font-black text-emerald-900 dark:text-emerald-400 text-sm flex items-center gap-2">
                            <flux:icon.chat-bubble-bottom-center-text class="size-4 shrink-0 text-emerald-600 dark:text-emerald-400" />
                            {{ __('Enviar Consolidado a WhatsApp') }}
                        </h4>

                        <div class="space-y-3">
                            <div class="flex flex-wrap gap-4 text-xs font-bold text-emerald-800 dark:text-emerald-400">
                                <label class="flex items-center gap-1.5 cursor-pointer">
                                    <input type="radio" name="contactType" value="general" x-model="contactType" class="text-emerald-600 focus:ring-emerald-500">
                                    <span>{{ __('Compartir (Elegir chat en WhatsApp)') }}</span>
                                </label>
                                <label class="flex items-center gap-1.5 cursor-pointer">
                                    <input type="radio" name="contactType" value="contacts" x-model="contactType" class="text-emerald-600 focus:ring-emerald-500">
                                    <span>{{ __('Contactos Guardados') }}</span>
                                </label>
                                <label class="flex items-center gap-1.5 cursor-pointer">
                                    <input type="radio" name="contactType" value="custom" x-model="contactType" class="text-emerald-600 focus:ring-emerald-500">
                                    <span>{{ __('Número Personalizado') }}</span>
                                </label>
                            </div>

                            <div class="flex flex-col sm:flex-row gap-3 items-end">
                                <div x-show="contactType === 'contacts'" class="flex-1 w-full" x-cloak>
                                    <flux:select x-model="selectedContact" placeholder="Selecciona un contacto..." class="w-full">
                                        <flux:select.option value="573145207814">Fornuvi</flux:select.option>
                                        <flux:select.option value="573206296235">Ipermerca</flux:select.option>
                                    </flux:select>
                                </div>

                                <div x-show="contactType === 'custom'" class="flex-1 w-full" x-cloak>
                                    <flux:input x-model="customNumber" placeholder="Ej: 573001234567 (con código de país)" class="w-full" />
                                </div>

                                <div x-show="contactType === 'general'" class="flex-1 py-2 text-xs text-emerald-700 dark:text-emerald-400" x-cloak>
                                    {{ __('El mensaje se enviará a WhatsApp y podrás seleccionar a cualquier persona o grupo de tu lista de chats.') }}
                                </div>

                                <flux:button type="button"
                                    class="shrink-0 bg-emerald-600 hover:bg-emerald-700 text-white border-emerald-600 dark:bg-emerald-500 dark:hover:bg-emerald-600 dark:text-white font-bold"
                                    x-on:click="
                                        let url = 'https://api.whatsapp.com/send?text={{ $this->consolidatedWhatsappMessage }}';
                                        if (contactType === 'contacts' && selectedContact) {
                                            url = 'https://wa.me/' + selectedContact + '?text={{ $this->consolidatedWhatsappMessage }}';
                                        } else if (contactType === 'custom' && customNumber) {
                                            let cleanNum = customNumber.replace(/[\s\+]/g, '');
                                            url = 'https://wa.me/' + cleanNum + '?text={{ $this->consolidatedWhatsappMessage }}';
                                        }
                                        window.open(url, '_blank');
                                    "
                                    x-bind:disabled="contactType === 'contacts' ? !selectedContact : (contactType === 'custom' ? !customNumber : false)">
                                    {{ __('Enviar') }}
                                </flux:button>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Close --}}
                <div class="flex justify-end pt-2 border-t border-gray-100 dark:border-white/10">
                    <flux:modal.close>
                        <flux:button variant="ghost">{{ __('Cerrar') }}</flux:button>
                    </flux:modal.close>
                </div>
            </div>
        </flux:modal>

    </div>
</div>