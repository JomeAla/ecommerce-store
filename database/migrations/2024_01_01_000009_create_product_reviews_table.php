<?php

/**
 * Migration: Create product_reviews table
 *
 * Stores customer reviews and ratings for products with
 * approval workflow for moderation.
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
     * Creates the product_reviews table with fields for product ratings,
     * customer information, comments, and approval status.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                  ->constrained('products')
                  ->onDelete('cascade');                // FK to products
            $table->foreignId('customer_id')
                  ->nullable()
                  ->constrained('customers')
                  ->onDelete('set null');              // FK to customers (nullable for guest reviews)
            $table->string('name');                       // Reviewer name
            $table->string('email');                      // Reviewer email
            $table->tinyInteger('rating');                // Rating value (1-5)
            $table->text('comment')->nullable();         // Review comment
            $table->boolean('is_approved')->default(false); // Approval status
            $table->timestamps();
        });

        // Indexes for performance
        Schema::table('product_reviews', function (Blueprint $table) {
            $table->index('product_id');
            $table->index('is_approved');
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
        Schema::dropIfExists('product_reviews');
    }
};