<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add approval fields for authors and booksellers
            $table->timestamp('author_approved_at')->nullable()->after('role');
            $table->timestamp('bookseller_approved_at')->nullable()->after('author_approved_at');
            
            // Track who approved them
            $table->foreignId('author_approved_by')->nullable()->after('author_approved_at')->constrained('users');
            $table->foreignId('bookseller_approved_by')->nullable()->after('bookseller_approved_at')->constrained('users');
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