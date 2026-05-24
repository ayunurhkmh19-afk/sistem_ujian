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
        Schema::create('exam_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_schedule_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            
            // Nomor meja diurutkan otomatis nanti
            $table->integer('desk_number'); 
            
            $table->timestamps();

            // CONSTRAINT PENTING:
            // Memastikan satu siswa hanya bisa masuk 1 ruangan di jadwal ujian yang sama.
            $table->unique(['exam_schedule_id', 'student_id'], 'one_student_per_schedule');
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
