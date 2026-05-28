<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('xp_points')->default(0)->after('wallet_balance');
            $table->integer('level')->default(1)->after('xp_points');
            $table->integer('streak_days')->default(0)->after('level');
            $table->timestamp('last_active_at')->nullable()->after('streak_days');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['xp_points', 'level', 'streak_days', 'last_active_at']);
        });
    }
};