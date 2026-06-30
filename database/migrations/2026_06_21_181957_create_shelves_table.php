<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shelves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->onDelete('cascade');
            
            // Shelf Details
            $table->string('name'); // e.g., "Fiction A-01", "Science B-02"
            $table->string('code')->unique(); // e.g., "A-01", "B-02", "C-03"
            $table->string('category')->nullable(); // e.g., Fiction, Science, History
            $table->text('description')->nullable();
            
            // Location
            $table->string('floor')->nullable();
            $table->string('section')->nullable();
            $table->string('column')->nullable();
            $table->string('row')->nullable();
            $table->integer('capacity')->default(50);
            $table->integer('current_count')->default(0);
            
            // Status
            $table->enum('status', ['active', 'inactive', 'full'])->default('active');
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['institution_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shelves');
    }
};