<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'commission_payment_method')) {
                $table->string('commission_payment_method')->nullable();
            }
            if (!Schema::hasColumn('orders', 'commission_transaction_id')) {
                $table->string('commission_transaction_id')->nullable();
            }
            if (!Schema::hasColumn('orders', 'commission_paid_at')) {
                $table->timestamp('commission_paid_at')->nullable();
            }
            // This one is different because we are changing an existing column
            $table->string('commission_status', 50)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'commission_payment_method',
                'commission_transaction_id',
                'commission_paid_at'
            ]);
        });
    }
};