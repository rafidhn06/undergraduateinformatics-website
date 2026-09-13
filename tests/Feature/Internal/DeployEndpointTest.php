<?php

namespace Tests\Feature\Internal;

use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class DeployEndpointTest extends TestCase
{
    public function test_rejects_missing_token_with_401(): void
    {
        Config::set('deploy.token', 'secret-token');
        Config::set('deploy.directory', sys_get_temp_dir() . '/deploy-test-' . uniqid());

        $response = $this->postJson('/internal/deploy', []);

        $response->assertStatus(401);
        $response->assertJson(['ok' => false]);
    }

    public function test_rejects_wrong_token_with_401(): void
    {
        Config::set('deploy.token', 'secret-token');
        Config::set('deploy.directory', sys_get_temp_dir() . '/deploy-test-' . uniqid());

        $response = $this->postJson('/internal/deploy', [], [
            'Authorization' => 'Bearer wrong-token',
        ]);

        $response->assertStatus(401);
        $response->assertJson(['ok' => false]);
    }

    public function test_returns_422_when_archives_missing(): void
    {
        Config::set('deploy.token', 'secret-token');
        $emptyDir = sys_get_temp_dir() . '/deploy-test-' . uniqid();
        mkdir($emptyDir, 0777, true);
        Config::set('deploy.directory', $emptyDir);

        $response = $this->postJson('/internal/deploy', [], [
            'Authorization' => 'Bearer secret-token',
        ]);

        $response->assertStatus(422);
        $response->assertJson(['ok' => false]);
    }

    public function test_rollback_returns_422_when_prev_archives_missing(): void
    {
        Config::set('deploy.token', 'secret-token');
        $emptyDir = sys_get_temp_dir() . '/deploy-test-' . uniqid();
        mkdir($emptyDir, 0777, true);
        Config::set('deploy.directory', $emptyDir);

        $response = $this->postJson('/internal/deploy', ['rollback' => true], [
            'Authorization' => 'Bearer secret-token',
        ]);

        $response->assertStatus(422);
        $response->assertJson(['ok' => false]);
    }
}
