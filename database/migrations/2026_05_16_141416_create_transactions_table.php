<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // 'credit', 'debit', 'withdrawal', 'refund', 'commission'
            $table->decimal('amount', 10, 2);
            $table->decimal('balance_after', 10, 2);
            $table->string('description')->nullable();
            $table->string('reference')->nullable();
            $table->string('status')->default('completed'); // 'pending', 'completed', 'failed', 'processing'
            $table->string('method')->nullable(); // 'mpesa', 'card', 'bank', 'referral'
            $table->nullableMorphs('payable'); // For linking to Book, Quiz, etc.
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};