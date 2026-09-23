<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostModelTest extends TestCase
{
    use RefreshDatabase;

    public static function imageProvider(): array
    {
        return [
            'null image' => [null, false],
            'real path' => ['images/posts/foo.jpg', true],
            'placeholder path' => ['images/placeholder.png', true],
        ];
    }

    public function test_filter_uses_given_search_without_http_request(): void
    {
        Post::create([
            'title' => 'Beasiswa Luar Negeri',
            'subtitle' => 'Sub',
            'body' => '<p>Body</p>',
        ]);
        Post::create([
            'title' => 'Kalender Akademik',
            'subtitle' => 'Sub',
            'body' => '<p>Body</p>',
        ]);

        $results = Post::filter(['search' => 'beasiswa'])->get();

        $this->assertCount(1, $results);
        $this->assertSame('Beasiswa Luar Negeri', $results->first()->title);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('imageProvider')]
    public function test_has_image(?string $image, bool $expected): void
    {
        $post = Post::create([
            'title' => 'Judul',
            'subtitle' => 'Sub',
            'body' => '<p>Body</p>',
            'image' => $image,
        ]);

        $this->assertSame($expected, $post->hasImage());
    }
}