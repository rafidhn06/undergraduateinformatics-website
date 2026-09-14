<?php

namespace Tests\Unit\Services;

use App\Models\MsFormDefinition;
use App\Services\MsForms\FormDefinitionService;
use App\Services\MsForms\MsFormsException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\Concerns\FakesMicrosoftForms;
use Tests\TestCase;

class FormDefinitionServiceTest extends TestCase
{
    use FakesMicrosoftForms;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
    }

    public function test_resolve_reads_the_stored_payload_without_http(): void
    {
        Http::fake($this->microsoftEndpoints());
        MsFormDefinition::query()->create([
            'kind' => 'feedback',
            'link' => 'https://forms.office.com/r/abc123',
            'payload' => ['link' => 'https://forms.office.com/r/abc123', 'title' => ['text' => 'stored title']],
            'fetched_at' => now(),
        ]);
        Http::preventStrayRequests();

        $payload = app(FormDefinitionService::class)->resolve('feedback');

        $this->assertSame('stored title', $payload['title']['text']);
        Http::assertNothingSent();
    }

    public function test_resolve_throws_when_no_row_is_stored(): void
    {
        $this->expectException(MsFormsException::class);

        app(FormDefinitionService::class)->resolve('feedback');
    }

    public function test_refresh_fetches_and_writes_payload_and_fetched_at(): void
    {
        Http::fake($this->microsoftEndpoints());

        $payload = app(FormDefinitionService::class)->refresh('feedback', 'https://forms.office.com/r/abc123');

        $this->assertSame('this is form title', $payload['title']['text']);
        $row = MsFormDefinition::query()->where('kind', 'feedback')->firstOrFail();
        $this->assertSame('https://forms.office.com/r/abc123', $row->link);
        $this->assertSame('this is form title', $row->payload['title']['text']);
        $this->assertNotNull($row->fetched_at);
    }

    public function test_refresh_failure_preserves_the_prior_row(): void
    {
        Http::fake($this->microsoftEndpoints());
        MsFormDefinition::query()->create([
            'kind' => 'feedback',
            'link' => 'https://forms.office.com/r/abc123',
            'payload' => ['link' => 'https://forms.office.com/r/abc123', 'title' => ['text' => 'old']],
            'fetched_at' => now()->subDay(),
        ]);
        $this->microsoftUnreachable = true;

        try {
            app(FormDefinitionService::class)->refresh('feedback', 'https://forms.office.com/r/abc123');
            $this->fail('Expected MsFormsException');
        } catch (MsFormsException) {
        }

        $this->assertSame('old', MsFormDefinition::query()->where('kind', 'feedback')->firstOrFail()->payload['title']['text']);
    }
}
