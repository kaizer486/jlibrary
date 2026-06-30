<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ==========================================
        // INSTITUTIONS TABLE
        // ==========================================
        Schema::table('institutions', function (Blueprint $table) {
            // Check if columns exist before adding
            if (!Schema::hasColumn('institutions', 'subscription_status')) {
                $table->enum('subscription_status', ['active', 'expired', 'pending', 'cancelled'])->default('pending')->after('subscription_expires_at');
            }
            
            if (!Schema::hasColumn('institutions', 'subscription_started_at')) {
                $table->timestamp('subscription_started_at')->nullable()->after('subscription_expires_at');
            }
            
            if (!Schema::hasColumn('institutions', 'subscription_price_paid')) {
                $table->decimal('subscription_price_paid', 10, 2)->nullable()->after('subscription_status');
            }
            
            if (!Schema::hasColumn('institutions', 'subscription_payment_method')) {
                $table->string('subscription_payment_method')->nullable()->after('subscription_price_paid');
            }
        });

        // ==========================================
        // USERS TABLE
        // ==========================================
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'subscription_tier')) {
                $table->string('subscription_tier')->nullable()->after('is_institution_admin');
            }
            
            if (!Schema::hasColumn('users', 'subscription_expires_at')) {
                $table->timestamp('subscription_expires_at')->nullable()->after('subscription_tier');
            }
        });
    }

    public function down(): void
    {
        // ==========================================
        // INSTITUTIONS TABLE
        // ==========================================
        Schema::table('institutions', function (Blueprint $table) {
            $table->dropColumn([
                'subscription_status',
                'subscription_started_at',
                'subscription_price_paid',
                'subscription_payment_method',
            ]);
        });

        // ==========================================
        // USERS TABLE
        // ==========================================
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'subscription_tier',
                'subscription_expires_at',
            ]);
        });
    }
};