<?php

namespace Database\Factories;

use App\Models\FeedbackLink;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeedbackLinkFactory extends Factory
{
    protected $model = FeedbackLink::class;

    public function definition(): array
    {
        return [
            'link' => fake()->url(),
        ];
    }
}
