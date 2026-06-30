<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrowings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('institution_id')->constrained()->onDelete('cascade');
            $table->date('borrowed_at');
            $table->date('due_date');
            $table->date('returned_at')->nullable();
            $table->string('status')->default('borrowed'); // borrowed, returned, overdue, lost
            $table->text('notes')->nullable();
            $table->foreignId('borrowed_by')->nullable()->constrained('users')->nullOnDelete(); // librarian/admin who processed
            $table->foreignId('returned_to')->nullable()->constrained('users')->nullOnDelete(); // librarian/admin who processed return
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index(['book_id', 'status']);
            $table->index(['institution_id', 'status']);
            $table->index(['due_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrowings');
    }
};