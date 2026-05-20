<?php

/**
 * Migration: Create cart table
 *
 * Stores shopping cart items for both authenticated customers
 * and guest users via session ID.
 *
 * @author JoAla Team
 * @version 1.0.0
 */
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the cart table with session-based and customer-based
     * cart item storage with unique constraint for upsert behavior.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->string('session_id');                // Session identifier for guest users
            $table->foreignId('customer_id')
                  ->nullable()
                  ->constrained('customers')
                  ->onDelete('set null');               // FK to customers (nullable for guests)
            $table->foreignId('product_id')
                  ->constrained('products')
                  ->onDelete('cascade');                // FK to products
            $table->integer('quantity')->default(1);   // Quantity in cart
            $table->decimal('price_at_time', 12, 2);     // Price snapshot at time added
            $table->timestamps();

            // Unique constraint for upsert behavior (one product per session)
            $table->unique(['session_id', 'product_id']);
        });

        // Indexes for performance
        Schema::table('carts', function (Blueprint $table) {
            $table->index('session_id');
            $table->index('customer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};