<?php

namespace Tests\Feature\Web;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTagControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): void
    {
        $this->actingAs(User::create([
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
        ]));
    }

    public function test_admin_can_store_a_tag(): void
    {
        $this->actingAsAdmin();

        $response = $this->post('/admin/tags', ['name' => 'Beasiswa', 'description' => 'Info beasiswa']);

        $response->assertRedirect(route('admin.tags.index'));
        $this->assertDatabaseHas('tags', ['name' => 'Beasiswa']);
    }

    public function test_admin_store_validates_tag_name(): void
    {
        $this->actingAsAdmin();

        $response = $this->post('/admin/tags', ['name' => '', 'description' => 'Info']);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('tags', 0);
    }

    public function test_error_toast_has_a_close_button(): void
    {
        $this->actingAsAdmin();

        $this->post('/admin/tags', ['name' => '', 'description' => 'Info']);

        $this->get('/admin/tags/create')->assertSee('admin-toast__close', false);
    }

    public function test_admin_can_update_a_tag(): void
    {
        $this->actingAsAdmin();
        $tag = Tag::create(['name' => 'Beasiswa', 'description' => 'Info']);

        $response = $this->put("/admin/tags/{$tag->id}", ['name' => 'Beasiswa Baru', 'description' => 'Info']);

        $response->assertRedirect(route('admin.tags.index'));
        $this->assertDatabaseHas('tags', ['name' => 'Beasiswa Baru']);
    }

    public function test_admin_update_validates_tag_name(): void
    {
        $this->actingAsAdmin();
        $tag = Tag::create(['name' => 'Beasiswa', 'description' => 'Info']);

        $response = $this->put("/admin/tags/{$tag->id}", ['name' => '', 'description' => 'Info']);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseHas('tags', ['name' => 'Beasiswa']);
    }
}
