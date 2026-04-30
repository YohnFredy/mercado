<?php

use App\Models\Order;
use Livewire\Component;
use Livewire\Attributes\Computed;

new class extends Component
{
    public ?int $selectedOrderId = null;

    #[Computed]
    public function orders()
    {
        return Order::with('items')
            ->where('customer_email', auth()->user()->email)
            ->latest()
            ->get();
    }

    #[Computed]
    public function selectedOrder()
    {
        if (!$this->selectedOrderId) {
            return null;
        }
        return Order::with('items')->find($this->selectedOrderId);
    }

    public function viewDetails($orderId)
    {
        $this->selectedOrderId = $orderId;
        
        \Flux::modal('order-details')->show();
    }
};
?>

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-black text-gray-900 tracking-tight">Mis Pedidos</h1>
        <p class="text-gray-500 mt-2 font-medium">Revisa el historial y estado de tus compras.</p>
    </div>

    @if($this->orders->isEmpty())
        <div class="bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 p-12 text-center">
            <div class="size-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm border border-gray-100">
                <flux:icon.shopping-bag class="size-8 text-gray-400" />
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Aún no tienes pedidos</h3>
            <p class="text-gray-500 mb-6 font-medium">Explora nuestro catálogo y realiza tu primera compra rápida y segura.</p>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 bg-primary hover:bg-primary/95 text-white px-6 py-2.5 rounded-xl font-bold transition-all shadow-md active:scale-95">
                Ir a la Tienda
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($this->orders as $order)
                <flux:card class="space-y-4 hover:shadow-xl hover:shadow-gray-200/50 transition-all duration-300 border-gray-200/60 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Pedido</p>
                                <p class="font-bold text-gray-900">{{ $order->order_number }}</p>
                            </div>
                            <flux:badge :color="
                                $order->status === 'delivered' ? 'green' : 
                                ($order->status === 'cancelled' ? 'red' : 
                                ($order->status === 'shipped' ? 'blue' : 'zinc'))
                            " size="sm" class="uppercase text-[10px] font-bold">
                                {{ ucfirst($order->status) }}
                            </flux:badge>
                        </div>

                        <div class="flex flex-col gap-2 text-sm text-gray-600 font-medium">
                            <div class="flex items-center gap-2 bg-gray-50 p-2 rounded-lg border border-gray-100">
                                <flux:icon.calendar class="size-4 text-gray-400" />
                                <span>{{ $order->created_at->format('d M, Y - h:i A') }}</span>
                            </div>
                            <div class="flex items-center gap-2 bg-gray-50 p-2 rounded-lg border border-gray-100">
                                <flux:icon.banknotes class="size-4 text-gray-400" />
                                <span class="font-black text-gray-900">${{ number_format($order->total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 mt-2 border-t border-gray-100 flex justify-between items-center">
                        <p class="text-xs text-gray-500 font-bold">{{ $order->items->count() }} artículo(s)</p>
                        <flux:button wire:click="viewDetails({{ $order->id }})" size="sm" variant="ghost" class="text-primary hover:text-primary/90 hover:bg-primary/5 font-bold px-3">
                            Ver Detalles
                        </flux:button>
                    </div>
                </flux:card>
            @endforeach
        </div>
    @endif

    <flux:modal name="order-details" class="md:w-[45rem]" variant="flyout">
        @if($this->selectedOrder)
            <div class="space-y-6">
                <!-- Header Modal -->
                <div>
                    <h2 class="text-xl font-black text-gray-900 bg-gray-50 p-4 rounded-xl border border-gray-100 flex items-center justify-between">
                        <div class="flex flex-col">
                            <span class="text-xs text-gray-500 font-medium uppercase tracking-widest">Pedido</span>
                            <span>{{ $this->selectedOrder->order_number }}</span>
                        </div>
                        <flux:badge :color="
                            $this->selectedOrder->status === 'delivered' ? 'green' : 
                            ($this->selectedOrder->status === 'cancelled' ? 'red' : 
                            ($this->selectedOrder->status === 'shipped' ? 'blue' : 'zinc'))
                        " class="uppercase font-bold">
                            {{ ucfirst($this->selectedOrder->status) }}
                        </flux:badge>
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <h4 class="font-black text-gray-900 text-sm flex items-center gap-2">
                            <flux:icon.map-pin class="size-4 text-secondary" />
                            Destino
                        </h4>
                        <div class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg border border-gray-100">
                            <p class="font-bold text-gray-900">{{ $this->selectedOrder->customer_name }}</p>
                            <p>{{ $this->selectedOrder->shipping_address }}</p>
                            <p>{{ $this->selectedOrder->shipping_city }}, {{ $this->selectedOrder->shipping_department }}</p>
                            <p>{{ $this->selectedOrder->shipping_country }}</p>
                            <p class="mt-2 text-xs font-bold text-gray-400">Cel: <span class="text-gray-600">{{ $this->selectedOrder->customer_phone }}</span></p>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <h4 class="font-black text-gray-900 text-sm flex items-center gap-2">
                            <flux:icon.banknotes class="size-4 text-secondary" />
                            Resumen de Pago
                        </h4>
                        <div class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg border border-gray-100 space-y-2">
                            <div class="flex justify-between">
                                <span>Subtotal</span>
                                <span>${{ number_format($this->selectedOrder->subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Envío</span>
                                <span>{{ $this->selectedOrder->shipping_cost > 0 ? '$'.number_format($this->selectedOrder->shipping_cost, 0, ',', '.') : 'Gratis' }}</span>
                            </div>
                            <div class="flex justify-between pt-2 mt-2 border-t border-gray-200 font-bold text-gray-900">
                                <span>Total Pagado</span>
                                <span class="text-primary">${{ number_format($this->selectedOrder->total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <h4 class="font-black text-gray-900 text-sm flex items-center gap-2">
                        <flux:icon.shopping-bag class="size-4 text-secondary" />    
                        Productos Comprados
                    </h4>
                    <div class="divide-y divide-gray-100 border border-gray-100 rounded-xl overflow-hidden shadow-sm">
                        @foreach($this->selectedOrder->items as $item)
                            <div class="flex gap-4 p-3 bg-white items-center">
                                <div class="size-12 bg-gray-50 rounded-lg p-1 border border-gray-100 flex-shrink-0">
                                    <img src="{{ $item->product_image ?? 'https://exitocol.vtexassets.com/arquivos/ids/28955924/Arroz-Diana-500-gr-445117_a.jpg?v=638864002494600000' }}" alt="{{ $item->product_title }}" class="w-full h-full object-contain">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-gray-900 line-clamp-2">{{ $item->product_title }}</p>
                                    <p class="text-xs text-gray-500 font-medium mt-0.5">{{ $item->quantity }} x ${{ number_format($item->unit_price, 0, ',', '.') }}</p>
                                </div>
                                <div class="font-black text-sm text-primary">
                                    ${{ number_format($item->subtotal, 0, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t border-gray-100">
                    <flux:modal.close>
                        <flux:button variant="ghost" class="font-bold text-gray-500 hover:text-gray-900">Cerrar</flux:button>
                    </flux:modal.close>
                </div>
            </div>
        @endif
    </flux:modal>
</div>