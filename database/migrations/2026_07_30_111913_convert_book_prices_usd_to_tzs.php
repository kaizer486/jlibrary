<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected float $rate = 2647.50;

    public function up(): void
    {
        DB::table('books')
            ->where('is_paid', true)
            ->where('price', '>', 0)
            ->whereNull('deleted_at')
            ->orderBy('id')
            ->chunkById(100, function ($books) {
                foreach ($books as $book) {
                    $converted = round($book->price * $this->rate, 2);
                    if ($converted > 99999999.99) {
                        \Log::warning('Skipped price conversion — would overflow decimal(10,2)', [
                            'book_id' => $book->id, 'original_price' => $book->price, 'converted' => $converted,
                        ]);
                        continue;
                    }
                    DB::table('books')->where('id', $book->id)->update(['price' => $converted]);
                }
            });

        DB::table('books')
            ->where(function ($q) {
                $q->where('softcopy_price', '>', 0)->orWhere('hardcopy_price', '>', 0);
            })
            ->whereNull('deleted_at')
            ->orderBy('id')
            ->chunkById(100, function ($books) {
                foreach ($books as $book) {
                    $update = [];
                    if ($book->softcopy_price > 0) {
                        $update['softcopy_price'] = round($book->softcopy_price * $this->rate, 2);
                    }
                    if ($book->hardcopy_price > 0) {
                        $update['hardcopy_price'] = round($book->hardcopy_price * $this->rate, 2);
                    }
                    if ($update) {
                        DB::table('books')->where('id', $book->id)->update($update);
                    }
                }
            });
    }

    public function down(): void
    {
        DB::table('books')
            ->where('is_paid', true)
            ->where('price', '>', 0)
            ->whereNull('deleted_at')
            ->orderBy('id')
            ->chunkById(100, function ($books) {
                foreach ($books as $book) {
                    DB::table('books')
                        ->where('id', $book->id)
                        ->update(['price' => round($book->price / $this->rate, 2)]);
                }
            });

        DB::table('books')
            ->where(function ($q) {
                $q->where('softcopy_price', '>', 0)->orWhere('hardcopy_price', '>', 0);
            })
            ->whereNull('deleted_at')
            ->orderBy('id')
            ->chunkById(100, function ($books) {
                foreach ($books as $book) {
                    $update = [];
                    if ($book->softcopy_price > 0) {
                        $update['softcopy_price'] = round($book->softcopy_price / $this->rate, 2);
                    }
                    if ($book->hardcopy_price > 0) {
                        $update['hardcopy_price'] = round($book->hardcopy_price / $this->rate, 2);
                    }
                    if ($update) {
                        DB::table('books')->where('id', $book->id)->update($update);
                    }
                }
            });
    }
};