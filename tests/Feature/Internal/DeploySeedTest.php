<?php

namespace Tests\Feature\Internal;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class DeploySeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_rejects_seed_without_token(): void
    {
        $response = $this->postJson('/internal/deploy', ['seed' => true]);

        $response->assertStatus(401);
    }

    public function test_runs_seed_on_fresh_database(): void
    {
        Config::set('deploy.token', 'secret-token');

        $response = $this->postJson('/internal/deploy', ['seed' => true], [
            'Authorization' => 'Bearer secret-token',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['ok' => true]);
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('feedback_links', 1);
        $this->assertDatabaseCount('reservation_links', 1);
    }

    public function test_repeats_seed_when_users_exist(): void
    {
        Config::set('deploy.token', 'secret-token');
        $this->seed();

        $response = $this->postJson('/internal/deploy', ['seed' => true], [
            'Authorization' => 'Bearer secret-token',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['ok' => true]);
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('feedback_links', 1);
        $this->assertDatabaseCount('reservation_links', 1);
    }
}
