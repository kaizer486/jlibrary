<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            // Add new columns if they don't exist
            if (!Schema::hasColumn('institutions', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('status');
            }
            
            if (!Schema::hasColumn('institutions', 'is_verified')) {
                $table->boolean('is_verified')->default(false)->after('is_featured');
            }
            
            if (!Schema::hasColumn('institutions', 'views_count')) {
                $table->integer('views_count')->default(0)->after('is_verified');
            }
            
            // Add soft deletes if not already present
            if (!Schema::hasColumn('institutions', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->dropColumn(['is_featured', 'is_verified', 'views_count']);
            $table->dropSoftDeletes();
        });
    }
};