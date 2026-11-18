<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add column if missing (no unique index yet — add it after backfill)
        if (!Schema::hasColumn('books', 'book_code')) {
            Schema::table('books', function (Blueprint $table) {
                $table->string('book_code', 30)
                    ->nullable()
                    ->after('id')
                    ->comment('Stable, human-readable book identifier (BK-YYYY-NNNN)');
            });
        }

        // 2. Backfill existing books
        $year = date('Y');
        $sequence = 1;

        $existingCodes = DB::table('books')
            ->whereNotNull('book_code')
            ->pluck('book_code')
            ->toArray();

        // Find max sequence already in use
        foreach ($existingCodes as $code) {
            if (preg_match('/^BK-\d{4}-(\d{4})$/', $code, $m)) {
                $sequence = max($sequence, (int) $m[1] + 1);
            }
        }

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

        // 3. Now add the unique index (safe because all rows have a code)
        Schema::table('books', function (Blueprint $table) {
            if (!$this->hasIndex('books', 'books_book_code_unique')) {
                $table->unique('book_code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if ($this->hasIndex('books', 'books_book_code_unique')) {
                $table->dropUnique(['book_code']);
            }
            if (Schema::hasColumn('books', 'book_code')) {
                $table->dropColumn('book_code');
            }
        });
    }

    private function hasIndex(string $table, string $indexName): bool
    {
        $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
        return count($indexes) > 0;
    }
};