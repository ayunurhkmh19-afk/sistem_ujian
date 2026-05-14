<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_create_exam_allocations_table.php
public function up(): void
{
    Schema::create('exam_allocations', function (Blueprint $table) {
        $table->id();
        $table->foreignId('exam_session_id')->constrained()->onDelete('cascade');
        $table->foreignId('room_id')->constrained()->onDelete('cascade');
        $table->foreignId('student_id')->constrained()->onDelete('cascade');
        
        // Nomor meja diurutkan otomatis nanti [cite: 30]
        $table->integer('desk_number'); 
        
        $table->timestamps();

        // CONSTRAINT PENTING:
        // Memastikan satu siswa hanya bisa masuk 1 ruangan di sesi ujian yang sama.
        // Ini mencegah siswa "dipanggil lagi" ke ruangan lain.
        $table->unique(['exam_session_id', 'student_id'], 'one_student_per_session');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_allocations');
    }
};
