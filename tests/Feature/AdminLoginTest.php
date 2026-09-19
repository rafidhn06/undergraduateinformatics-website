<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_redirects_to_admin_dashboard(): void
    {
        User::create([
            'email' => 'admin@example.com',
            'password' => 'password',
            'password_recovery_id' => 1,
        ]);

        $response = $this->post('/admin/login', ['email' => 'admin@example.com', 'password' => 'password']);

        $response->assertRedirect(route('admin.datasets.index'));
    }

    public function test_login_rejects_invalid_credentials(): void
    {
        $response = $this->post('/admin/login', ['email' => 'nobody@example.com', 'password' => 'wrong']);

        $response->assertSessionHas('error');
    }

    public function test_authenticated_user_visiting_login_is_redirected_to_admin_dashboard(): void
    {
        User::create([
            'email' => 'admin@example.com',
            'password' => 'password',
            'password_recovery_id' => 1,
        ]);

        $response = $this->actingAs(User::first())->get('/admin/login');

        $response->assertRedirect(route('admin.datasets.index'));
    }

    public function test_logout_uses_post_and_redirects_home(): void
    {
        User::create([
            'email' => 'admin@example.com',
            'password' => 'password',
            'password_recovery_id' => 1,
        ]);

        $response = $this->actingAs(User::first())->post('/admin/logout');

        $response->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_logout_via_get_is_not_routed(): void
    {
        User::create([
            'email' => 'admin@example.com',
            'password' => 'password',
            'password_recovery_id' => 1,
        ]);

        $this->actingAs(User::first())->get('/admin/logout')->assertNotFound();
    }
}
