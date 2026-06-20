<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Check if table already exists
        if (!Schema::hasTable('commission_logs')) {
            Schema::create('commission_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
                $table->morphs('saleable');
                $table->decimal('total_amount', 10, 2);
                $table->decimal('author_earnings', 10, 2);
                $table->decimal('platform_fee', 10, 2);
                $table->string('currency', 3)->default('TZS');
                $table->decimal('exchange_rate', 10, 4)->nullable();
                $table->enum('status', [
                    'pending', 'processing', 'completed', 'withdrawn', 'failed'
                ])->default('pending');
                $table->timestamp('payout_date')->nullable();
                $table->string('payout_method')->nullable();
                $table->string('payout_reference')->nullable();
                $table->text('notes')->nullable();
                $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                
                // Indexes - CHECK IF THEY EXIST BEFORE ADDING
                $table->index(['author_id', 'status']);
                $table->index(['author_id', 'created_at']);
                $table->index('status');
                $table->index('payout_date');
            });
        } else {
            // Table exists, add missing indexes safely
            Schema::table('commission_logs', function (Blueprint $table) {
                // Check each index before adding
                $indexes = DB::select("SHOW INDEX FROM commission_logs WHERE Key_name = 'commission_logs_author_id_status_index'");
                if (empty($indexes)) {
                    $table->index(['author_id', 'status']);
                }
                
                $indexes = DB::select("SHOW INDEX FROM commission_logs WHERE Key_name = 'commission_logs_author_id_created_at_index'");
                if (empty($indexes)) {
                    $table->index(['author_id', 'created_at']);
                }
                
                $indexes = DB::select("SHOW INDEX FROM commission_logs WHERE Key_name = 'commission_logs_status_index'");
                if (empty($indexes)) {
                    $table->index('status');
                }
                
                $indexes = DB::select("SHOW INDEX FROM commission_logs WHERE Key_name = 'commission_logs_payout_date_index'");
                if (empty($indexes)) {
                    $table->index('payout_date');
                }
            });
        }
        
        // Add commission tracking to marketplace_listings if table exists
        if (Schema::hasTable('marketplace_listings')) {
            Schema::table('marketplace_listings', function (Blueprint $table) {
                if (!Schema::hasColumn('marketplace_listings', 'commission_paid')) {
                    $table->boolean('commission_paid')->default(false)->after('status');
                }
                if (!Schema::hasColumn('marketplace_listings', 'commission_log_id')) {
                    $table->foreignId('commission_log_id')->nullable()->after('commission_paid')
                        ->constrained('commission_logs')->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        // Remove foreign key from marketplace_listings first
        if (Schema::hasTable('marketplace_listings') && Schema::hasColumn('marketplace_listings', 'commission_log_id')) {
            Schema::table('marketplace_listings', function (Blueprint $table) {
                $table->dropForeign(['commission_log_id']);
                $table->dropColumn(['commission_paid', 'commission_log_id']);
            });
        }
        
        Schema::dropIfExists('commission_logs');
    }
};