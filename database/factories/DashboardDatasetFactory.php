<?php

namespace Database\Factories;

use App\Models\DashboardDataset;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DashboardDatasetFactory extends Factory
{
    protected $model = DashboardDataset::class;

    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->randomNumber(5),
            'sheet_name' => fake()->word(),
            'chart_type' => 'bar',
            'x_label' => fake()->word(),
            'y_label' => fake()->word(),
            'description' => null,
        ];
    }
}
