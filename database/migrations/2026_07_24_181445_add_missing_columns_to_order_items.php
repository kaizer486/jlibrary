<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Add order_id foreign key
            if (!Schema::hasColumn('order_items', 'order_id')) {
                $table->foreignId('order_id')->constrained('orders')->onDelete('cascade')->after('id');
            }
            
            // Add book_id foreign key
            if (!Schema::hasColumn('order_items', 'book_id')) {
                $table->foreignId('book_id')->constrained('books')->onDelete('cascade')->after('order_id');
            }
            
            // Add quantity
            if (!Schema::hasColumn('order_items', 'quantity')) {
                $table->integer('quantity')->default(1)->after('book_id');
            }
            
            // Add price
            if (!Schema::hasColumn('order_items', 'price')) {
                $table->decimal('price', 10, 2)->after('quantity');
            }
            
            // Add subtotal
            if (!Schema::hasColumn('order_items', 'subtotal')) {
                $table->decimal('subtotal', 10, 2)->after('price');
            }
            
            // Add indexes
            $table->index('order_id');
            $table->index('book_id');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn([
                'order_id', 
                'book_id', 
                'quantity', 
                'price', 
                'subtotal'
            ]);
        });
    }
};