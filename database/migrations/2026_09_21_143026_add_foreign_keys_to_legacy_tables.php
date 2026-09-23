<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('post_tags')) {
            if (! $this->foreignKeyExists('post_tags', 'post_id')) {
                Schema::table('post_tags', function (Blueprint $table) {
                    $table->foreign('post_id')->references('id')->on('posts')->cascadeOnDelete();
                });
            }

            if (! $this->foreignKeyExists('post_tags', 'tag_id')) {
                Schema::table('post_tags', function (Blueprint $table) {
                    $table->foreign('tag_id')->references('id')->on('tags')->cascadeOnDelete();
                });
            }
        }

        if (Schema::hasTable('important_links') && ! $this->foreignKeyExists('important_links', 'important_section_id')) {
            Schema::table('important_links', function (Blueprint $table) {
                $table->foreign('important_section_id')->references('id')->on('important_sections')->cascadeOnDelete();
            });
        }

        if (Schema::hasTable('password_recoveries') && ! $this->foreignKeyExists('password_recoveries', 'user_id')) {
            Schema::table('password_recoveries', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('password_recoveries') && $this->foreignKeyExists('password_recoveries', 'user_id')) {
            Schema::table('password_recoveries', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
        }

        if (Schema::hasTable('important_links') && $this->foreignKeyExists('important_links', 'important_section_id')) {
            Schema::table('important_links', function (Blueprint $table) {
                $table->dropForeign(['important_section_id']);
            });
        }

        if (Schema::hasTable('post_tags')) {
            if ($this->foreignKeyExists('post_tags', 'post_id')) {
                Schema::table('post_tags', function (Blueprint $table) {
                    $table->dropForeign(['post_id']);
                });
            }

            if ($this->foreignKeyExists('post_tags', 'tag_id')) {
                Schema::table('post_tags', function (Blueprint $table) {
                    $table->dropForeign(['tag_id']);
                });
            }
        }
    }

    private function foreignKeyExists(string $table, string $column): bool
    {
        if (DB::getDriverName() === 'sqlite') {
            $quoted = str_replace('"', '""', $table);
            $keys = DB::select('PRAGMA foreign_key_list("' . $quoted . '")');

            foreach ($keys as $key) {
                if (($key->from ?? null) === $column) {
                    return true;
                }
            }

            return false;
        }

        return DB::selectOne(
            'SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL LIMIT 1',
            [$table, $column]
        ) !== null;
    }
};
