<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class CartNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_notification_is_hidden_by_default(): void
    {
        Volt::test('cart-notification')
            ->assertSet('visible', false);
    }

    public function test_cart_notification_becomes_visible_on_add_to_cart_event(): void
    {
        Volt::test('cart-notification')
            ->dispatch('add-to-cart', [
                'productId' => 1,
                'quantity' => 2,
                'name' => 'Arroz Diana 500g',
                'image' => null,
                'price' => 3500.0,
            ])
            ->assertSet('visible', true)
            ->assertSet('productName', 'Arroz Diana 500g')
            ->assertSet('productPrice', 3500.0)
            ->assertSet('quantity', 2);
    }

    public function test_cart_notification_can_be_dismissed(): void
    {
        Volt::test('cart-notification')
            ->dispatch('add-to-cart', [
                'productId' => 1,
                'quantity' => 1,
                'name' => 'Producto de prueba',
                'image' => null,
                'price' => 5000.0,
            ])
            ->assertSet('visible', true)
            ->call('dismiss')
            ->assertSet('visible', false);
    }
}
