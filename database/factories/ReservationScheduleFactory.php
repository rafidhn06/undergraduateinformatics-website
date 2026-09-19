<?php

namespace Database\Factories;

use App\Models\ReservationSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationScheduleFactory extends Factory
{
    protected $model = ReservationSchedule::class;

    public function definition(): array
    {
        return [
            'date' => fake()->date(),
            'shift' => fake()->randomElement(['09:00:00', '13:00:00', '15:00:00']),
            'requested_by' => fake()->name(),
            'agenda' => fake()->sentence(),
        ];
    }
}
