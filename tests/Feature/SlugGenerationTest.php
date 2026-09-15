<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SlugGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_gets_slug_from_title_on_create(): void
    {
        $post = Post::create(['title' => 'Pendaftaran Beasiswa 2026', 'subtitle' => 'Sub', 'body' => 'Body']);

        $this->assertSame('pendaftaran-beasiswa-2026', $post->slug);
    }

    public function test_duplicate_post_titles_get_unique_suffix(): void
    {
        $first = Post::create(['title' => 'Pengumuman Baru', 'subtitle' => 'Sub', 'body' => 'Body']);
        $second = Post::create(['title' => 'Pengumuman Baru', 'subtitle' => 'Sub', 'body' => 'Body']);

        $this->assertSame('pengumuman-baru', $first->slug);
        $this->assertSame('pengumuman-baru-2', $second->slug);
    }

    public function test_editing_post_keeps_slug(): void
    {
        $post = Post::create(['title' => 'Pengumuman Lama', 'subtitle' => 'Sub', 'body' => 'Body']);

        $post->update(['title' => 'Pengumuman Baru Lagi']);

        $this->assertSame('pengumuman-lama', $post->fresh()->slug);
    }

    public function test_empty_post_title_falls_back_to_post(): void
    {
        $post = Post::create(['title' => '!!!', 'subtitle' => 'Sub', 'body' => 'Body']);

        $this->assertSame('post', $post->slug);
    }

    public function test_tag_gets_slug_from_name_on_create(): void
    {
        $tag = Tag::create(['name' => 'Beasiswa']);

        $this->assertSame('beasiswa', $tag->slug);
    }

    public function test_duplicate_tag_names_get_unique_suffix(): void
    {
        $first = Tag::create(['name' => 'Beasiswa']);
        $second = Tag::create(['name' => 'Beasiswa']);

        $this->assertSame('beasiswa', $first->slug);
        $this->assertSame('beasiswa-2', $second->slug);
    }

    public function test_renaming_tag_keeps_slug(): void
    {
        $tag = Tag::create(['name' => 'Beasiswa']);

        $tag->update(['name' => 'Beasiswa Dalam Negeri']);

        $this->assertSame('beasiswa', $tag->fresh()->slug);
    }

    public function test_empty_tag_name_falls_back_to_tag(): void
    {
        $tag = Tag::create(['name' => '!!!']);

        $this->assertSame('tag', $tag->slug);
    }
}
