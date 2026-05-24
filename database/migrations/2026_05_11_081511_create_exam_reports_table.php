<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_schedule_id')->constrained('exam_schedules')->onDelete('cascade');
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Pengawas yang melapor
            $table->integer('total_present')->default(0);
            $table->integer('total_absent')->default(0);
            $table->text('incident_notes')->nullable();
            $table->enum('status', ['Draft', 'Submitted'])->default('Draft');
            $table->timestamps();

            // Constraint: Satu berita acara per ruangan per jadwal
            $table->unique(['exam_schedule_id', 'room_id'], 'report_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_reports');
    }
};
