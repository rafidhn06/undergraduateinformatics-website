<?php

namespace Tests\Feature\Api;

use App\Models\FeedbackLink;
use App\Services\MsForms\FormDefinitionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\Concerns\FakesMicrosoftForms;
use Tests\TestCase;

class FeedbackControllerTest extends TestCase
{
    use FakesMicrosoftForms;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
        Http::fake($this->microsoftEndpoints());
        FeedbackLink::create(['link' => 'https://forms.office.com/r/abc123']);
    }

    public function test_form_returns_normalized_definition(): void
    {
        app(FormDefinitionService::class)->refresh('feedback', 'https://forms.office.com/r/abc123');

        $response = $this->getJson('/api/feedback');

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');
        $response->assertJsonStructure([
            'data' => [
                'link',
                'title',
                'description',
                'sections' => [
                    '*' => ['id', 'title', 'subtitle', 'questionIds'],
                ],
                'questions' => [
                    '*' => ['id', 'title', 'type', 'required', 'multiple', 'choices'],
                ],
            ],
        ]);
        $response->assertJsonPath('data.title.text', 'this is form title');
        $response->assertJsonPath('data.title.html', null);
        $response->assertJsonPath('data.description.text', 'this is form subtitle');
        $response->assertJsonPath('data.description.html', null);
        $response->assertJsonPath('data.questions.0.type', 'text');
        $response->assertJsonPath('data.questions.1.type', 'date');
        $response->assertJsonPath('data.questions.2.type', 'choice');
        $response->assertJsonPath('data.sections.0.id', 'r5ea034e6b67a462ba2a1ff857fad2490');
        $response->assertJsonPath('data.sections.0.questionIds', ['rd7645a06d5f94664917ff0617f123de3']);
        $response->assertJsonPath('data.sections.0.subtitle.text', 'this is section subtitle');
        $response->assertJsonPath('data.sections.1.questionIds', ['rb38b17fd578e4dfbb6b32d32f4dfc885']);
        $response->assertJsonPath('data.questions.2.choices.0.branchTargetId', null);
    }

    public function test_form_returns_branching_definition(): void
    {
        $this->runtimeFixture = 'form-definition-branching.raw.json';
        app(FormDefinitionService::class)->refresh('feedback', 'https://forms.office.com/r/abc123');

        $response = $this->getJson('/api/feedback');

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');
        $response->assertJsonPath('data.sections.0.id', 'p1');
        $response->assertJsonPath('data.sections.0.questionIds', ['q1']);
        $response->assertJsonPath('data.questions.0.choices.0.branchTargetId', 'end');
        $response->assertJsonPath('data.questions.0.choices.1.branchTargetId', 'q2');
    }

    public function test_form_resolves_short_link_when_stored(): void
    {
        FeedbackLink::query()->delete();
        FeedbackLink::create(['link' => 'https://forms.office.com/r/abc123']);
        app(FormDefinitionService::class)->refresh('feedback', 'https://forms.office.com/r/abc123');

        $response = $this->getJson('/api/feedback');

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');
        $response->assertJsonPath('data.link', 'https://forms.office.com/r/abc123');
        $response->assertJsonPath('data.title.text', 'this is form title');
        $response->assertJsonPath('data.title.html', null);
    }

    public function test_submit_forwards_answers_to_microsoft(): void
    {
        $response = $this->postJson('/api/feedback', [
            'answers' => [
                ['questionId' => 'r1', 'answer' => 'Budi'],
                ['questionId' => 'r2', 'answer' => ['Saran', 'Keluhan']],
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/responses')
                && $request['answers'] === json_encode([
                    ['questionId' => 'r1', 'answer1' => 'Budi'],
                    ['questionId' => 'r2', 'answer1' => json_encode(['Saran', 'Keluhan'])],
                ]);
        });
    }

    public function test_submit_requires_answers(): void
    {
        $response = $this->postJson('/api/feedback', []);

        $response->assertStatus(422);
        $response->assertJsonPath('status', 'error');
    }

    public function test_submit_rejects_answer_without_value(): void
    {
        $response = $this->postJson('/api/feedback', [
            'answers' => [
                ['questionId' => 'r1'],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonPath('status', 'error');
    }

    public function test_get_feedback_returns_404_when_no_definition_is_stored(): void
    {
        $response = $this->getJson('/api/feedback');

        $response->assertStatus(404);
        $response->assertJsonPath('status', 'error');
    }

    public function test_form_returns_404_when_no_link_configured(): void
    {
        FeedbackLink::query()->delete();

        $response = $this->getJson('/api/feedback');

        $response->assertStatus(404);
        $response->assertJsonPath('status', 'error');
    }

    public function test_get_feedback_returns_404_for_malformed_link_without_stored_definition(): void
    {
        FeedbackLink::query()->delete();
        FeedbackLink::create(['link' => 'not-a-url']);

        $response = $this->getJson('/api/feedback');

        $response->assertStatus(404);
        $response->assertJsonPath('status', 'error');
    }

    public function test_get_feedback_returns_404_for_non_microsoft_link_without_stored_definition(): void
    {
        FeedbackLink::query()->delete();
        FeedbackLink::create(['link' => 'https://example.com/forms/fake']);

        $response = $this->getJson('/api/feedback');

        $response->assertStatus(404);
        $response->assertJsonPath('status', 'error');
    }
}
