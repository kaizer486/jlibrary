<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Books Table
        if (!Schema::hasColumn('books', 'institution_id')) {
            Schema::table('books', function (Blueprint $table) {
                $table->foreignId('institution_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('institutions')
                    ->nullOnDelete();
            });
        }

        // Quizzes Table
        if (Schema::hasTable('quizzes') && !Schema::hasColumn('quizzes', 'institution_id')) {
            Schema::table('quizzes', function (Blueprint $table) {
                $table->foreignId('institution_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('institutions')
                    ->nullOnDelete();
            });
        }

        // Certificates Table
        if (Schema::hasTable('certificates') && !Schema::hasColumn('certificates', 'institution_id')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->foreignId('institution_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('institutions')
                    ->nullOnDelete();
            });
        }

        // Marketplace Listings Table
        if (Schema::hasTable('marketplace_listings') && !Schema::hasColumn('marketplace_listings', 'institution_id')) {
            Schema::table('marketplace_listings', function (Blueprint $table) {
                $table->foreignId('institution_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('institutions')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        // Books Table
        if (Schema::hasColumn('books', 'institution_id')) {
            Schema::table('books', function (Blueprint $table) {
                $table->dropForeign(['institution_id']);
                $table->dropColumn('institution_id');
            });
        }

        // Quizzes Table
        if (Schema::hasTable('quizzes') && Schema::hasColumn('quizzes', 'institution_id')) {
            Schema::table('quizzes', function (Blueprint $table) {
                $table->dropForeign(['institution_id']);
                $table->dropColumn('institution_id');
            });
        }

        // Certificates Table
        if (Schema::hasTable('certificates') && Schema::hasColumn('certificates', 'institution_id')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->dropForeign(['institution_id']);
                $table->dropColumn('institution_id');
            });
        }

        // Marketplace Listings Table
        if (Schema::hasTable('marketplace_listings') && Schema::hasColumn('marketplace_listings', 'institution_id')) {
            Schema::table('marketplace_listings', function (Blueprint $table) {
                $table->dropForeign(['institution_id']);
                $table->dropColumn('institution_id');
            });
        }
    }
};