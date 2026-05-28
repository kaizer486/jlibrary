<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['author', 'bookseller', 'publisher', 'researcher']);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('message')->nullable(); // User's application message
            
            // Business/Personal Details
            $table->string('business_name')->nullable();
            $table->string('business_address')->nullable();
            $table->string('tax_id')->nullable(); // TIN number
            $table->string('phone')->nullable();
            
            // Document paths
            $table->string('id_document')->nullable(); // National ID or Passport
            $table->string('certificate_document')->nullable(); // Education certificate
            $table->string('business_license')->nullable(); // Business license (for booksellers)
            $table->string('tax_certificate')->nullable(); // Tax certificate
            
            // Admin review
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('admin_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};