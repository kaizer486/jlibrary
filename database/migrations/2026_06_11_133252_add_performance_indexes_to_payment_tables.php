<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ==========================================
        // PAYMENTS TABLE INDEXES
        // ==========================================
        Schema::table('payments', function (Blueprint $table) {
            // Check if index exists before adding
            if (!Schema::hasIndex('payments', 'payments_user_id_status_index')) {
                $table->index(['user_id', 'status']);
            }
            
            if (!Schema::hasIndex('payments', 'payments_status_created_at_index')) {
                $table->index(['status', 'created_at']);
            }
            
            // Skip polymorphic index if it already exists (using different name check)
            $indexExists = false;
            $indexes = \DB::select("SHOW INDEX FROM payments WHERE Key_name = 'payments_payable_type_payable_id_index'");
            if (empty($indexes)) {
                $table->index(['payable_type', 'payable_id']);
            }
            
            if (!Schema::hasIndex('payments', 'payments_transaction_id_index')) {
                $table->index('transaction_id');
            }
            
            if (!Schema::hasIndex('payments', 'payments_idempotency_key_index') && Schema::hasColumn('payments', 'idempotency_key')) {
                $table->index('idempotency_key');
            }
        });
        
        // ==========================================
        // TRANSACTIONS TABLE INDEXES
        // ==========================================
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasIndex('transactions', 'transactions_user_id_type_index')) {
                $table->index(['user_id', 'type']);
            }
            
            if (!Schema::hasIndex('transactions', 'transactions_user_id_created_at_index')) {
                $table->index(['user_id', 'created_at']);
            }
            
            if (!Schema::hasIndex('transactions', 'transactions_status_index')) {
                $table->index('status');
            }
            
            if (!Schema::hasIndex('transactions', 'transactions_reference_index')) {
                $table->index('reference');
            }
        });
        
        // ==========================================
        // WITHDRAWAL_REQUESTS TABLE INDEXES
        // ==========================================
        if (Schema::hasTable('withdrawal_requests')) {
            Schema::table('withdrawal_requests', function (Blueprint $table) {
                if (!Schema::hasIndex('withdrawal_requests', 'withdrawal_requests_status_index')) {
                    $table->index('status');
                }
                
                if (!Schema::hasIndex('withdrawal_requests', 'withdrawal_requests_institution_id_status_index')) {
                    $table->index(['institution_id', 'status']);
                }
                
                if (!Schema::hasIndex('withdrawal_requests', 'withdrawal_requests_user_id_status_index')) {
                    $table->index(['user_id', 'status']);
                }
                
                if (!Schema::hasIndex('withdrawal_requests', 'withdrawal_requests_created_at_index')) {
                    $table->index('created_at');
                }
            });
        }
        
        // ==========================================
        // USERS TABLE INDEXES
        // ==========================================
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasIndex('users', 'users_wallet_balance_index')) {
                $table->index('wallet_balance');
            }
        });
    }

    public function down(): void
    {
        // Payments
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['payable_type', 'payable_id']);
            $table->dropIndex(['transaction_id']);
            $table->dropIndex(['idempotency_key']);
        });
        
        // Transactions
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'type']);
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex(['status']);
            $table->dropIndex(['reference']);
        });
        
        // Withdrawal requests
        if (Schema::hasTable('withdrawal_requests')) {
            Schema::table('withdrawal_requests', function (Blueprint $table) {
                $table->dropIndex(['status']);
                $table->dropIndex(['institution_id', 'status']);
                $table->dropIndex(['user_id', 'status']);
                $table->dropIndex(['created_at']);
            });
        }
        
        // Users
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['wallet_balance']);
        });
    }
};