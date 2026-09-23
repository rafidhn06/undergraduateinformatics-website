<?php

namespace Tests\Unit\Services;

use App\Services\MsForms\MsFormsClient;
use App\Services\MsForms\MsFormsParseException;
use App\Services\MsForms\MsFormsRequestException;
use App\Services\MsForms\ResolvedFormTarget;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MsFormsClientTest extends TestCase
{
    private MsFormsClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->client = new MsFormsClient;
        Http::preventStrayRequests();
    }

    public function test_resolve_short_link_follows_redirect_and_extracts_target(): void
    {
        Http::fake([
            'https://forms.office.com/r/abc123' => Http::response('', 302, [
                'Location' => 'https://forms.office.com/pages/responsepage.aspx?id=FORM123',
            ]),
            'https://forms.office.com/pages/responsepage.aspx*' => Http::response(
                file_get_contents(base_path('tests/Fixtures/msforms/response-page.html')),
                200
            ),
        ]);

        $target = $this->client->resolve('https://forms.office.com/r/abc123');

        $this->assertSame('FORM123', $target->formId);
        $this->assertSame(
            'https://forms.cloud.microsoft/formapi/api/11111111-1111-1111-1111-111111111111/users/22222222-2222-2222-2222-222222222222/light',
            $target->apiBaseUrl
        );
    }

    public function test_resolve_full_link_does_not_require_a_redirect(): void
    {
        Http::fake([
            'https://forms.cloud.microsoft/pages/responsepage.aspx*' => Http::response(
                file_get_contents(base_path('tests/Fixtures/msforms/response-page.html')),
                200
            ),
        ]);

        $target = $this->client->resolve('https://forms.cloud.microsoft/pages/responsepage.aspx?id=FORM123');

        $this->assertSame('FORM123', $target->formId);
        $this->assertSame(
            'https://forms.cloud.microsoft/formapi/api/11111111-1111-1111-1111-111111111111/users/22222222-2222-2222-2222-222222222222/light',
            $target->apiBaseUrl
        );
    }

    public function test_resolve_rejects_bare_microsoft_suffix_host(): void
    {
        $this->expectException(MsFormsRequestException::class);
        $this->expectExceptionMessage('Invalid form link');

        $this->client->resolve('https://portal.microsoft/pages/responsepage.aspx?id=FORM123');
    }

    public function test_resolve_rejects_non_microsoft_hosts(): void
    {
        $this->expectException(MsFormsRequestException::class);
        $this->expectExceptionMessage('Invalid form link');

        $this->client->resolve('https://example.com/pages/responsepage.aspx?id=FORM123');
    }

    public function test_resolve_rejects_redirect_to_non_microsoft_host(): void
    {
        Http::fake([
            'https://forms.office.com/r/abc123' => Http::response('', 302, [
                'Location' => 'https://example.com/steal',
            ]),
        ]);

        $this->expectException(MsFormsRequestException::class);
        $this->expectExceptionMessage('Invalid form link');

        $this->client->resolve('https://forms.office.com/r/abc123');
    }

    public function test_resolve_throws_request_exception_when_page_fetch_fails(): void
    {
        Http::fake(['https://forms.office.com/r/broken' => Http::response('', 500)]);

        $this->expectException(MsFormsRequestException::class);

        $this->client->resolve('https://forms.office.com/r/broken');
    }

    public function test_resolve_throws_parse_exception_when_prefetch_url_is_missing(): void
    {
        Http::fake([
            'https://forms.office.com/pages/responsepage.aspx*' => Http::response(
                '<html><body>no form here</body></html>',
                200
            ),
        ]);

        $this->expectException(MsFormsParseException::class);

        $this->client->resolve('https://forms.office.com/pages/responsepage.aspx?id=NOPE');
    }

    public function test_resolve_wraps_connection_exception_as_request_exception(): void
    {
        Http::fake([
            'https://forms.office.com/r/connfail' => fn () => throw new ConnectionException('cURL error 6: Could not resolve host'),
        ]);

        $this->expectException(MsFormsRequestException::class);

        $this->client->resolve('https://forms.office.com/r/connfail');
    }

    public function test_fetch_form_definition_returns_raw_json(): void
    {
        Http::fake([
            'https://forms.cloud.microsoft/formapi/api/*/users/*/light/runtimeForms*' => Http::response(
                file_get_contents(base_path('tests/Fixtures/msforms/form-definition.raw.json')),
                200
            ),
        ]);

        $target = new ResolvedFormTarget(
            'https://forms.cloud.microsoft/formapi/api/11111111-1111-1111-1111-111111111111/users/22222222-2222-2222-2222-222222222222/light',
            'FORM123',
        );

        $raw = $this->client->fetchFormDefinition($target);

        $this->assertCount(3, $raw['questions']);
    }

    public function test_fetch_form_definition_throws_request_exception_on_failure(): void
    {
        Http::fake([
            'https://forms.cloud.microsoft/formapi/api/*/users/*/light/runtimeForms*' => Http::response('', 403),
        ]);

        $target = new ResolvedFormTarget(
            'https://forms.cloud.microsoft/formapi/api/11111111-1111-1111-1111-111111111111/users/22222222-2222-2222-2222-222222222222/light',
            'FORM123',
        );

        $this->expectException(MsFormsRequestException::class);

        $this->client->fetchFormDefinition($target);
    }

    public function test_fetch_form_definition_wraps_connection_exception(): void
    {
        Http::fake([
            'https://forms.cloud.microsoft/formapi/api/*/users/*/light/runtimeForms*' => fn () => throw new ConnectionException('cURL error 6: Could not resolve host'),
        ]);

        $target = new ResolvedFormTarget(
            'https://forms.cloud.microsoft/formapi/api/11111111-1111-1111-1111-111111111111/users/22222222-2222-2222-2222-222222222222/light',
            'FORM123',
        );

        $this->expectException(MsFormsRequestException::class);

        $this->client->fetchFormDefinition($target);
    }

    public function test_submit_answers_posts_expected_payload(): void
    {
        Http::fake([
            'https://forms.cloud.microsoft/formapi/api/*/users/*/forms(*)/responses' => Http::response('', 201),
        ]);

        $target = new ResolvedFormTarget(
            'https://forms.cloud.microsoft/formapi/api/11111111-1111-1111-1111-111111111111/users/22222222-2222-2222-2222-222222222222/light',
            'FORM123',
        );

        $this->client->submitAnswers(
            $target,
            [['questionId' => 'r1', 'answer1' => 'Budi']],
            '2026-08-25T00:00:00+00:00',
        );

        Http::assertSent(function ($request) {
            return str_contains($request->url(), "/forms('FORM123')/responses")
                && ! str_contains($request->url(), '/light')
                && $request['startDate'] === '2026-08-25T00:00:00+00:00'
                && $request['submitDate'] === '2026-08-25T00:00:00+00:00'
                && $request['answers'] === json_encode([['questionId' => 'r1', 'answer1' => 'Budi']]);
        });
    }

    public function test_submit_answers_throws_request_exception_on_failure(): void
    {
        Http::fake([
            'https://forms.cloud.microsoft/formapi/api/*/users/*/forms(*)/responses' => Http::response('', 500),
        ]);

        $target = new ResolvedFormTarget(
            'https://forms.cloud.microsoft/formapi/api/11111111-1111-1111-1111-111111111111/users/22222222-2222-2222-2222-222222222222/light',
            'FORM123',
        );

        $this->expectException(MsFormsRequestException::class);

        $this->client->submitAnswers($target, [['questionId' => 'r1', 'answer1' => 'Budi']], 'x');
    }

    public function test_submit_answers_propagates_microsoft_error_message(): void
    {
        Http::fake([
            'https://forms.cloud.microsoft/formapi/api/*/users/*/forms(*)/responses' => Http::response(
                '{"error":{"message":"The form is no longer accepting responses"}}',
                400
            ),
        ]);

        $target = new ResolvedFormTarget(
            'https://forms.cloud.microsoft/formapi/api/11111111-1111-1111-1111-111111111111/users/22222222-2222-2222-2222-222222222222/light',
            'FORM123',
        );

        $this->expectException(MsFormsRequestException::class);
        $this->expectExceptionMessage('The form is no longer accepting responses');

        $this->client->submitAnswers($target, [['questionId' => 'r1', 'answer1' => 'Budi']], 'x');
    }
}
