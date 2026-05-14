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
    Schema::create('rooms', function (Blueprint $table) {
        $table->id();
        // Terhubung ke sesi ujian tertentu
        $table->foreignId('exam_session_id')->constrained()->onDelete('cascade');
        $table->string('name'); // Misal: "Ruang 01"
        $table->integer('capacity'); // Untuk validasi range siswa
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
