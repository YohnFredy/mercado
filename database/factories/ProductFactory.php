<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(3);

        return [
            'brand_id' => Brand::factory(),
            'sku' => $this->faker->unique()->ean13(),
            'barcode' => $this->faker->unique()->ean13(),
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => $this->faker->paragraph(),
            'cost_price_excl_vat' => $this->faker->randomFloat(2, 50, 500),
            'selling_price_excl_vat' => $this->faker->randomFloat(2, 600, 2000),
            'vat_percentage' => 19.00,
            'discount_percentage' => $this->faker->randomFloat(2, 0, 20),
            'specifications' => [
                'Color' => $this->faker->safeColorName(),
                'Material' => $this->faker->word(),
                'Weight' => $this->faker->numberBetween(100, 5000).'g',
            ],
            'additional_information' => $this->faker->text(),
            'is_active' => true,
        ];
    }
}
