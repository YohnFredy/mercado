<?php

namespace Tests\Feature\Livewire\Category;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MobileMenuTest extends TestCase
{
    use RefreshDatabase;

    public function test_mobile_menu_component_renders_successfully(): void
    {
        Livewire::test('category.mobile-menu')
            ->assertOk()
            ->assertSeeHtml('Categorías');
    }

    public function test_mobile_menu_loads_root_categories(): void
    {
        $category = Category::create([
            'name' => 'Electrónica',
            'slug' => 'electronica',
            'is_active' => true,
        ]);

        Livewire::test('category.mobile-menu')
            ->assertOk()
            ->assertSee('Electrónica');
    }

    public function test_mobile_menu_loads_categories_with_children(): void
    {
        $parent = Category::create([
            'name' => 'Hogar',
            'slug' => 'hogar',
            'is_active' => true,
        ]);

        $child = Category::create([
            'parent_id' => $parent->id,
            'name' => 'Cocina',
            'slug' => 'hogar-cocina',
            'is_active' => true,
        ]);

        $grandchild = Category::create([
            'parent_id' => $child->id,
            'name' => 'Ollas',
            'slug' => 'hogar-cocina-ollas',
            'is_active' => true,
        ]);

        Livewire::test('category.mobile-menu')
            ->assertOk()
            ->assertSee('Hogar')
            ->assertSee('Cocina')
            ->assertSee('Ollas');
    }

    public function test_mobile_menu_does_not_show_child_categories_as_root(): void
    {
        $parent = Category::create([
            'name' => 'Bebidas',
            'slug' => 'bebidas',
            'is_active' => true,
        ]);

        $child = Category::create([
            'parent_id' => $parent->id,
            'name' => 'Jugos',
            'slug' => 'bebidas-jugos',
            'is_active' => true,
        ]);

        $component = Livewire::test('category.mobile-menu');

        $categories = $component->get('categories');

        $this->assertCount(1, $categories);
        $this->assertEquals('Bebidas', $categories[0]['name']);
    }

    public function test_mobile_menu_renders_footer_link(): void
    {
        Livewire::test('category.mobile-menu')
            ->assertOk()
            ->assertSee('Ver todas las categorías');
    }

    public function test_mobile_menu_does_not_load_inactive_categories(): void
    {
        $activeCategory = Category::create([
            'name' => 'Categoría Activa',
            'slug' => 'categoria-activa',
            'is_active' => true,
        ]);

        $inactiveCategory = Category::create([
            'name' => 'Categoría Inactiva',
            'slug' => 'categoria-inactiva',
            'is_active' => false,
        ]);

        $activeParent = Category::create([
            'name' => 'Padre Activo',
            'slug' => 'padre-activo',
            'is_active' => true,
        ]);

        $inactiveChild = Category::create([
            'parent_id' => $activeParent->id,
            'name' => 'Hijo Inactivo',
            'slug' => 'hijo-inactivo',
            'is_active' => false,
        ]);

        $activeChild = Category::create([
            'parent_id' => $activeParent->id,
            'name' => 'Hijo Activo',
            'slug' => 'hijo-activo',
            'is_active' => true,
        ]);

        $inactiveGrandchild = Category::create([
            'parent_id' => $activeChild->id,
            'name' => 'Nieto Inactivo',
            'slug' => 'nieto-inactivo',
            'is_active' => false,
        ]);

        Livewire::test('category.mobile-menu')
            ->assertOk()
            ->assertSee('Categoría Activa')
            ->assertDontSee('Categoría Inactiva')
            ->assertSee('Padre Activo')
            ->assertDontSee('Hijo Inactivo')
            ->assertSee('Hijo Activo')
            ->assertDontSee('Nieto Inactivo');
    }
}
