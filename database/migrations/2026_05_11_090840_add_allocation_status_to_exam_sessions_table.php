<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Allocation status column is now created in the main exam_sessions migration
    }

    public function down(): void
    {
        // Do nothing
    }
};
