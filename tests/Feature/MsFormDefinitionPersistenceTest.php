<?php

namespace Tests\Feature;

use App\Models\MsFormDefinition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MsFormDefinitionPersistenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_persists_a_definition_row_by_kind(): void
    {
        MsFormDefinition::query()->create([
            'kind' => 'feedback',
            'link' => 'https://forms.office.com/r/abc123',
            'payload' => ['link' => 'https://forms.office.com/r/abc123', 'title' => ['text' => 'Hello']],
            'fetched_at' => now(),
        ]);

        $this->assertDatabaseHas('ms_form_definitions', ['kind' => 'feedback']);
        $this->assertSame('Hello', MsFormDefinition::query()->where('kind', 'feedback')->firstOrFail()->payload['title']['text']);
    }
}
