<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class AdminOrderManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_orders_page_is_rendered_for_authorized_user(): void
    {
        // Suppress deprecations warnings or errors if any
        $user = User::factory()->create();

        // Grant permissions needed
        $viewPermission = Permission::create(['name' => 'orders:view']);
        $user->givePermissionTo($viewPermission);

        $response = $this->actingAs($user)->get(route('admin.orders'));

        $response->assertStatus(200);
    }
}
