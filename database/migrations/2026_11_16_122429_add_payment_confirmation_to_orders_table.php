<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'payment_status')) {
                $table->string('payment_status', 30)
                    ->nullable()
                    ->default('unpaid')
                    ->after('payment_method')
                    ->comment('unpaid | seller_marked_paid | buyer_confirmed | disputed');
            }

            if (!Schema::hasColumn('orders', 'payment_received_at')) {
                $table->timestamp('payment_received_at')
                    ->nullable()
                    ->after('payment_status')
                    ->comment('When seller marked payment as received');
            }

            if (!Schema::hasColumn('orders', 'payment_confirmed_by_buyer_at')) {
                $table->timestamp('payment_confirmed_by_buyer_at')
                    ->nullable()
                    ->after('payment_received_at')
                    ->comment('When buyer confirmed they paid');
            }

            if (!Schema::hasColumn('orders', 'payment_dispute_reason')) {
                $table->text('payment_dispute_reason')
                    ->nullable()
                    ->after('payment_confirmed_by_buyer_at')
                    ->comment('Buyer reason if they dispute the payment');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'payment_dispute_reason')) {
                $table->dropColumn('payment_dispute_reason');
            }
            if (Schema::hasColumn('orders', 'payment_confirmed_by_buyer_at')) {
                $table->dropColumn('payment_confirmed_by_buyer_at');
            }
            if (Schema::hasColumn('orders', 'payment_received_at')) {
                $table->dropColumn('payment_received_at');
            }
            if (Schema::hasColumn('orders', 'payment_status')) {
                $table->dropColumn('payment_status');
            }
        });
    }
};