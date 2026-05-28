<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->integer('progress_percent')->default(0);
            $table->integer('current_page')->default(0);
            $table->enum('status', ['want_to_read', 'reading', 'completed'])->default('want_to_read');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_books');
    }
};