<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('icon')->default('ti-trophy');
            $table->string('color')->default('#fbbf24');
            $table->integer('required_points')->default(0);
            $table->enum('type', ['certificate', 'quiz_master', 'streak', 'social'])->default('certificate');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};