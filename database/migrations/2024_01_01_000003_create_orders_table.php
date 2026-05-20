<?php

/**
 * Migration: Create orders table
 *
 * Stores order information including customer details, product snapshots,
 * payment status, and download tokens for digital products.
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
     * Creates the orders table with comprehensive fields for tracking
     * orders, payments, and digital product downloads.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();      // Unique order identifier
            $table->foreignId('customer_id')
                  ->nullable()
                  ->constrained('customers')
                  ->onDelete('set null');                 // FK to customers (nullable for guest)
            $table->string('customer_name');              // Customer name snapshot
            $table->string('customer_email');             // Customer email snapshot
            $table->string('customer_phone')->nullable(); // Customer phone snapshot
            $table->foreignId('product_id')
                  ->constrained('products')
                  ->onDelete('cascade');                  // FK to products
            $table->string('product_name');               // Product name snapshot
            $table->integer('quantity')->default(1);     // Quantity ordered
            $table->decimal('unit_price', 12, 2);         // Unit price at time of order
            $table->decimal('subtotal', 12, 2);           // Subtotal (unit_price * quantity)
            $table->decimal('discount_amount', 12, 2)->default(0); // Discount applied
            $table->decimal('total_amount', 12, 2);      // Final total amount
            $table->string('coupon_code')->nullable();   // Applied coupon code
            $table->enum('payment_method', ['paystack', 'null'])->nullable(); // Payment method
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending'); // Payment status
            $table->string('payment_reference')->nullable(); // Payment gateway reference
            $table->timestamp('paid_at')->nullable();     // Timestamp when payment was confirmed
            $table->string('download_token')->unique()->nullable(); // Unique token for download
            $table->integer('download_count')->default(0); // Number of times downloaded
            $table->timestamp('download_expires_at')->nullable(); // Download link expiration
            $table->json('cart_data')->nullable();        // Cart snapshot (JSON)
            $table->string('ip_address')->nullable();     // Customer IP address
            $table->text('user_agent')->nullable();       // Browser user agent
            $table->text('notes')->nullable();            // Order notes
            $table->timestamps();
        });

        // Indexes for performance
        Schema::table('orders', function (Blueprint $table) {
            $table->index('order_number');
            $table->index('customer_email');
            $table->index('payment_status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};