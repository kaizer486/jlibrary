<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Drop the existing unique constraint
        Schema::table('shelves', function (Blueprint $table) {
            $table->dropUnique('shelves_code_institution_id_unique');
        });

        // Step 2: Add a regular index (not unique) for performance
        Schema::table('shelves', function (Blueprint $table) {
            $table->index(['code', 'institution_id']);
        });

       
    }

    public function down(): void
    {
        Schema::table('shelves', function (Blueprint $table) {
            $table->dropIndex(['code', 'institution_id']);
            $table->unique(['code', 'institution_id']);
        });
    }
};