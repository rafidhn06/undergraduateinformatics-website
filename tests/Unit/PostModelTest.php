<?php

namespace Tests\Unit;

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