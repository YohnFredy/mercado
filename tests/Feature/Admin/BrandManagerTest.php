<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_brands_page_is_rendered_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.brands'));

        $response->assertStatus(200);
    }
}
