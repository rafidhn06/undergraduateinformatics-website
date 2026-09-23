<?php

namespace Tests\Feature\Web;

use App\Models\ImportantSection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSectionOrderTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): void
    {
        $this->actingAs(User::create([
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
        ]));
    }

    public function test_admin_can_reorder_sections(): void
    {
        $this->actingAsAdmin();
        $first = ImportantSection::create(['name' => 'A', 'order_number' => 1]);
        $second = ImportantSection::create(['name' => 'B', 'order_number' => 2]);

        $response = $this->put('/admin/sections/reorder', [
            'order' => [$first->id => 2, $second->id => 1],
        ]);

        $response->assertRedirect(route('admin.sections.index'));
        $this->assertSame(2, $first->refresh()->order_number);
        $this->assertSame(1, $second->refresh()->order_number);
    }

    public function test_admin_reorder_rejects_duplicate_orders(): void
    {
        $this->actingAsAdmin();
        $first = ImportantSection::create(['name' => 'A', 'order_number' => 1]);
        $second = ImportantSection::create(['name' => 'B', 'order_number' => 2]);

        $response = $this->put('/admin/sections/reorder', [
            'order' => [$first->id => 1, $second->id => 1],
        ]);

        $response->assertSessionHasErrors('order');
        $this->assertSame(1, $first->refresh()->order_number);
        $this->assertSame(2, $second->refresh()->order_number);
    }

    public function test_admin_reorder_rejects_empty_orders(): void
    {
        $this->actingAsAdmin();
        $first = ImportantSection::create(['name' => 'A', 'order_number' => 1]);

        $response = $this->put('/admin/sections/reorder', [
            'order' => [$first->id => ''],
        ]);

        $response->assertSessionHasErrors('order.' . $first->id);
        $this->assertSame(1, $first->refresh()->order_number);
    }

    public function test_admin_reorder_rejects_order_beyond_section_count(): void
    {
        $this->actingAsAdmin();
        $first = ImportantSection::create(['name' => 'A', 'order_number' => 1]);

        $response = $this->put('/admin/sections/reorder', [
            'order' => [$first->id => 5],
        ]);

        $response->assertSessionHasErrors('order');
        $this->assertSame(1, $first->refresh()->order_number);
    }
}
