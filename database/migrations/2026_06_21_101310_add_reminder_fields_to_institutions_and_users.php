<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add to institutions table
        Schema::table('institutions', function (Blueprint $table) {
            if (!Schema::hasColumn('institutions', 'reminder_30_sent_at')) {
                $table->timestamp('reminder_30_sent_at')->nullable();
                $table->timestamp('reminder_15_sent_at')->nullable();
                $table->timestamp('reminder_7_sent_at')->nullable();
                $table->timestamp('reminder_3_sent_at')->nullable();
                $table->timestamp('reminder_1_sent_at')->nullable();
                $table->timestamp('reminder_expired_sent_at')->nullable();
            }
        });

        // Add to users table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'reminder_30_sent_at')) {
                $table->timestamp('reminder_30_sent_at')->nullable();
                $table->timestamp('reminder_15_sent_at')->nullable();
                $table->timestamp('reminder_7_sent_at')->nullable();
                $table->timestamp('reminder_3_sent_at')->nullable();
                $table->timestamp('reminder_1_sent_at')->nullable();
                $table->timestamp('reminder_expired_sent_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->dropColumn([
                'reminder_30_sent_at',
                'reminder_15_sent_at',
                'reminder_7_sent_at',
                'reminder_3_sent_at',
                'reminder_1_sent_at',
                'reminder_expired_sent_at',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'reminder_30_sent_at',
                'reminder_15_sent_at',
                'reminder_7_sent_at',
                'reminder_3_sent_at',
                'reminder_1_sent_at',
                'reminder_expired_sent_at',
            ]);
        });
    }
};