<?php

namespace Database\Factories;

use App\Models\ImportantSection;
use Illuminate\Database\Eloquent\Factories\Factory;

class ImportantSectionFactory extends Factory
{
    protected $model = ImportantSection::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(3, true),
            'order_number' => fake()->numberBetween(1, 9999),
        ];
    }
}
