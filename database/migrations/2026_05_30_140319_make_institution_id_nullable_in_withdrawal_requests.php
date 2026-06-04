<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('withdrawal_requests', function (Blueprint $table) {
            $table->foreignId('institution_id')->nullable()->change();
            $table->foreignId('user_id')->nullable()->after('institution_id');
        });
    }

    public function down(): void
    {
        Schema::table('withdrawal_requests', function (Blueprint $table) {
            $table->foreignId('institution_id')->nullable(false)->change();
            $table->dropColumn('user_id');
        });
    }
};