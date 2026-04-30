<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminOrderManagerTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        return $user;
    }

    public function test_order_manager_page_renders(): void
    {
        $this->actingAsAdmin();

        $this->get('/admin/orders')
            ->assertOk()
            ->assertSeeLivewire('pages::admin.orders.order-manager');
    }

    public function test_orders_are_listed(): void
    {
        $this->actingAsAdmin();

        Order::factory()->count(3)->create();

        Livewire::test('pages::admin.orders.order-manager')
            ->assertViewHas('orders', fn ($orders) => $orders->total() === 3);
    }

    public function test_search_filters_orders_by_customer_name(): void
    {
        $this->actingAsAdmin();

        Order::factory()->create(['customer_name' => 'Juan Perez']);
        Order::factory()->create(['customer_name' => 'Maria Lopez']);

        Livewire::test('pages::admin.orders.order-manager')
            ->set('search', 'Juan')
            ->assertViewHas('orders', fn ($orders) => $orders->total() === 1);
    }

    public function test_search_filters_orders_by_order_number(): void
    {
        $this->actingAsAdmin();

        $order = Order::factory()->create(['order_number' => 'ORD-20260101-0001']);
        Order::factory()->create(['order_number' => 'ORD-20260202-0002']);

        Livewire::test('pages::admin.orders.order-manager')
            ->set('search', 'ORD-20260101')
            ->assertViewHas('orders', fn ($orders) => $orders->total() === 1
                && $orders->first()->id === $order->id);
    }

    public function test_status_filter_works(): void
    {
        $this->actingAsAdmin();

        Order::factory()->create(['status' => 'pending']);
        Order::factory()->create(['status' => 'pending']);
        Order::factory()->create(['status' => 'delivered']);

        Livewire::test('pages::admin.orders.order-manager')
            ->set('statusFilter', 'pending')
            ->assertViewHas('orders', fn ($orders) => $orders->total() === 2);
    }

    public function test_view_order_sets_selected_order_id_and_new_status(): void
    {
        $this->actingAsAdmin();

        $order = Order::factory()->create(['status' => 'pending']);

        Livewire::test('pages::admin.orders.order-manager')
            ->call('viewOrder', $order->id)
            ->assertSet('selectedOrderId', $order->id)
            ->assertSet('newStatus', 'pending');
    }

    public function test_update_status_changes_order_status(): void
    {
        $this->actingAsAdmin();

        $order = Order::factory()->create(['status' => 'pending']);

        Livewire::test('pages::admin.orders.order-manager')
            ->call('viewOrder', $order->id)
            ->set('newStatus', 'processing')
            ->call('updateStatus');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'processing',
        ]);
    }

    public function test_update_status_to_paid_sets_paid_at(): void
    {
        $this->actingAsAdmin();

        $order = Order::factory()->create(['status' => 'pending', 'paid_at' => null]);

        Livewire::test('pages::admin.orders.order-manager')
            ->call('viewOrder', $order->id)
            ->set('newStatus', 'paid')
            ->call('updateStatus');

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'paid']);
        $this->assertNotNull($order->fresh()->paid_at);
    }

    public function test_update_status_to_paid_does_not_overwrite_existing_paid_at(): void
    {
        $this->actingAsAdmin();

        $paidAt = now()->subDays(1);
        $order = Order::factory()->create(['status' => 'processing', 'paid_at' => $paidAt]);

        Livewire::test('pages::admin.orders.order-manager')
            ->call('viewOrder', $order->id)
            ->set('newStatus', 'paid')
            ->call('updateStatus');

        $this->assertEquals(
            $paidAt->toDateTimeString(),
            $order->fresh()->paid_at->toDateTimeString()
        );
    }
}
