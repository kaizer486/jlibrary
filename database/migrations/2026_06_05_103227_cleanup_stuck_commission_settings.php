<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Force delete the stuck migration record
        DB::table('migrations')
            ->where('migration', '2026_05_30_134120_create_commission_settings_table')
            ->delete();
        
        // Drop the table if it exists
        Schema::dropIfExists('commission_settings');
    }

    public function down(): void
    {
        // Nothing to do here
    }
};