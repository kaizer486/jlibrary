<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketplace_listings', function (Blueprint $table) {
            if (!Schema::hasColumn('marketplace_listings', 'category_id')) {
                $table->foreignId('category_id')
                    ->nullable()
                    ->after('seller_id')
                    ->constrained('marketplace_categories')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('marketplace_listings', 'stock')) {
                $table->integer('stock')
                    ->nullable()
                    ->after('price')
                    ->comment('Physical copies available, null for unlimited/digital');
            }
        });
    }

    public function down(): void
    {
        Schema::table('marketplace_listings', function (Blueprint $table) {
            if (Schema::hasColumn('marketplace_listings', 'category_id')) {
                try {
                    $table->dropForeign(['category_id']);
                } catch (\Throwable $e) {
                    // FK not present
                }
                $table->dropColumn('category_id');
            }

            if (Schema::hasColumn('marketplace_listings', 'stock')) {
                $table->dropColumn('stock');
            }
        });
    }
};