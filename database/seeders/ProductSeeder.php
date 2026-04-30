<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create 30 brands
        $brands = Brand::factory()->count(30)->create();

        // 2. Identify leaf categories (categories with no children)
        $leafCategories = Category::whereDoesntHave('children')->get();

        if ($leafCategories->isEmpty()) {
            $this->command->warn('No leaf categories found. Please run CategorySeeder first.');

            return;
        }

        // 3. For each leaf category, create 8 products
        foreach ($leafCategories as $category) {
            Product::factory()
                ->count(8)
                ->state(fn () => [
                    'brand_id' => $brands->random()->id,
                ])
                ->create()
                ->each(function ($product) use ($category) {
                    $product->categories()->attach($category->id);
                });
        }
    }
}
