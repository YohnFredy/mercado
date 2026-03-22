<?php

namespace Tests\Feature\Livewire\Category;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MenuTest extends TestCase
{
    use RefreshDatabase;

    public function test_desktop_menu_component_renders_successfully(): void
    {
        Livewire::test('category.menu')
            ->assertOk()
            ->assertSeeHtml('Categorías');
    }

    public function test_desktop_menu_loads_root_categories(): void
    {
        Category::create([
            'name' => 'Electrónica',
            'slug' => 'electronica',
            'is_active' => true,
        ]);

        Livewire::test('category.menu')
            ->assertOk()
            ->assertSee('Electrónica');
    }

    public function test_desktop_menu_loads_categories_with_children(): void
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

        Livewire::test('category.menu')
            ->assertOk()
            ->assertSee('Hogar')
            ->assertSee('Cocina')
            ->assertSee('Ollas');
    }
}
