<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('books', function (Blueprint $table) {
            // Check if column exists before adding
            if (!Schema::hasColumn('books', 'sub_category')) {
                $table->string('sub_category')->nullable()->after('category');
            }
            
            if (!Schema::hasColumn('books', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('status');
            }
            
            if (!Schema::hasColumn('books', 'is_trending')) {
                $table->boolean('is_trending')->default(false)->after('is_featured');
            }
            
            if (!Schema::hasColumn('books', 'published_date')) {
                $table->date('published_date')->nullable()->after('total_pages');
            }
        });
    }

    public function down()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn([
                'sub_category',
                'is_featured',
                'is_trending',
                'published_date'
            ]);
        });
    }
};