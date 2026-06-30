<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bookshop Books table
        Schema::create('bookshop_books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('author')->nullable();
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('stock_quantity')->default(0);
            $table->integer('sold_count')->default(0);
            $table->enum('status', ['active', 'inactive', 'out_of_stock'])->default('active');
            $table->string('category')->nullable();
            $table->string('isbn')->nullable();
            $table->integer('pages')->nullable();
            $table->string('publisher')->nullable();
            $table->year('publication_year')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Bookshop Orders table (for tracking sales)
        Schema::create('bookshop_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])->default('pending');
            $table->string('payment_method')->nullable();
            $table->text('shipping_address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Bookshop Order Items table
        Schema::create('bookshop_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('bookshop_orders')->onDelete('cascade');
            $table->foreignId('book_id')->constrained('bookshop_books')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->decimal('total', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookshop_order_items');
        Schema::dropIfExists('bookshop_orders');
        Schema::dropIfExists('bookshop_books');
    }
};