<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds the fields needed so a single `orders` table can serve BOTH:
     *   - institution-owned books  (institution_id set, author_id null)
     *   - author-owned books        (author_id set,       institution_id null)
     *
     * Also records the book format being ordered (softcopy / hardcopy / both)
     * and the per-unit price at time of order, plus a customer phone for
     * hardcopy deliveries.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Who owns the book this order is for (author side).
            // institution_id already exists on the table for institution-owned books.
            if (!Schema::hasColumn('orders', 'author_id')) {
                $table->foreignId('author_id')
                    ->nullable()
                    ->after('institution_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            // Format of the book being ordered.
            if (!Schema::hasColumn('orders', 'book_type')) {
                $table->string('book_type', 20)
                    ->nullable()
                    ->after('book_id')
                    ->comment('softcopy | hardcopy | both');
            }

            // Price per unit captured at order time (so later price changes
            // don't rewrite history).
            if (!Schema::hasColumn('orders', 'price_per_unit')) {
                $table->decimal('price_per_unit', 12, 2)
                    ->nullable()
                    ->after('quantity');
            }

            // Customer phone for hardcopy delivery.
            if (!Schema::hasColumn('orders', 'customer_phone')) {
                $table->string('customer_phone', 30)
                    ->nullable()
                    ->after('shipping_address');
            }
        });

        // Composite indexes to speed up the dashboard queries.
        Schema::table('orders', function (Blueprint $table) {
            // Avoid duplicate index errors if the migration is re-run.
            try {
                $table->index(['author_id', 'status'], 'orders_author_status_index');
            } catch (\Throwable $e) {
                // index already exists — safe to ignore
            }

            try {
                $table->index(['institution_id', 'status'], 'orders_institution_status_index');
            } catch (\Throwable $e) {
                // index already exists — safe to ignore
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop indexes first (guarded).
            try {
                $table->dropIndex('orders_author_status_index');
            } catch (\Throwable $e) {
                // not present
            }

            try {
                $table->dropIndex('orders_institution_status_index');
            } catch (\Throwable $e) {
                // not present
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            // Drop foreign key + column safely.
            if (Schema::hasColumn('orders', 'author_id')) {
                try {
                    $table->dropForeign(['author_id']);
                } catch (\Throwable $e) {
                    // FK not present
                }
                $table->dropColumn('author_id');
            }

            if (Schema::hasColumn('orders', 'book_type')) {
                $table->dropColumn('book_type');
            }

            if (Schema::hasColumn('orders', 'price_per_unit')) {
                $table->dropColumn('price_per_unit');
            }

            if (Schema::hasColumn('orders', 'customer_phone')) {
                $table->dropColumn('customer_phone');
            }
        });
    }
};