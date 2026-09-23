<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('posts')
            ->where('image', 'images/DummyImage.png')
            ->update(['image' => null]);
    }

    public function down(): void
    {
    }
};