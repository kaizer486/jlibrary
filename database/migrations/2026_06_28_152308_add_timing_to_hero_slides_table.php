<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hero_slides', function (Blueprint $table) {
            $table->integer('slide_duration')->default(5)->after('order')->comment('Duration in seconds');
            $table->string('button_color')->default('#7c3aed')->after('cta_url');
            $table->string('text_color')->default('#ffffff')->after('button_color');
            $table->json('settings')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('hero_slides', function (Blueprint $table) {
            $table->dropColumn(['slide_duration', 'button_color', 'text_color', 'settings']);
        });
    }
};