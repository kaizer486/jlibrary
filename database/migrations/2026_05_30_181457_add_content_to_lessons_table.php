<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check if the column doesn't exist before adding it
        if (!Schema::hasColumn('lessons', 'content')) {
            Schema::table('lessons', function (Blueprint $table) {
                $table->longText('content')->nullable()->after('title');
            });
        }
    }

    public function down(): void
    {
        // Check if the column exists before dropping it
        if (Schema::hasColumn('lessons', 'content')) {
            Schema::table('lessons', function (Blueprint $table) {
                $table->dropColumn('content');
            });
        }
    }
};