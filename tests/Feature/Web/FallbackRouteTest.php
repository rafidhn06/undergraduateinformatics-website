<?php

namespace Tests\Feature\Web;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FallbackRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_unknown_public_url_renders_app_wrapper_with_404_status(): void
    {
        $response = $this->get('/halaman-tidak-ada');

        $response->assertStatus(404);
        $response->assertViewIs('app');
        $response->assertSee('"seeds":[]', false);
    }

    public function test_unknown_public_url_renders_empty_seeds(): void
    {
        $response = $this->get('/halaman-tidak-ada');

        $response->assertStatus(404);
        $response->assertSee('"seeds":[]', false);
    }

    public function test_unknown_api_url_keeps_json_404(): void
    {
        $response = $this->getJson('/api/tidak-ada');

        $response->assertStatus(404);
        $response->assertHeader('content-type', 'application/json');
    }

    public function test_unknown_admin_url_keeps_laravel_404(): void
    {
        $response = $this->get('/admin/tidak-ada');

        $response->assertStatus(404);
        $response->assertDontSee('window.__INITIAL_DATA__', false);
    }
}