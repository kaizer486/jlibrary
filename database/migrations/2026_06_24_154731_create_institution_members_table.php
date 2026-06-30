<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institution_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('institution_id')->constrained()->onDelete('cascade');
            $table->string('role')->default('member'); // admin, librarian, instructor, member
            $table->string('status')->default('active'); // active, pending, suspended
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'institution_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institution_members');
    }
};