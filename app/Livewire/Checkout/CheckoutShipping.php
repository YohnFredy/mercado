<?php

namespace App\Livewire\Checkout;

use App\Models\City;
use App\Models\Country;
use App\Models\Department;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Datos de Envío')]
class CheckoutShipping extends Component
{
    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $document = '';

    public string $address = '';

    public int $country_id = 1;

    public ?int $department_id = null;

    public ?int $city_id = null;

    public string $notes = '';

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:255',
            'phone' => 'required|string|min:7|max:20',
            'document' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string|min:5|max:500',
            'department_id' => 'required|exists:departments,id',
            'city_id' => 'required|exists:cities,id',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    #[Computed]
    public function departments()
    {
        return Department::where('country_id', $this->country_id)->orderBy('name')->get();
    }

    #[Computed]
    public function cities()
    {
        if (! $this->department_id) {
            return collect();
        }

        return City::where('department_id', $this->department_id)->orderBy('name')->get();
    }

    #[Computed]
    public function shippingCost()
    {
        if (! $this->city_id) {
            return 0;
        }

        return City::find($this->city_id)?->cost ?? 0;
    }

    public function updatedDepartmentId(): void
    {
        $this->city_id = null;
    }

    public function mount(CartService $cart): void
    {
        if ($cart->getCount() === 0) {
            $this->redirectRoute('home', navigate: true);

            return;
        }

        /** @var User|null $user */
        $user = Auth::user();

        if ($user) {
            $this->name = $user->name;
            $this->email = $user->email ?? '';
        }
    }

    public function placeOrder(CartService $cart): void
    {
        $this->validate();

        if ($cart->getCount() === 0) {
            $this->redirectRoute('home', navigate: true);

            return;
        }

        $items = $cart->getItems();
        $subtotal = $cart->getTotal();
        $shipping = (float) $this->shippingCost();
        $total = $subtotal + $shipping;

        $selectedCity = City::find($this->city_id);
        $selectedDepartment = Department::find($this->department_id);
        $selectedCountry = Country::find($this->country_id);

        $order = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'customer_name' => $this->name,
            'customer_email' => $this->email ?: null,
            'customer_phone' => $this->phone,
            'customer_document' => $this->document ?: null,
            'shipping_address' => $this->address,
            'shipping_country' => $selectedCountry ? $selectedCountry->name : '',
            'shipping_department' => $selectedDepartment ? $selectedDepartment->name : '',
            'shipping_city' => $selectedCity ? $selectedCity->name : '',
            'shipping_notes' => $this->notes ?: null,
            'subtotal' => $subtotal,
            'shipping_cost' => $shipping,
            'total' => $total,
            'pts' => $cart->getTotalPts(),
            'status' => 'pending',
            'payment_method' => 'bank_transfer',
        ]);

        foreach ($items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['id'],
                'product_title' => $item['title'],
                'product_image' => $item['image'] ?? null,
                'unit_price' => $item['price'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['price'] * $item['quantity'],
            ]);
        }

        $cart->clear();
        $this->dispatch('cart-updated');

        $this->redirectRoute('checkout.confirmation', ['order' => $order->id], navigate: true);
    }

    public function render(CartService $cart)
    {
        $subtotal = $cart->getTotal();
        $shippingCost = $this->shippingCost();

        return view('livewire.checkout.checkout-shipping', [
            'items' => $cart->getItems(),
            'subtotal' => $subtotal,
            'shippingCost' => $shippingCost,
            'total' => $subtotal + $shippingCost,
            'totalPts' => $cart->getTotalPts(),
            'count' => $cart->getCount(),
        ]);
    }
}
