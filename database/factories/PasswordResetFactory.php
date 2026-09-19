<?php

namespace Database\Factories;

use App\Models\PasswordReset;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PasswordResetFactory extends Factory
{
    protected $model = PasswordReset::class;

    public function definition(): array
    {
        return [
            'user_id' => UserFactory::new(),
            'token' => Str::random(48),
            'stage' => 'questions',
            'expires_at' => now()->addHour(),
        ];
    }
}
