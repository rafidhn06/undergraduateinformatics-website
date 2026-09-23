<?php

namespace Tests\Feature\Web;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RouteConventionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_routes_are_named(): void
    {
        foreach ([
            'api.posts.index',
            'api.posts.show',
            'api.tags.index',
            'api.tags.show',
            'api.link-sections.index',
            'api.important-links.index',
            'api.datasets.index',
            'api.feedback-form.show',
            'api.feedback-submissions.store',
            'api.reservation-form.show',
            'api.reservation-form.availability',
            'api.reservation-submissions.store',
        ] as $name) {
            $this->assertTrue(Route::has($name), "Missing route name: {$name}");
        }
    }

    public function test_legacy_search_url_redirects_to_posts_index(): void
    {
        $response = $this->get('/posts/search?q=beasiswa&per_page=5&page=2');

        $response->assertStatus(301);
        $response->assertRedirect('/posts?q=beasiswa&per_page=5&page=2');
    }

    public function test_admin_home_redirects_to_datasets(): void
    {
        $this->actingAs(User::create([
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
        ]));

        $this->get('/admin/')->assertRedirect(route('admin.datasets.index'));
    }
}
