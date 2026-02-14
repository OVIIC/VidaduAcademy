<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Safely update only rows that contain the invalid placeholder URL
        DB::table('courses')
            ->where('thumbnail', 'LIKE', '%api/placeholder%')
            ->update(['thumbnail' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot revert data changes easily, but this is cleaning up garbage data so it's fine.
    }
};
