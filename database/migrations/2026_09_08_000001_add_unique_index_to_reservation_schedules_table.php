<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $names = collect(Schema::getIndexes('reservation_schedules'))->pluck('name');

        if ($names->contains('reservation_schedules_date_shift_unique')) {
            Schema::table('reservation_schedules', function (Blueprint $table) {
                $table->dropUnique('reservation_schedules_date_shift_unique');
            });
        }

        Schema::table('reservation_schedules', function (Blueprint $table) {
            $table->unique(['date', 'shift']);
        });
    }

    public function down(): void
    {
        Schema::table('reservation_schedules', function (Blueprint $table) {
            $table->dropUnique(['date', 'shift']);
        });
    }
};
