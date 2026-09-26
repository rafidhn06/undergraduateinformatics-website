<?php

namespace Database\Seeders;

use App\Models\ReservationLink;
use Illuminate\Database\Seeder;

class ReservationLinkSeeder extends Seeder
{
    public function run(): void
    {
        $link = config('forms.dummy_reservation_link');

        $existing = ReservationLink::query()->first();

        if ($existing === null) {
            ReservationLink::create(['link' => $link]);
        }
    }
}