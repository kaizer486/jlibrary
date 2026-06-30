<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // Add polymorphic columns
            if (!Schema::hasColumn('subscriptions', 'subscribable_type')) {
                $table->string('subscribable_type')->nullable()->after('id');
            }
            
            if (!Schema::hasColumn('subscriptions', 'subscribable_id')) {
                $table->unsignedBigInteger('subscribable_id')->nullable()->after('subscribable_type');
            }
            
            // Make institution_id nullable (since it will be replaced by polymorphic)
            $table->foreignId('institution_id')->nullable()->change();
            
            // Add indexes
            $table->index(['subscribable_type', 'subscribable_id']);
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['subscribable_type', 'subscribable_id']);
            $table->dropIndex(['subscribable_type', 'subscribable_id']);
            $table->foreignId('institution_id')->nullable(false)->change();
        });
    }
};