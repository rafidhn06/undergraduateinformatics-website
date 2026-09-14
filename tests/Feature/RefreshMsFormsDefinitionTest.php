<?php

namespace Tests\Feature;

use App\Models\FeedbackLink;
use App\Models\ReservationLink;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\Concerns\FakesMicrosoftForms;
use Tests\TestCase;

class RefreshMsFormsDefinitionTest extends TestCase
{
    use FakesMicrosoftForms;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
        FeedbackLink::query()->delete();
        ReservationLink::query()->delete();
    }

    public function test_command_writes_the_definition_row(): void
    {
        Http::fake($this->microsoftEndpoints());
        FeedbackLink::create(['link' => 'https://forms.office.com/r/abc123']);

        $this->artisan('msforms:refresh-definition')->assertSuccessful();

        $row = \App\Models\MsFormDefinition::query()->where('kind', 'feedback')->firstOrFail();
        $this->assertSame('https://forms.office.com/r/abc123', $row->link);
        $this->assertSame('https://forms.office.com/r/abc123', $row->payload['link']);
        $this->assertNotNull($row->fetched_at);
    }

    public function test_command_succeeds_when_no_feedback_link_is_configured(): void
    {
        $this->artisan('msforms:refresh-definition')->assertSuccessful();
    }

    public function test_command_keeps_existing_row_when_microsoft_is_unreachable(): void
    {
        Http::fake(['https://forms.office.com/r/*' => Http::response('', 500)]);
        FeedbackLink::create(['link' => 'https://forms.office.com/r/abc123']);
        \App\Models\MsFormDefinition::query()->create([
            'kind' => 'feedback',
            'link' => 'https://forms.office.com/r/abc123',
            'payload' => ['link' => 'https://forms.office.com/r/abc123', 'stale' => true],
            'fetched_at' => now()->subDay(),
        ]);

        $this->artisan('msforms:refresh-definition')->assertFailed();

        $this->assertTrue(\App\Models\MsFormDefinition::query()->where('kind', 'feedback')->firstOrFail()->payload['stale']);
    }
}
