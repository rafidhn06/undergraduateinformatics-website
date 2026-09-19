<?php

namespace Database\Factories;

use App\Models\ImportantLink;
use Illuminate\Database\Eloquent\Factories\Factory;

class ImportantLinkFactory extends Factory
{
    protected $model = ImportantLink::class;

    public function definition(): array
    {
        return [
            'important_section_id' => ImportantSectionFactory::new(),
            'name' => fake()->words(3, true),
            'link' => fake()->url(),
        ];
    }
}
