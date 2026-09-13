<?php

namespace Tests\Feature\Internal;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DeploySeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_rejects_seed_without_token(): void
    {
        $response = $this->postJson('/internal/deploy', ['seed' => true]);

        $response->assertStatus(401);
    }

    public function test_refuses_seed_when_users_exist(): void
    {
        Config::set('deploy.token', 'secret-token');
        Schema::disableForeignKeyConstraints();
        DB::table('users')->insert([
            'password_recovery_id' => 1,
            'email' => 'seeded@example.com',
            'password' => 'secret',
        ]);
        Schema::enableForeignKeyConstraints();

        $response = $this->postJson('/internal/deploy', ['seed' => true], [
            'Authorization' => 'Bearer secret-token',
        ]);

        $response->assertStatus(422);
        $response->assertJson(['ok' => false, 'error' => 'Already seeded']);
    }
}
