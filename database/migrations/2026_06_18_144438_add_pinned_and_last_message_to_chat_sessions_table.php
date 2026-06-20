<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_sessions', function (Blueprint $table) {
            $table->boolean('is_pinned')->default(false)->after('messages');
            $table->timestamp('last_message_at')->nullable()->after('is_pinned');
            
            // Add indexes for performance
            $table->index(['user_id', 'last_message_at']);
            $table->index(['user_id', 'is_pinned']);
        });
    }

    public function down(): void
    {
        Schema::table('chat_sessions', function (Blueprint $table) {
            $table->dropColumn(['is_pinned', 'last_message_at']);
        });
    }
};