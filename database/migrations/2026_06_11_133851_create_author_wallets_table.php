<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('author_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('balance', 12, 2)->default(0);
            $table->decimal('total_earned', 12, 2)->default(0);
            $table->decimal('total_withdrawn', 12, 2)->default(0);
            $table->decimal('pending_withdrawal', 12, 2)->default(0);
            $table->string('currency', 3)->default('TZS');
            
            // Stripe Connect for international authors
            $table->string('stripe_account_id')->nullable();
            $table->boolean('stripe_onboarded')->default(false);
            
            // Withdrawal preferences
            $table->string('preferred_payout_method')->nullable(); // 'mpesa', 'bank', 'stripe'
            $table->string('payout_phone')->nullable(); // For mobile money
            $table->string('payout_bank_account')->nullable(); // For bank transfer
            
            $table->timestamps();
            
            $table->index(['user_id', 'balance']);
            $table->index('stripe_account_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('author_wallets');
    }
};