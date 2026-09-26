<?php

namespace Tests\Feature;

use App\Models\PasswordRecovery;
use App\Models\User;
use Database\Seeders\PasswordRecoverySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordRecoverySeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_preserves_existing_recovery_answers(): void
    {
        $user = User::create(['email' => 'bif@telkomuniversity.ac.id', 'password' => 'secret']);
        PasswordRecovery::create([
            'user_id' => $user->id,
            'first_question' => 'Q1 admin',
            'second_question' => 'Q2 admin',
            'first_answer' => 'A1 admin',
            'second_answer' => 'A2 admin',
        ]);

        $this->seed(PasswordRecoverySeeder::class);

        $recovery = PasswordRecovery::where('user_id', $user->id)->first();
        $this->assertSame('Q1 admin', $recovery->first_question);
        $this->assertSame('A1 admin', $recovery->first_answer);
    }
}
