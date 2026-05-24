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
        Schema::create('time_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('name');       // "Sesi 1", "Sesi 2"
            $table->time('start_time');   // 09:00
            $table->time('end_time');     // 11:00
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_sessions');
    }
};
