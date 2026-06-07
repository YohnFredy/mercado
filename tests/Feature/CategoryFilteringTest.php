<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class CategoryFilteringTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_page_displays_products_from_category()
    {
        $category = Category::create(['name' => 'Lácteos', 'slug' => 'lacteos']);
        $product = Product::factory()->create(['title' => 'Leche Entera']);
        $category->products()->attach($product);

        $otherCategory = Category::create(['name' => 'Carnes', 'slug' => 'carnes']);
        $otherProduct = Product::factory()->create(['title' => 'Carne de Res']);
        $otherCategory->products()->attach($otherProduct);

        Volt::test('pages::product.product-list', ['category' => $category])
            ->assertSee('Leche Entera')
            ->assertDontSee('Carne de Res');
    }

    public function test_filtering_by_subcategory()
    {
        $parent = Category::create(['name' => 'Lácteos', 'slug' => 'lacteos']);
        $child = Category::create(['name' => 'Quesos', 'slug' => 'quesos', 'parent_id' => $parent->id]);

        $productInParent = Product::factory()->create(['title' => 'Leche']);
        $productInChild = Product::factory()->create(['title' => 'Queso Campesino']);

        $parent->products()->attach($productInParent);
        $child->products()->attach($productInChild);

        Volt::test('pages::product.product-list', ['category' => $parent])
            ->assertSee('Leche')
            ->assertSee('Queso Campesino')
            ->assertSee('Quesos')
            ->assertSee(route('category', $child->slug));
    }

    public function test_filtering_by_brand()
    {
        $category = Category::create(['name' => 'Lácteos', 'slug' => 'lacteos']);
        $brandA = Brand::create(['name' => 'Colanta', 'slug' => 'colanta']);
        $brandB = Brand::create(['name' => 'Alpina', 'slug' => 'alpina']);

        $productA = Product::factory()->create(['title' => 'Leche Colanta', 'brand_id' => $brandA->id]);
        $productB = Product::factory()->create(['title' => 'Leche Alpina', 'brand_id' => $brandB->id]);

        $category->products()->attach([$productA->id, $productB->id]);

        Volt::test('pages::product.product-list', ['category' => $category])
            ->assertSee('Leche Colanta')
            ->assertSee('Leche Alpina')
            ->set('selectedBrands', [$brandA->id])
            ->assertSee('Leche Colanta')
            ->assertDontSee('Leche Alpina');
    }

    public function test_searching_products()
    {
        Product::factory()->create(['title' => 'Leche Deslactosada']);
        Product::factory()->create(['title' => 'Pan Tajado']);

        Volt::test('pages::product.product-list')
            ->assertSee('Leche Deslactosada')
            ->assertSee('Pan Tajado')
            ->set('search', 'Leche')
            ->assertSee('Leche Deslactosada')
            ->assertDontSee('Pan Tajado');
    }

    public function test_filtering_by_price()
    {
        $category = Category::factory()->create(['is_active' => true]);
        $brand = Brand::factory()->create(['is_active' => true]);

        // Price: 1000 * 1.1 = 1100
        $p1 = Product::factory()->create(['title' => 'Barato', 'selling_price_excl_vat' => 1000, 'vat_percentage' => 10, 'brand_id' => $brand->id, 'is_active' => true]);
        $p1->categories()->attach($category);

        // Price: 2000 * 1.1 = 2200
        $p2 = Product::factory()->create(['title' => 'Caro', 'selling_price_excl_vat' => 2000, 'vat_percentage' => 10, 'brand_id' => $brand->id, 'is_active' => true]);
        $p2->categories()->attach($category);

        Volt::test('pages::product.product-list', ['category' => $category])
            ->assertSee('Barato')
            ->assertSee('Caro')
            ->set('minPrice', 2000)
            ->assertDontSee('Barato')
            ->assertSee('Caro')
            ->set('minPrice', null)
            ->set('maxPrice', 1500)
            ->assertSee('Barato')
            ->assertDontSee('Caro');
    }

    public function test_sorting_products()
    {
        Product::factory()->create(['title' => 'A Product', 'selling_price_excl_vat' => 2000, 'vat_percentage' => 0]);
        Product::factory()->create(['title' => 'Z Product', 'selling_price_excl_vat' => 1000, 'vat_percentage' => 0]);

        Volt::test('pages::product.product-list')
            ->set('sort', 'name-az')
            ->assertSeeInOrder(['A Product', 'Z Product'])
            ->set('sort', 'price-low')
            ->assertSeeInOrder(['Z Product', 'A Product']);
    }

    public function test_recommended_sorting_option()
    {
        $normalProduct = Product::factory()->create([
            'title' => 'Producto Normal',
            'selling_price_excl_vat' => 1000,
            'vat_percentage' => 0,
            'discount_percentage' => 0,
            'is_active' => true,
        ]);

        $discountedProduct = Product::factory()->create([
            'title' => 'Producto Con Descuento',
            'selling_price_excl_vat' => 1500,
            'vat_percentage' => 0,
            'discount_percentage' => 20,
            'is_active' => true,
        ]);

        Volt::test('pages::product.product-list')
            ->assertSet('sort', 'recommended')
            ->assertSeeInOrder(['Producto Con Descuento', 'Producto Normal']);
    }
}
