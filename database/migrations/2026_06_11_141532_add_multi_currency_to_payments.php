<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add currency fields to payments table
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'currency')) {
                $table->string('currency', 3)->default('TZS')->after('amount');
            }
            if (!Schema::hasColumn('payments', 'exchange_rate')) {
                $table->decimal('exchange_rate', 10, 4)->nullable()->after('currency');
            }
            if (!Schema::hasColumn('payments', 'amount_usd')) {
                $table->decimal('amount_usd', 10, 2)->nullable()->after('exchange_rate');
            }
        });
        
        // Add currency fields to transactions table
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'currency')) {
                $table->string('currency', 3)->default('TZS')->after('amount');
            }
            if (!Schema::hasColumn('transactions', 'exchange_rate')) {
                $table->decimal('exchange_rate', 10, 4)->nullable()->after('currency');
            }
        });
        
        // Add currency fields to withdrawal_requests table
        Schema::table('withdrawal_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('withdrawal_requests', 'currency')) {
                $table->string('currency', 3)->default('TZS')->after('amount');
            }
        });
        
        // Add currency fields to commission_logs table
        Schema::table('commission_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('commission_logs', 'exchange_rate')) {
                $table->decimal('exchange_rate', 10, 4)->nullable()->after('platform_fee');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['currency', 'exchange_rate', 'amount_usd']);
        });
        
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['currency', 'exchange_rate']);
        });
        
        Schema::table('withdrawal_requests', function (Blueprint $table) {
            $table->dropColumn(['currency']);
        });
        
        Schema::table('commission_logs', function (Blueprint $table) {
            $table->dropColumn(['exchange_rate']);
        });
    }
};