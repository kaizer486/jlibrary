<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hero_slides', function (Blueprint $table) {
            // Add new columns if they don't exist
            if (!Schema::hasColumn('hero_slides', 'slide_type')) {
                $table->string('slide_type')->default('dashboard')->after('image')->comment('dashboard, books, ai, community, custom');
            }
            
            if (!Schema::hasColumn('hero_slides', 'badge_text')) {
                $table->string('badge_text')->nullable()->after('slide_type');
            }
            
            if (!Schema::hasColumn('hero_slides', 'stats')) {
                $table->json('stats')->nullable()->after('cta_url');
            }
            
            if (!Schema::hasColumn('hero_slides', 'slide_duration')) {
                $table->integer('slide_duration')->default(5)->after('order');
            }
            
            if (!Schema::hasColumn('hero_slides', 'button_color')) {
                $table->string('button_color')->default('#7c3aed')->after('slide_duration');
            }
            
            if (!Schema::hasColumn('hero_slides', 'text_color')) {
                $table->string('text_color')->default('#ffffff')->after('button_color');
            }
            
            if (!Schema::hasColumn('hero_slides', 'settings')) {
                $table->json('settings')->nullable()->after('text_color');
            }
        });
    }

    public function down(): void
    {
        Schema::table('hero_slides', function (Blueprint $table) {
            $columns = ['slide_type', 'badge_text', 'stats', 'slide_duration', 'button_color', 'text_color', 'settings'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('hero_slides', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};