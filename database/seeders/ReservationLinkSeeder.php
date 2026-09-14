<?php

namespace Database\Seeders;

use App\Models\ReservationLink;
use Illuminate\Database\Seeder;

class ReservationLinkSeeder extends Seeder
{
    public function run(): void
    {
        $link = env('DUMMY_FORM_LINK', 'https://forms.cloud.microsoft/pages/responsepage.aspx?id=D_6vkKPCCEG7mGzrTpTvFX8cu6Jzq2tJlX2QoRxK9bJUMFlTUkJFWlcxSjRFQ0RGSFhIVUM0Wk4zSC4u&route=shorturl');

        $existing = ReservationLink::query()->first();

        if ($existing !== null) {
            $existing->update(['link' => $link]);

            return;
        }

        ReservationLink::create(['link' => $link]);
    }
}