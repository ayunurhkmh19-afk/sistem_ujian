<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_schedules', function (Blueprint $table) {
            $table->id();
            // Terhubung ke Sesi Ujian (misal: UAS 2025)
            $table->foreignId('exam_session_id')->constrained()->onDelete('cascade');
            
            $table->string('subject_name'); // Nama Mata Pelajaran
            $table->date('exam_date');      // Tanggal Ujian
            $table->time('start_time');     // Jam Mulai
            $table->time('end_time');       // Jam Selesai
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_schedules');
    }
};