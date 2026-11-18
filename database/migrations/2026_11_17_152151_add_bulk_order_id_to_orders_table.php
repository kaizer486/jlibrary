<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if (!Schema::hasColumn('books', 'book_code')) {
                $table->string('book_code', 30)
                    ->nullable()
                    ->after('id')
                    ->comment('Stable, human-readable book identifier (BK-YYYY-NNNN)');

                $table->unique('book_code');
            }
        });

        // ── Backfill existing books ──
        $year = date('Y');
        $sequence = 1;

        DB::table('books')
            ->whereNull('book_code')
            ->orderBy('id')
            ->chunkById(100, function ($books) use (&$sequence, $year) {
                foreach ($books as $book) {
                    $code = 'BK-' . $year . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
                    DB::table('books')
                        ->where('id', $book->id)
                        ->update(['book_code' => $code]);
                    $sequence++;
                }
            });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if (Schema::hasColumn('books', 'book_code')) {
                $table->dropUnique(['book_code']);
                $table->dropColumn('book_code');
            }
        });
    }
};