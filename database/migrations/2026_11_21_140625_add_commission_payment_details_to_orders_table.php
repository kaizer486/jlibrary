<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('commission_payment_method')->nullable()->after('commission_notes');
            $table->string('commission_transaction_id')->nullable()->after('commission_payment_method');
            $table->timestamp('commission_paid_at')->nullable()->after('commission_transaction_id');
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