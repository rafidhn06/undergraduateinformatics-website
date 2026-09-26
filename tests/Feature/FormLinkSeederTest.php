<?php

namespace Tests\Feature;

use App\Models\FeedbackLink;
use App\Models\ReservationLink;
use Database\Seeders\FeedbackLinkSeeder;
use Database\Seeders\ReservationLinkSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormLinkSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_feedback_seeder_preserves_configured_link(): void
    {
        FeedbackLink::create(['link' => 'https://forms.office.com/admin-link']);

        $this->seed(FeedbackLinkSeeder::class);

        $this->assertSame('https://forms.office.com/admin-link', FeedbackLink::query()->first()->link);
    }

    public function test_reservation_seeder_preserves_configured_link(): void
    {
        ReservationLink::create(['link' => 'https://forms.office.com/admin-link']);

        $this->seed(ReservationLinkSeeder::class);

        $this->assertSame('https://forms.office.com/admin-link', ReservationLink::query()->first()->link);
    }
}
