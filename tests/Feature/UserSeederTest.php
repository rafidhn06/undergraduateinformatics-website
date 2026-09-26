<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_preserves_existing_admin_password(): void
    {
        User::create(['email' => 'bif@telkomuniversity.ac.id', 'password' => 'kata-sandi-rahasia']);

        $this->seed(UserSeeder::class);

        $this->assertTrue(Hash::check('kata-sandi-rahasia', User::where('email', 'bif@telkomuniversity.ac.id')->first()->password));
    }

    public function test_seeder_creates_missing_admin_account(): void
    {
        $this->seed(UserSeeder::class);

        $this->assertNotNull(User::where('email', 'bif@telkomuniversity.ac.id')->first());
    }
}
