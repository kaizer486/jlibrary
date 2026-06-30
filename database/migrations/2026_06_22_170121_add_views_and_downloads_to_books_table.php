<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if (!Schema::hasColumn('books', 'views_count')) {
                $table->integer('views_count')->default(0)->after('status');
            }
            if (!Schema::hasColumn('books', 'downloads')) {
                $table->integer('downloads')->default(0)->after('views_count');
            }
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if (Schema::hasColumn('books', 'views_count')) {
                $table->dropColumn('views_count');
            }
            if (Schema::hasColumn('books', 'downloads')) {
                $table->dropColumn('downloads');
            }
        });
    }
};