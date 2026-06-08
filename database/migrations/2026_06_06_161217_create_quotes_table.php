<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->text('quote_text');
            $table->string('author')->nullable();
            $table->string('category')->default('motivation');
            $table->string('status')->default('active'); // active, inactive, draft
            $table->integer('views_count')->default(0);
            $table->integer('saves_count')->default(0);
            $table->integer('shares_count')->default(0);
            $table->date('scheduled_date')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('user_favorite_quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('quote_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['user_id', 'quote_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_favorite_quotes');
        Schema::dropIfExists('quotes');
    }
};