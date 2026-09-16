<?php

namespace Tests\Feature;

use App\Models\ImportantLink;
use App\Models\ImportantSection;
use Database\Seeders\ImportantLinkSeeder;
use Database\Seeders\ImportantSectionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportantLinkSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_assigns_varied_timestamps(): void
    {
        $this->seed(ImportantSectionSeeder::class);
        $this->seed(ImportantLinkSeeder::class);

        $this->assertSame(59, ImportantLink::count());
        $this->assertSame(59, ImportantLink::distinct()->count('updated_at'));
    }

    public function test_seeder_orders_latest_links_by_recency(): void
    {
        $this->seed(ImportantSectionSeeder::class);
        $this->seed(ImportantLinkSeeder::class);

        $latest = ImportantLink::orderByDesc('updated_at')->orderByDesc('id')->limit(5)->pluck('updated_at');

        $this->assertCount(5, $latest);
        $this->assertSame(5, $latest->unique()->count());
    }

    public function test_seeder_is_idempotent_for_timestamps(): void
    {
        $this->seed(ImportantSectionSeeder::class);
        $this->seed(ImportantLinkSeeder::class);

        $before = ImportantLink::pluck('updated_at', 'id')->map(fn ($date) => (string) $date)->all();

        $this->seed(ImportantLinkSeeder::class);

        $after = ImportantLink::pluck('updated_at', 'id')->map(fn ($date) => (string) $date)->all();

        $this->assertSame($before, $after);
    }

    public function test_seeder_spreads_legacy_batch_timestamps(): void
    {
        $this->seed(ImportantSectionSeeder::class);

        $section = ImportantSection::where('name', 'General')->firstOrFail();
        $timestamp = now()->startOfDay();

        foreach (['Video Profil Prodi S-1 Informatika', 'Link Tree LAAK FIF'] as $name) {
            ImportantLink::create([
                'important_section_id' => $section->id,
                'name' => $name,
                'link' => 'https://example.com',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }

        $this->seed(ImportantLinkSeeder::class);

        $timestamps = ImportantLink::where('important_section_id', $section->id)
            ->whereIn('name', ['Video Profil Prodi S-1 Informatika', 'Link Tree LAAK FIF'])
            ->pluck('updated_at')
            ->map(fn ($date) => (string) $date);

        $this->assertSame(2, $timestamps->unique()->count());
    }

    public function test_seeder_preserves_edited_links(): void
    {
        $this->seed(ImportantSectionSeeder::class);

        $section = ImportantSection::where('name', 'General')->firstOrFail();
        $link = ImportantLink::create([
            'important_section_id' => $section->id,
            'name' => 'Video Profil Prodi S-1 Informatika',
            'link' => 'https://example.com',
            'created_at' => now()->subDays(10),
            'updated_at' => now()->subDays(2),
        ]);

        $this->seed(ImportantLinkSeeder::class);

        $this->assertSame((string) $link->updated_at, (string) $link->fresh()->updated_at);
        $this->assertSame((string) $link->created_at, (string) $link->fresh()->created_at);
    }
}
