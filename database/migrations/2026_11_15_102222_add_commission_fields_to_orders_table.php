<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'platform_commission')) {
                $table->decimal('platform_commission', 12, 2)
                    ->nullable()
                    ->after('total')
                    ->comment('Commission super-admin negotiates with the book owner');
            }

            if (!Schema::hasColumn('orders', 'commission_status')) {
                $table->string('commission_status', 20)
                    ->nullable()
                    ->after('platform_commission')
                    ->comment('pending | negotiated | collected');
            }

            if (!Schema::hasColumn('orders', 'commission_notes')) {
                $table->text('commission_notes')
                    ->nullable()
                    ->after('commission_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'commission_notes')) {
                $table->dropColumn('commission_notes');
            }
            if (Schema::hasColumn('orders', 'commission_status')) {
                $table->dropColumn('commission_status');
            }
            if (Schema::hasColumn('orders', 'platform_commission')) {
                $table->dropColumn('platform_commission');
            }
        });
    }
};