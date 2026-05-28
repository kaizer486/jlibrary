<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('mpesa_phone')->nullable()->after('wallet_balance');
            $table->string('tigopesa_phone')->nullable()->after('mpesa_phone');
            $table->string('halopesa_phone')->nullable()->after('tigopesa_phone');
            $table->string('bank_name')->nullable()->after('halopesa_phone');
            $table->string('bank_account_number')->nullable()->after('bank_name');
            $table->string('bank_account_name')->nullable()->after('bank_account_number');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'mpesa_phone',
                'tigopesa_phone', 
                'halopesa_phone',
                'bank_name',
                'bank_account_number',
                'bank_account_name',
            ]);
        });
    }
};