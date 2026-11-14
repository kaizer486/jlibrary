<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `orders` MODIFY `institution_id` BIGINT UNSIGNED NULL");
    }

    public function down(): void
    {
        $authorOrderCount = DB::table('orders')->whereNull('institution_id')->count();

        if ($authorOrderCount === 0) {
            DB::statement("ALTER TABLE `orders` MODIFY `institution_id` BIGINT UNSIGNED NOT NULL");
        }
    }
};