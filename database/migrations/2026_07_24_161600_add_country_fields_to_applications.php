<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            if (!Schema::hasColumn('applications', 'full_name')) {
                $table->string('full_name')->nullable()->after('user_id');
            }
            
            if (!Schema::hasColumn('applications', 'email')) {
                $table->string('email')->nullable()->after('full_name');
            }
            
            if (!Schema::hasColumn('applications', 'country')) {
                $table->string('country')->nullable()->after('email');
            }
            
            if (!Schema::hasColumn('applications', 'country_code')) {
                $table->string('country_code')->nullable()->after('country');
            }
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumnIfExists('full_name');
            $table->dropColumnIfExists('email');
            $table->dropColumnIfExists('country');
            $table->dropColumnIfExists('country_code');
        });
    }
};