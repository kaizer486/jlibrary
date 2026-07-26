<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'coins')) {
                $table->integer('coins')->default(0)->after('wallet_balance');
            }
            if (!Schema::hasColumn('users', 'referral_earnings')) {
                $table->integer('referral_earnings')->default(0)->after('coins');
            }
            if (!Schema::hasColumn('users', 'referred_by')) {
                $table->unsignedBigInteger('referred_by')->nullable()->after('referral_earnings');
                $table->foreign('referred_by')->references('id')->on('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('users', 'total_referrals')) {
                $table->integer('total_referrals')->default(0)->after('referred_by');
            }
            if (!Schema::hasColumn('users', 'completed_referrals')) {
                $table->integer('completed_referrals')->default(0)->after('total_referrals');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referred_by']);
            $table->dropColumn([
                'coins', 
                'referral_earnings', 
                'referred_by', 
                'total_referrals', 
                'completed_referrals'
            ]);
        });
    }
};