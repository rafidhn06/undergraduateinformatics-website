<?php

namespace Tests\Unit\Support;

use App\Support\PostBodySanitizer;
use PHPUnit\Framework\TestCase;

final class PostBodySanitizerTest extends TestCase
{
    private PostBodySanitizer $sanitizer;

    protected function setUp(): void
    {
        $this->sanitizer = new PostBodySanitizer;
    }

    public function test_removes_scripts_and_event_handlers(): void
    {
        $result = $this->sanitizer->sanitize('<script>alert(1)</script><p onclick="alert(1)">teks</p>');

        $this->assertNotNull($result);
        $this->assertStringNotContainsString('<script', $result);
        $this->assertStringNotContainsString('onclick', $result);
        $this->assertStringContainsString('teks', $result);
    }

    public function test_strips_javascript_urls_and_event_images(): void
    {
        $result = $this->sanitizer->sanitize('<a href="javascript:alert(1)">klik</a><img src="x" onerror="alert(1)" alt="gambar">');

        $this->assertNotNull($result);
        $this->assertStringNotContainsString('javascript:', $result);
        $this->assertStringNotContainsString('onerror', $result);
    }

    public function test_keeps_article_structure(): void
    {
        $result = $this->sanitizer->sanitize('<h2>Judul</h2><p>Isi <strong>tebal</strong></p><table><tbody><tr><td>Sel</td></tr></tbody></table>');

        $this->assertStringContainsString('<h2>Judul</h2>', $result);
        $this->assertStringContainsString('<strong>tebal</strong>', $result);
        $this->assertStringContainsString('<table>', $result);
    }

    public function test_returns_null_for_null_or_empty_input(): void
    {
        $this->assertNull($this->sanitizer->sanitize(null));
        $this->assertNull($this->sanitizer->sanitize(''));
        $this->assertNull($this->sanitizer->sanitize('<script></script>'));
    }
}
