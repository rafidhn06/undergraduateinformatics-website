<?php

namespace Database\Factories;

use App\Models\MsFormDefinition;
use Illuminate\Database\Eloquent\Factories\Factory;

class MsFormDefinitionFactory extends Factory
{
    protected $model = MsFormDefinition::class;

    public function definition(): array
    {
        return [
            'kind' => fake()->unique()->word(),
            'link' => fake()->url(),
            'payload' => null,
            'fetched_at' => null,
        ];
    }
}
