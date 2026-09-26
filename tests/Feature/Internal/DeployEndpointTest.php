<?php

namespace Tests\Feature\Internal;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;
use ZipArchive;

class DeployEndpointTest extends TestCase
{
    use RefreshDatabase;

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
        $this->assertArrayHasKey('pending_migrations', $response->json());
    }

    public function test_seed_runs_only_when_explicitly_requested(): void
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

        $response = $this->postJson('/internal/deploy', ['only' => ['migrate']], [
            'Authorization' => 'Bearer secret-token',
        ]);

        $response->assertStatus(200);
        $steps = collect($response->json('steps'))->mapWithKeys(fn ($step) => [$step['name'] => $step]);
        $this->assertSame('Skipped', $steps['seed']['detail']);
    }

    public function test_seed_runs_when_only_includes_seed(): void
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

        $response = $this->postJson('/internal/deploy', ['only' => ['seed']], [
            'Authorization' => 'Bearer secret-token',
        ]);

        $response->assertStatus(200);
        $steps = collect($response->json('steps'))->mapWithKeys(fn ($step) => [$step['name'] => $step]);
        $this->assertTrue($steps['seed']['ok']);
    }

    public function test_rejects_invalid_seed_class_with_422(): void
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

        foreach (['Nope\\Evil', 'App\\Models\\User', 'Database\\Seeders\\Missing'] as $class) {
            $response = $this->postJson('/internal/deploy', ['only' => ['seed'], 'seed_class' => $class], [
                'Authorization' => 'Bearer secret-token',
            ]);

            $response->assertStatus(422);
        }
    }

    public function test_accepts_namespaced_seeder_class(): void
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

        $response = $this->postJson('/internal/deploy', ['only' => ['seed'], 'seed_class' => 'Database\\Seeders\\TagSeeder'], [
            'Authorization' => 'Bearer secret-token',
        ]);

        $response->assertStatus(200);
        $this->assertNotNull(\App\Models\Tag::where('slug', 's1-informatika')->first());
    }
}
