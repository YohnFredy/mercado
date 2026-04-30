<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_number' => 'ORD-'.now()->format('Ymd').'-'.str_pad(fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'customer_phone' => fake()->phoneNumber(),
            'customer_document' => fake()->numerify('#########'),
            'shipping_address' => fake()->streetAddress(),
            'shipping_country' => 'Colombia',
            'shipping_department' => fake()->state(),
            'shipping_city' => fake()->city(),
            'shipping_notes' => null,
            'subtotal' => fake()->randomFloat(2, 5000, 500000),
            'shipping_cost' => fake()->randomElement([0, 5000, 8000, 12000]),
            'total' => fn (array $attrs) => $attrs['subtotal'] + $attrs['shipping_cost'],
            'status' => fake()->randomElement(['pending', 'paid', 'processing', 'shipped', 'delivered', 'cancelled']),
            'payment_method' => fake()->randomElement(['cash', 'transfer', 'card']),
            'paid_at' => null,
        ];
    }

    /** State: mark order as paid with a paid_at timestamp. */
    public function paid(): static
    {
        return $this->state(fn () => [
            'status' => 'paid',
            'paid_at' => now()->subHours(rand(1, 48)),
        ]);
    }

    /** State: mark as delivered. */
    public function delivered(): static
    {
        return $this->state(fn () => [
            'status' => 'delivered',
            'paid_at' => now()->subDays(2),
        ]);
    }
}
