<?php

use Flux\Flux;
use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;

new #[Layout('layouts.admin')] class extends Component {
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public ?int $selectedOrderId = null;
    public string $newStatus = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function selectedOrder(): ?Order
    {
        if (! $this->selectedOrderId) {
            return null;
        }

        return Order::with('items')->find($this->selectedOrderId);
    }

    public function viewOrder(int $id): void
    {
        $this->selectedOrderId = $id;
        $order = Order::find($id);
        $this->newStatus = $order?->status ?? '';
        Flux::modal('order-detail')->show();
    }

    public function updateStatus(): void
    {
        Gate::authorize('orders:edit');

        $order = Order::findOrFail($this->selectedOrderId);

        $updateData = ['status' => $this->newStatus];

        if ($this->newStatus === 'paid' && is_null($order->paid_at)) {
            $updateData['paid_at'] = now();
        }

        $order->update($updateData);

        unset($this->selectedOrder);

        $this->dispatch('order-status-updated');
    }

    public function statusLabel(string $status): string
    {
        return match ($status) {
            'pending'    => 'Pendiente',
            'paid'       => 'Pagado',
            'processing' => 'En Proceso',
            'shipped'    => 'Enviado',
            'delivered'  => 'Entregado',
            'cancelled'  => 'Cancelado',
            default      => ucfirst($status),
        };
    }

    public function statusColor(string $status): string
    {
        return match ($status) {
            'pending'    => 'zinc',
            'paid'       => 'blue',
            'processing' => 'yellow',
            'shipped'    => 'indigo',
            'delivered'  => 'green',
            'cancelled'  => 'red',
            default      => 'zinc',
        };
    }

    public function with(): array
    {
        $query = Order::with('items')
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('order_number', 'like', '%' . $this->search . '%')
                        ->orWhere('customer_name', 'like', '%' . $this->search . '%')
                        ->orWhere('customer_email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest();

        return [
            'orders'   => $query->paginate(12),
            'statuses' => ['pending', 'paid', 'processing', 'shipped', 'delivered', 'cancelled'],
        ];
    }
};
?>

<div>
    <div class="flex flex-col gap-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <flux:heading size="xl">{{ __('Gestión de Pedidos') }}</flux:heading>
                <flux:subheading>{{ __('Administra, valida y actualiza el estado de los pedidos.') }}</flux:subheading>
            </div>
        </div>

        {{-- Search & Filter --}}
        <div class="flex flex-col sm:flex-row gap-3">
            <flux:input
                wire:model.live.debounce.300ms="search"
                icon="magnifying-glass"
                placeholder="{{ __('Buscar por # pedido, cliente o email...') }}"
                class="w-full sm:max-w-sm"
            />
            <flux:select wire:model.live="statusFilter" class="w-full sm:w-48">
                <flux:select.option value="">{{ __('Todos los estados') }}</flux:select.option>
                @foreach($statuses as $status)
                    <flux:select.option value="{{ $status }}">{{ $this->statusLabel($status) }}</flux:select.option>
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
                            <flux:badge
                                color="{{ $this->statusColor($order->status) }}"
                                size="sm"
                                class="uppercase text-[10px] font-bold"
                            >
                                {{ $this->statusLabel($order->status) }}
                            </flux:badge>
                        </flux:table.cell>

                        <flux:table.cell>
                            <span class="text-sm text-gray-500 dark:text-zinc-400">
                                {{ $order->created_at->format('d M Y') }}
                            </span>
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:button
                                size="sm"
                                variant="subtle"
                                icon="eye"
                                wire:click="viewOrder({{ $order->id }})"
                            >
                                {{ __('Ver') }}
                            </flux:button>
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
            @if($this->selectedOrder)
                @php $order = $this->selectedOrder; @endphp
                <div class="space-y-6">

                    {{-- Modal Header --}}
                    <div class="flex items-start justify-between gap-4 bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/10 rounded-xl p-4">
                        <div>
                            <p class="text-xs text-gray-400 dark:text-zinc-500 uppercase tracking-widest font-bold mb-1">{{ __('Pedido') }}</p>
                            <p class="text-lg font-black text-gray-900 dark:text-white font-mono">{{ $order->order_number }}</p>
                            <p class="text-xs text-gray-500 dark:text-zinc-400 mt-1">
                                {{ __('Creado:') }} {{ $order->created_at->format('d M Y - h:i A') }}
                            </p>
                        </div>
                        <flux:badge
                            color="{{ $this->statusColor($order->status) }}"
                            class="uppercase font-bold text-xs shrink-0"
                        >
                            {{ $this->statusLabel($order->status) }}
                        </flux:badge>
                    </div>

                    {{-- Payment Confirmation --}}
                    @if($order->paid_at)
                        <div class="flex items-center gap-3 bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/30 rounded-xl p-3">
                            <flux:icon.check-circle class="size-5 text-green-600 dark:text-green-400 shrink-0" />
                            <div>
                                <p class="text-sm font-bold text-green-800 dark:text-green-300">{{ __('Pago Confirmado') }}</p>
                                <p class="text-xs text-green-700 dark:text-green-400">{{ $order->paid_at->format('d M Y - h:i A') }}</p>
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
                            <div class="bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/10 rounded-xl p-3 text-sm text-gray-600 dark:text-zinc-300 space-y-1">
                                <p class="font-bold text-gray-900 dark:text-white">{{ $order->customer_name }}</p>
                                <p>{{ $order->customer_email }}</p>
                                <p>{{ $order->customer_phone }}</p>
                                @if($order->customer_document)
                                    <p class="text-xs text-gray-400 dark:text-zinc-500">Doc: {{ $order->customer_document }}</p>
                                @endif
                            </div>
                        </div>

                        {{-- Shipping & Payment --}}
                        <div class="space-y-2">
                            <h4 class="font-black text-gray-900 dark:text-white text-sm flex items-center gap-2">
                                <flux:icon.map-pin class="size-4 text-primary shrink-0" />
                                {{ __('Destino y Pago') }}
                            </h4>
                            <div class="bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/10 rounded-xl p-3 text-sm text-gray-600 dark:text-zinc-300 space-y-1">
                                <p>{{ $order->shipping_address }}</p>
                                <p>{{ $order->shipping_city }}, {{ $order->shipping_department }}</p>
                                <p>{{ $order->shipping_country }}</p>
                                <div class="pt-2 mt-2 border-t border-gray-200 dark:border-white/10 space-y-1">
                                    <div class="flex justify-between text-xs">
                                        <span>{{ __('Subtotal') }}</span>
                                        <span>${{ number_format($order->subtotal, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between text-xs">
                                        <span>{{ __('Envío') }}</span>
                                        <span>{{ $order->shipping_cost > 0 ? '$'.number_format($order->shipping_cost, 0, ',', '.') : 'Gratis' }}</span>
                                    </div>
                                    <div class="flex justify-between font-black text-gray-900 dark:text-white pt-1">
                                        <span>{{ __('Total') }}</span>
                                        <span class="text-primary">${{ number_format($order->total, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Status Update --}}
                    @can('orders:edit')
                    <div class="bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/10 rounded-xl p-4 space-y-3">
                        <h4 class="font-black text-gray-900 dark:text-white text-sm flex items-center gap-2">
                            <flux:icon.arrow-path class="size-4 text-primary shrink-0" />
                            {{ __('Actualizar Estado del Pedido') }}
                        </h4>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <flux:select wire:model="newStatus" class="flex-1">
                                @foreach($statuses as $status)
                                    <flux:select.option value="{{ $status }}">{{ $this->statusLabel($status) }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:button
                                variant="primary"
                                wire:click="updateStatus"
                                wire:loading.attr="disabled"
                                wire:target="updateStatus"
                                class="shrink-0"
                            >
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
                        <div class="divide-y divide-gray-100 dark:divide-white/10 border border-gray-100 dark:border-white/10 rounded-xl overflow-hidden">
                            @foreach($order->items as $item)
                                <div class="flex gap-3 p-3 bg-white dark:bg-white/5 items-center" wire:key="{{ $item->id }}">
                                    <div class="size-12 bg-gray-50 dark:bg-white/10 rounded-lg border border-gray-100 dark:border-white/10 flex-shrink-0 p-1 overflow-hidden">
                                        <img
                                            src="{{ $item->product_image ?? 'https://via.placeholder.com/48' }}"
                                            alt="{{ $item->product_title }}"
                                            class="w-full h-full object-contain"
                                        >
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white line-clamp-1">{{ $item->product_title }}</p>
                                        <p class="text-xs text-gray-500 dark:text-zinc-400">
                                            {{ $item->quantity }} × ${{ number_format($item->unit_price, 0, ',', '.') }}
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
                    @if($order->shipping_notes)
                        <div class="bg-yellow-50 dark:bg-yellow-500/10 border border-yellow-200 dark:border-yellow-500/30 rounded-xl p-3">
                            <p class="text-xs font-bold text-yellow-800 dark:text-yellow-300 mb-1">{{ __('Notas de Envío') }}</p>
                            <p class="text-sm text-yellow-700 dark:text-yellow-400">{{ $order->shipping_notes }}</p>
                        </div>
                    @endif

                    {{-- Close --}}
                    <div class="flex justify-end pt-2 border-t border-gray-100 dark:border-white/10">
                        <flux:modal.close>
                            <flux:button variant="ghost">{{ __('Cerrar') }}</flux:button>
                        </flux:modal.close>
                    </div>
                </div>
            @endif
        </flux:modal>

    </div>
</div>
