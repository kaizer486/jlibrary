<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->nullableMorphs('auditable');
            $table->string('action');
            $table->string('status_before')->nullable();
            $table->string('status_after')->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->text('reason')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['admin_id', 'created_at']);
            $table->index('action');
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('payment_audit_logs');
    }
};