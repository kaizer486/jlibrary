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
        // ✅ Drop the global unique constraint on code
        Schema::table('shelves', function (Blueprint $table) {
            $table->dropUnique('shelves_code_unique');
        });

        // ✅ Add a composite unique constraint: code + institution_id
        Schema::table('shelves', function (Blueprint $table) {
            $table->unique(['code', 'institution_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shelves', function (Blueprint $table) {
            // Drop the composite unique
            $table->dropUnique('shelves_code_institution_id_unique');
            
            // Re-add the global unique
            $table->unique('code');
        });
    }
};