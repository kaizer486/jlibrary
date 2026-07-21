<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('community_members', function (Blueprint $table) {
            $table->string('role')->default('member')->after('user_id');
        });
    }

    public function down()
    {
        Schema::table('community_members', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};