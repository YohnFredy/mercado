<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_page_is_rendered_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.products'));

        $response->assertStatus(200);
    }
}
