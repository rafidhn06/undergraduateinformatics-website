<?php

namespace Tests\Feature;

use App\Models\DashboardDataset;
use App\Models\DashboardDatasetItem;
use App\Models\ImportantLink;
use App\Models\ImportantSection;
use App\Models\PasswordRecovery;
use App\Models\Post;
use App\Models\PostTag;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_is_idempotent(): void
    {
        $this->seed();
        $this->seed();

        $this->assertSame(1, User::count());
        $this->assertSame(1, PasswordRecovery::count());
        $this->assertSame(7, Tag::count());
        $this->assertSame(30, Post::count());
        $this->assertSame(65, PostTag::count());
        $this->assertSame(12, ImportantSection::count());
        $this->assertSame(59, ImportantLink::count());
        $this->assertSame(4, DashboardDataset::count());
        $this->assertSame(17, DashboardDatasetItem::count());
    }
}