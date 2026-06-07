<?php

use App\Models\Order;
use App\Models\OrderItem;
use Flux\Flux;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.admin')] class extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public ?int $selectedOrderId = null;

    public string $newStatus = '';

    public array $consolidatedStatuses = ['pending', 'paid'];

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

    #[Computed]
    public function whatsappMessage(): string
    {
        $order = $this->selectedOrder;
        if (! $order) {
            return '';
        }

        $text = "*Pedido:* {$order->order_number}\n\n";
        $text .= "*Productos:*\n";
        foreach ($order->items as $item) {
            $text .= "✔️ *{$item->quantity}* - {$item->product_title}\n";
        }

        $text .= "\n*Cliente:* {$order->customer_name}";
        $text .= "\n*Dirección:* {$order->shipping_address}, {$order->shipping_notes}, {$order->shipping_city}, {$order->shipping_department}";
        $text .= "\n*Pts:* {$order->pts}";

        return urlencode($text);
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
            'pending' => 'Pendiente',
            'paid' => 'Pagado',
            'processing' => 'En Proceso',
            'shipped' => 'Enviado',
            'delivered' => 'Entregado',
            'cancelled' => 'Cancelado',
            default => ucfirst($status),
        };
    }

    public function statusColor(string $status): string
    {
        return match ($status) {
            'pending' => 'zinc',
            'paid' => 'blue',
            'processing' => 'yellow',
            'shipped' => 'indigo',
            'delivered' => 'green',
            'cancelled' => 'red',
            default => 'zinc',
        };
    }

    #[Computed]
    public function consolidatedProducts(): Collection
    {
        if (empty($this->consolidatedStatuses)) {
            return collect();
        }

        return OrderItem::whereHas('order', function ($query) {
            $query->whereIn('status', $this->consolidatedStatuses);
        })
            ->select('product_title', 'unit_price')
            ->selectRaw('SUM(quantity) as total_quantity')
            ->selectRaw('SUM(subtotal) as total_amount')
            ->groupBy('product_title', 'unit_price')
            ->orderBy('product_title')
            ->get();
    }

    #[Computed]
    public function consolidatedTotal(): float
    {
        return (float) $this->consolidatedProducts->sum('total_amount');
    }

    #[Computed]
    public function consolidatedWhatsappMessage(): string
    {
        $products = $this->consolidatedProducts;
        if ($products->isEmpty()) {
            return '';
        }

        $statusLabels = collect($this->consolidatedStatuses)
            ->map(fn ($status) => $this->statusLabel($status))
            ->join(', ');

        $text = "*CONSOLIDADO DE PRODUCTOS*\n";
        $text .= "*Estados:* {$statusLabels}\n";
        $text .= "------------------------------------------\n";

        foreach ($products as $item) {
            $formattedPrice = number_format($item->unit_price, 0, ',', '.');
            $formattedTotal = number_format($item->total_amount, 0, ',', '.');

            $text .= "{$item->total_quantity} - ";
            $text .= "{$item->product_title}\n";
            /* $text .= "✔️ *Cant:* {$item->total_quantity}  *P. Unit:* \${$formattedPrice}  *Total:* \${$formattedTotal}\n\n"; */
            
        }

        $text .= "------------------------------------------\n";
        $formattedGrandTotal = number_format($this->consolidatedTotal(), 0, ',', '.');
        /* $text .= "*VALOR TOTAL AL PÚBLICO:* \${$formattedGrandTotal}"; */

        return urlencode($text);
    }

    public function with(): array
    {
        $query = Order::with('items')
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('order_number', 'like', '%'.$this->search.'%')
                        ->orWhere('customer_name', 'like', '%'.$this->search.'%')
                        ->orWhere('customer_email', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest();

        return [
            'orders' => $query->paginate(12),
            'statuses' => ['pending', 'paid', 'processing', 'shipped', 'delivered', 'cancelled'],
        ];
    }
};
