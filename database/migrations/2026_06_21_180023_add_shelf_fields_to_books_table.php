<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // Check if columns exist before adding
            if (!Schema::hasColumn('books', 'shelf_number')) {
                $table->string('shelf_number')->nullable()->after('status');
            }
            
            if (!Schema::hasColumn('books', 'shelf_name')) {
                $table->string('shelf_name')->nullable()->after('shelf_number');
            }
            
            if (!Schema::hasColumn('books', 'column_location')) {
                $table->string('column_location')->nullable()->after('shelf_name');
            }
            
            if (!Schema::hasColumn('books', 'position')) {
                $table->string('position')->nullable()->after('column_location');
            }
            
            if (!Schema::hasColumn('books', 'floor')) {
                $table->string('floor')->nullable()->after('position');
            }
            
            if (!Schema::hasColumn('books', 'section')) {
                $table->string('section')->nullable()->after('floor');
            }
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn([
                'shelf_number',
                'shelf_name',
                'column_location',
                'position',
                'floor',
                'section',
            ]);
        });
    }
};