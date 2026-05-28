<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create institutions table
        Schema::create('institutions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->default('Tanzania');
            $table->string('logo')->nullable();
            $table->string('website')->nullable();
            
            // Institution type: school, college, university, library, bookstore, publisher, research_center
            $table->enum('type', ['school', 'college', 'university', 'library', 'bookstore', 'publisher', 'research_center', 'other'])->default('school');
            
            // Status: pending, approved, suspended, inactive
            $table->enum('status', ['pending', 'approved', 'suspended', 'inactive'])->default('pending');
            
            // Subscription tier: basic, premium, enterprise
            $table->enum('subscription_tier', ['basic', 'premium', 'enterprise'])->default('basic');
            
            // Subscription expiry
            $table->timestamp('subscription_expires_at')->nullable();
            
            // Max users/students allowed for this institution
            $table->integer('max_users')->default(100);
            $table->integer('max_books')->default(1000);
            
            // Metadata
            $table->json('settings')->nullable();
            $table->json('metadata')->nullable();
            
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            
            $table->timestamps();
        });
        
        // Add institution_id to users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('institution_id')->nullable()->after('role')->constrained('institutions')->nullOnDelete();
            $table->string('position')->nullable()->after('institution_id'); // e.g., Head Librarian, Teacher, etc.
            $table->boolean('is_institution_admin')->default(false)->after('position');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['institution_id']);
            $table->dropColumn(['institution_id', 'position', 'is_institution_admin']);
        });
        
        Schema::dropIfExists('institutions');
    }
};