<?php

namespace Database\Seeders;

use App\Models\FeedbackLink;
use Illuminate\Database\Seeder;

class FeedbackLinkSeeder extends Seeder
{
    public function run(): void
    {
        $link = env('DUMMY_FORM_LINK', 'https://forms.office.com/r/cZuHFE5E3Z');

        $existing = FeedbackLink::query()->first();

        if ($existing !== null) {
            $existing->update(['link' => $link]);

            return;
        }

        FeedbackLink::create(['link' => $link]);
    }
}