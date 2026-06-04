<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the table if it exists (fix for stuck migration)
        Schema::dropIfExists('commission_settings');
        
        // Create the table fresh
        Schema::create('commission_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->decimal('value', 12, 2)->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });
        
        // Insert default commission settings
        DB::table('commission_settings')->insert([
            ['key' => 'institution_commission', 'value' => 70, 'description' => 'Percentage for institution from book sales'],
            ['key' => 'platform_commission', 'value' => 20, 'description' => 'Percentage for platform from book sales'],
            ['key' => 'author_commission', 'value' => 10, 'description' => 'Percentage for author from book sales'],
            ['key' => 'min_withdrawal', 'value' => 20000, 'description' => 'Minimum withdrawal amount in TSh'],
            ['key' => 'subscription_basic', 'value' => 25000, 'description' => 'Monthly subscription fee for basic plan'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_settings');
    }
};