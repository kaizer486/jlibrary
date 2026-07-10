<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('books', function (Blueprint $table) {
            // Book type: softcopy, hardcopy, both
            $table->enum('book_type', ['softcopy', 'hardcopy', 'both'])->default('both');
            
            // Softcopy price
            $table->decimal('softcopy_price', 10, 2)->nullable()->after('price');
            
            // Hardcopy price
            $table->decimal('hardcopy_price', 10, 2)->nullable()->after('softcopy_price');
            
            // Stock quantity for hardcopy
            $table->integer('stock_quantity')->default(0)->after('hardcopy_price');
            
            // Is this a bookstore product?
            $table->boolean('is_bookstore_item')->default(false)->after('stock_quantity');
        });
    }

    public function down()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn([
                'book_type',
                'softcopy_price',
                'hardcopy_price',
                'stock_quantity',
                'is_bookstore_item'
            ]);
        });
    }
};