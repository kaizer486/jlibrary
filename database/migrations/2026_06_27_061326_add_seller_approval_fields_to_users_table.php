<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Check if column exists before adding
            if (!Schema::hasColumn('users', 'author_approved_at')) {
                $table->timestamp('author_approved_at')->nullable()->after('role');
            }
            
            if (!Schema::hasColumn('users', 'bookseller_approved_at')) {
                $table->timestamp('bookseller_approved_at')->nullable()->after('author_approved_at');
            }
            
            if (!Schema::hasColumn('users', 'author_approved_by')) {
                $table->foreignId('author_approved_by')->nullable()->after('author_approved_at')->constrained('users');
            }
            
            if (!Schema::hasColumn('users', 'bookseller_approved_by')) {
                $table->foreignId('bookseller_approved_by')->nullable()->after('bookseller_approved_at')->constrained('users');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['author_approved_by']);
            $table->dropForeign(['bookseller_approved_by']);
            
            $table->dropColumn([
                'author_approved_at',
                'bookseller_approved_at',
                'author_approved_by',
                'bookseller_approved_by'
            ]);
        });
    }
};