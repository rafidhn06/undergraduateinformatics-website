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
        Schema::create('reservation_schedules', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('shift'); 
            $table->string('requested_by');
            $table->string('document_link')->nullable();
            $table->timestamps();

            $table->unique(['date', 'shift']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation_schedules');
    }
};
