<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_session_id')->constrained('exam_sessions')->onDelete('cascade');
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->enum('status', ['Hadir', 'Sakit', 'Izin', 'Alpa'])->default('Alpa');
            $table->foreignId('recorded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // Constraint: Satu siswa per ruangan per sesi
            $table->unique(['exam_session_id', 'room_id', 'student_id'], 'attendance_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_attendances');
    }
};
