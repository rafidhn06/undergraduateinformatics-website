<?php

namespace Tests\Feature\Internal;

use Illuminate\Support\Facades\Config;
use Tests\TestCase;
use ZipArchive;

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

    public function test_quick_run_executes_only_requested_steps(): void
    {
        Config::set('deploy.token', 'secret-token');
        $deployDir = sys_get_temp_dir() . '/deploy-test-' . uniqid();
        mkdir($deployDir, 0777, true);

        foreach (['app.zip', 'public.zip'] as $name) {
            $zip = new ZipArchive();
            $zip->open($deployDir . '/' . $name, ZipArchive::CREATE);
            $zip->addFromString('placeholder.txt', 'placeholder');
            $zip->close();
        }
        Config::set('deploy.directory', $deployDir);

        $response = $this->postJson('/internal/deploy', ['only' => ['storage-link']], [
            'Authorization' => 'Bearer secret-token',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('ok', true);

        $steps = collect($response->json('steps'))->mapWithKeys(fn ($step) => [$step['name'] => $step]);
        $this->assertSame('Skipped', $steps['extract-app']['detail']);
        $this->assertSame('Skipped', $steps['seed']['detail']);
        $this->assertTrue($steps['storage-link']['ok']);
        $this->assertArrayHasKey('duration_ms', $steps['storage-link']);
    }
}
