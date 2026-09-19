<?php

namespace Database\Factories;

use App\Models\DashboardDatasetItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class DashboardDatasetItemFactory extends Factory
{
    protected $model = DashboardDatasetItem::class;

    public function definition(): array
    {
        return [
            'dataset_id' => DashboardDatasetFactory::new(),
            'label' => fake()->word(),
            'value' => fake()->randomFloat(2, 0, 1000),
            'sort_order' => 1,
        ];
    }
}
