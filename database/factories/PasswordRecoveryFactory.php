<?php

namespace Database\Factories;

use App\Models\PasswordRecovery;
use Illuminate\Database\Eloquent\Factories\Factory;

class PasswordRecoveryFactory extends Factory
{
    protected $model = PasswordRecovery::class;

    public function definition(): array
    {
        return [
            'user_id' => UserFactory::new(),
            'first_question' => fake()->sentence(4),
            'second_question' => fake()->sentence(4),
            'first_answer' => fake()->word(),
            'second_answer' => fake()->word(),
        ];
    }
}
