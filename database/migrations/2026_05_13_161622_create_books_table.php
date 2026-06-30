<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            
            // Institution (Library)
            $table->foreignId('institution_id')->nullable()->constrained()->nullOnDelete();
            
            // Book Details
            $table->string('title');
            $table->string('author')->nullable();
            $table->text('description')->nullable();
            $table->string('isbn')->nullable()->unique();
            $table->year('publication_year')->nullable();
            $table->string('publisher')->nullable();
            $table->string('language')->default('English');
            $table->integer('total_pages')->default(0);
            
            // Media
            $table->string('cover_image')->nullable();
            $table->string('file_path')->nullable(); // PDF softcopy
            
            // Pricing
            $table->boolean('is_paid')->default(false);
            $table->decimal('price', 10, 2)->default(0);
            
            // Shelf Location
            $table->string('shelf_number')->nullable()->index(); // e.g., A-01, B-02
            $table->string('shelf_name')->nullable(); // e.g., Fiction, Science
            $table->string('column_location')->nullable(); // e.g., Column 3, Left Wing
            $table->string('position')->nullable(); // e.g., Row 2, Top Shelf
            $table->string('floor')->nullable(); // e.g., Ground Floor, 2nd Floor
            $table->string('section')->nullable(); // e.g., Fiction, Non-Fiction
            
            // Status
            $table->enum('status', ['approved', 'pending', 'rejected', 'available', 'borrowed', 'reserved'])->default('pending');
            $table->enum('availability', ['available', 'borrowed', 'reserved', 'under_maintenance'])->default('available');
            
            // Statistics
            $table->integer('views_count')->default(0);
            $table->integer('downloads')->default(0);
            $table->integer('copies_available')->default(1);
            $table->integer('total_copies')->default(1);
            
            // Uploader
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['institution_id', 'status']);
            $table->index(['shelf_number', 'shelf_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};