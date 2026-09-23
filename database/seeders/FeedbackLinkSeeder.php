<?php

namespace Database\Seeders;

use App\Models\FeedbackLink;
use Illuminate\Database\Seeder;

class FeedbackLinkSeeder extends Seeder
{
    public function run(): void
    {
        $link = config('forms.dummy_feedback_link');

        $existing = FeedbackLink::query()->first();

        if ($existing !== null) {
            $existing->update(['link' => $link]);

            return;
        }

        FeedbackLink::create(['link' => $link]);
    }
}