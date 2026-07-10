<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('payment_status')->nullable()->after('status');
            $table->string('payment_method')->nullable()->after('payment_status');
            $table->string('transaction_reference')->nullable()->after('payment_method');
            $table->string('mpesa_request_id')->nullable()->after('transaction_reference');
            $table->string('mpesa_checkout_request_id')->nullable()->after('mpesa_request_id');
            $table->string('mpesa_response_code')->nullable()->after('mpesa_checkout_request_id');
            $table->text('mpesa_response_description')->nullable()->after('mpesa_response_code');
        });
    }

    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn([
                'payment_status',
                'payment_method',
                'transaction_reference',
                'mpesa_request_id',
                'mpesa_checkout_request_id',
                'mpesa_response_code',
                'mpesa_response_description',
            ]);
        });
    }
};