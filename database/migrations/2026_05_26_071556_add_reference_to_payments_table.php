<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Check if column doesn't exist before adding
            if (!Schema::hasColumn('payments', 'reference')) {
                $table->string('reference')->nullable()->after('status');
            }
            if (!Schema::hasColumn('payments', 'method')) {
                $table->string('method')->nullable()->after('reference');
            }
            if (!Schema::hasColumn('payments', 'transaction_id')) {
                $table->string('transaction_id')->nullable()->after('method');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['reference', 'method', 'transaction_id']);
        });
    }
};