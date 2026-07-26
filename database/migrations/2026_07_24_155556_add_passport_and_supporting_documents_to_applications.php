<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            if (!Schema::hasColumn('applications', 'passport_photo')) {
                $table->string('passport_photo')->nullable()->after('phone');
            }
            
            if (!Schema::hasColumn('applications', 'supporting_document')) {
                $table->string('supporting_document')->nullable()->after('passport_photo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumnIfExists('passport_photo');
            $table->dropColumnIfExists('supporting_document');
        });
    }
};