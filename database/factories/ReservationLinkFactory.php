<?php

namespace Database\Factories;

use App\Models\ReservationLink;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationLinkFactory extends Factory
{
    protected $model = ReservationLink::class;

    public function definition(): array
    {
        return [
            'link' => fake()->url(),
        ];
    }
}
