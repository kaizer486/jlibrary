<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketplace_listings', function (Blueprint $table) {
            if (!Schema::hasColumn('marketplace_listings', 'book_type')) {
                $table->string('book_type', 20)
                    ->nullable()
                    ->default('softcopy')
                    ->after('price')
                    ->comment('softcopy | hardcopy | both');
            }

            if (!Schema::hasColumn('marketplace_listings', 'softcopy_price')) {
                $table->decimal('softcopy_price', 12, 2)
                    ->nullable()
                    ->after('book_type');
            }

            if (!Schema::hasColumn('marketplace_listings', 'hardcopy_price')) {
                $table->decimal('hardcopy_price', 12, 2)
                    ->nullable()
                    ->after('softcopy_price');
            }

            if (!Schema::hasColumn('marketplace_listings', 'hardcopy_available')) {
                $table->boolean('hardcopy_available')
                    ->nullable()
                    ->default(false)
                    ->after('hardcopy_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('marketplace_listings', function (Blueprint $table) {
            foreach (['hardcopy_available', 'hardcopy_price', 'softcopy_price', 'book_type'] as $col) {
                if (Schema::hasColumn('marketplace_listings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};