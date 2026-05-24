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
        Schema::create('exam_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Contoh: "Ujian Sekolah 2025"
            $table->date('start_date');
            $table->date('end_date'); // Ditambahkan di V2
            $table->boolean('is_active')->default(true);
            $table->enum('allocation_status', ['Pending', 'Processing', 'Completed', 'Failed'])
                  ->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_sessions');
    }
};
