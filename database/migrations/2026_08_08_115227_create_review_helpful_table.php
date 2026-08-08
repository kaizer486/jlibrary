<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('review_helpful', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('review_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // Prevent duplicate helpful marks
            $table->unique(['user_id', 'review_id']);
            
            // Add indexes for faster queries
            $table->index(['review_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('review_helpful');
    }
};