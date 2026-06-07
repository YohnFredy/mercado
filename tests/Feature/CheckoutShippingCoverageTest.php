<?php

namespace Tests\Feature;

use App\Livewire\Checkout\CheckoutShipping;
use App\Models\City;
use App\Models\Country;
use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CheckoutShippingCoverageTest extends TestCase
{
    use RefreshDatabase;

    private Country $country;

    private Department $activeDepartment;

    private Department $inactiveDepartment;

    private City $activeCity;

    private City $inactiveCity;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->country = Country::create(['name' => 'Colombia']);

        $this->activeDepartment = Department::create([
            'country_id' => $this->country->id,
            'name' => 'Valle del Cauca',
            'is_active' => true,
        ]);

        $this->inactiveDepartment = Department::create([
            'country_id' => $this->country->id,
            'name' => 'Chocó',
            'is_active' => false,
        ]);

        $this->activeCity = City::create([
            'department_id' => $this->activeDepartment->id,
            'name' => 'Cali',
            'cost' => 8000,
            'is_active' => true,
        ]);

        $this->inactiveCity = City::create([
            'department_id' => $this->activeDepartment->id,
            'name' => 'Buenaventura',
            'cost' => 15000,
            'is_active' => false,
        ]);

        $this->user = User::factory()->create();
    }

    /** Seeds a cart item in the session so the component does not redirect. */
    private function withCartItem(): void
    {
        session()->put('shopping_cart', [
            999 => [
                'id' => 999,
                'title' => 'Test Product',
                'price' => 50000,
                'pts' => 0,
                'quantity' => 1,
                'image' => null,
            ],
        ]);
    }

    #[Test]
    public function it_only_shows_active_departments_in_checkout(): void
    {
        $this->withCartItem();

        Livewire::actingAs($this->user)
            ->test(CheckoutShipping::class)
            ->assertSee('Valle del Cauca')
            ->assertDontSee('Chocó');
    }

    #[Test]
    public function it_only_shows_active_cities_for_selected_department(): void
    {
        $this->withCartItem();

        Livewire::actingAs($this->user)
            ->test(CheckoutShipping::class)
            ->set('department_id', $this->activeDepartment->id)
            ->assertSee('Cali')
            ->assertDontSee('Buenaventura');
    }

    #[Test]
    public function it_blocks_order_placement_when_city_is_inactive(): void
    {
        $this->withCartItem();

        // Force an inactive city into the component state (simulate a bypass attempt).
        Livewire::actingAs($this->user)
            ->test(CheckoutShipping::class)
            ->set('name', 'Juan Pérez')
            ->set('phone', '3001234567')
            ->set('address', 'Calle 5 # 10-20')
            ->set('department_id', $this->activeDepartment->id)
            ->set('city_id', $this->inactiveCity->id)
            ->call('placeOrder')
            ->assertHasErrors('city_id');
    }

    #[Test]
    public function it_does_not_show_inactive_department_in_checkout_list(): void
    {
        $this->withCartItem();

        Livewire::actingAs($this->user)
            ->test(CheckoutShipping::class)
            ->assertSee('Valle del Cauca')
            ->assertDontSee('Chocó');
    }

    #[Test]
    public function it_does_not_show_department_with_only_inactive_cities(): void
    {
        // Create a department that is active but has no active cities.
        $departmentNoActiveCities = Department::create([
            'country_id' => $this->country->id,
            'name' => 'Vaupés',
            'is_active' => true,
        ]);

        City::create([
            'department_id' => $departmentNoActiveCities->id,
            'name' => 'Mitú',
            'cost' => 20000,
            'is_active' => false,
        ]);

        $this->withCartItem();

        Livewire::actingAs($this->user)
            ->test(CheckoutShipping::class)
            ->assertDontSee('Vaupés');
    }
}
