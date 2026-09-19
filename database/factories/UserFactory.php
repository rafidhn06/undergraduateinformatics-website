<?php

namespace Database\Factories;

use App\Models\PasswordRecovery;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'password_recovery_id' => 0,
            'remember_token' => Str::random(10),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (User $user): void {
            if (PasswordRecovery::query()->whereKey($user->password_recovery_id)->exists()) {
                return;
            }

            $recovery = PasswordRecovery::query()->create([
                'user_id' => $user->id,
                'first_question' => 'What is your favorite color?',
                'second_question' => 'What is your favorite food?',
                'first_answer' => 'blue',
                'second_answer' => 'rice',
            ]);

            $user->forceFill(['password_recovery_id' => $recovery->id])->saveQuietly();
        });
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'remember_token' => null,
        ]);
    }
}
