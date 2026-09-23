<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'password_recovery_id')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('password_recovery_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'password_recovery_id')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('password_recovery_id')->nullable();
        });
    }
};
