<?php

namespace App\Services\MsForms;

use GuzzleHttp\Exception\TransferException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class MsFormsClient
{
    private const ALLOWED_HOST_SUFFIXES = ['office.com', 'microsoft.com', 'cloud.microsoft'];

    public function resolve(string $link): ResolvedFormTarget
    {
        return $this->resolveUncached($link);
    }

    private function resolveUncached(string $link): ResolvedFormTarget
    {
        if (
            filter_var($link, FILTER_VALIDATE_URL) === false
            || ! in_array(strtolower((string) parse_url($link, PHP_URL_SCHEME)), ['http', 'https'], true)
        ) {
            throw new MsFormsRequestException('Unable to load the form page');
        }

        $pageUrl = $link;
        $html = null;

        for ($hop = 0; $hop < 5; $hop++) {
            $this->assertMicrosoftHost($pageUrl);

            $response = $this->send(
                fn () => Http::withoutRedirecting()->timeout(10)->get($pageUrl),
                'Unable to load the form page'
            );

            if ($response->failed() && ! $response->redirect()) {
                throw new MsFormsRequestException('Unable to load the form page');
            }

            if ($response->redirect()) {
                $location = $response->header('Location');
                if (! $location) {
                    throw new MsFormsRequestException('Redirect without a target');
                }
                $pageUrl = $this->resolveLocation($pageUrl, $location);

                continue;
            }

            $html = $response->body();
            break;
        }

        if ($html === null) {
            throw new MsFormsRequestException('Too many redirects while resolving the form link');
        }

        return $this->parseTarget($html);
    }

    public function fetchFormDefinition(ResolvedFormTarget $target): array
    {
        $url = "{$target->apiBaseUrl}/runtimeForms('{$target->formId}')?\$expand=questions(\$expand=choices)";

        $response = $this->send(
            fn () => Http::timeout(10)->get($url),
            'Unable to load the form questions'
        );

        if ($response->failed()) {
            throw new MsFormsRequestException('Unable to load the form questions');
        }

        return $response->json() ?? [];
    }

    public function submitAnswers(
        ResolvedFormTarget $target,
        array $answers,
        string $submittedAt,
    ): void {
        $apiBaseUrl = preg_replace('~/light/?$~', '', $target->apiBaseUrl);

        $url = "{$apiBaseUrl}/forms('{$target->formId}')/responses";

        $response = $this->send(
            fn () => Http::timeout(10)->asJson()->post($url, [
                'startDate' => $submittedAt,
                'submitDate' => $submittedAt,
                'answers' => json_encode($answers),
            ]),
            'Unable to submit answers to Microsoft Forms'
        );

        if ($response->failed()) {
            $detail = $response->json('error.message') ?? $response->json('message');
            $message = is_string($detail) && $detail !== ''
                ? $detail
                : ($response->body() !== '' ? $response->body() : 'Unable to submit answers to Microsoft Forms');

            throw new MsFormsRequestException($message);
        }
    }

    private function send(callable $request, string $failureMessage): Response
    {
        try {
            return $request();
        } catch (ConnectionException|TransferException $e) {
            throw new MsFormsRequestException($failureMessage);
        }
    }

    private function assertMicrosoftHost(string $url): void
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        foreach (self::ALLOWED_HOST_SUFFIXES as $suffix) {
            if ($host === $suffix || str_ends_with($host, '.'.$suffix)) {
                return;
            }
        }

        throw new MsFormsRequestException('Invalid form link');
    }

    private function resolveLocation(string $currentUrl, string $location): string
    {
        if (str_starts_with($location, 'http')) {
            return $location;
        }

        return rtrim(parse_url($currentUrl, PHP_URL_SCHEME).'://'.parse_url($currentUrl, PHP_URL_HOST), '/').'/'.ltrim($location, '/');
    }

    private function parseTarget(string $html): ResolvedFormTarget
    {
        preg_match('/"prefetchFormUrl"\s*:\s*"(https:[^"]+)"/', $html, $matches);

        if (! isset($matches[1])) {
            throw new MsFormsParseException('Form definition URL not found in the page');
        }

        $prefetchUrl = str_replace('\\u0027', "'", $matches[1]);

        preg_match(
            "#^https://([^/]+)/formapi/api/([^/]+)/users/([^/]+)/light/runtimeForms\\('([^']+)'\\)#",
            $prefetchUrl,
            $urlMatches
        );

        if (count($urlMatches) < 5) {
            throw new MsFormsParseException('Form API URL has an unexpected shape');
        }

        return new ResolvedFormTarget(
            "https://{$urlMatches[1]}/formapi/api/{$urlMatches[2]}/users/{$urlMatches[3]}/light",
            $urlMatches[4],
        );
    }
}
