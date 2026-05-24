<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Obsolesced in V2. Rooms is now the global rooms table.
    }

    public function down(): void
    {
        // Do nothing
    }
};
