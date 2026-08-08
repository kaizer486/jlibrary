<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('helpfuls', function (Blueprint $table) {
            // Drop existing foreign keys if they exist
            $table->dropForeign(['user_id']);
            $table->dropForeign(['review_id']);
            
            // Ensure columns are the correct type
            $table->unsignedBigInteger('user_id')->change();
            $table->unsignedBigInteger('review_id')->change();
            
            // Re-add foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('review_id')->references('id')->on('reviews')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('helpfuls', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['review_id']);
        });
    }
};