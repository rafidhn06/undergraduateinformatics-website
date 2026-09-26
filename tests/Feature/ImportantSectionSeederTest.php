<?php

namespace Tests\Feature;

use App\Models\ImportantSection;
use Database\Seeders\ImportantSectionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportantSectionSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_preserves_existing_section_order(): void
    {
        ImportantSection::create(['name' => 'General', 'order_number' => 99]);

        $this->seed(ImportantSectionSeeder::class);

        $this->assertSame(99, ImportantSection::where('name', 'General')->first()->order_number);
    }
}
