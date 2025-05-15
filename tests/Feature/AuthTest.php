<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_and_logout()
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);

        // Login
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);

        // Logout
        $response = $this->post('/logout');
        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_common_user_cannot_access_product_routes()
    {
        $user = User::factory()->create(['nivel' => 'common']);
        $this->actingAs($user);

        $response = $this->get('/products');
        $response->assertStatus(403);
    }

    public function test_admin_user_can_access_product_routes()
    {
        $user = User::factory()->create(['nivel' => 'adm']);
        $this->actingAs($user);

        $response = $this->get('/products');
        $response->assertStatus(200);
    }

    public function test_super_admin_user_can_access_user_management()
    {
        $user = User::factory()->create(['nivel' => 'super-admin']);
        $this->actingAs($user);

        $response = $this->get('/users');
        $response->assertStatus(200);
    }
}
