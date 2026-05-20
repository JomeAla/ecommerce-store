<?php

/**
 * Migration: Create products table
 *
 * Stores all sellable products with pricing, images, and file downloads.
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
     * Creates the products table with all e-commerce fields including
     * pricing, inventory, SEO metadata, and file download paths.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');                        // Product display name
            $table->string('slug')->unique();             // URL-friendly identifier
            $table->text('description')->nullable();       // Full product description (HTML allowed)
            $table->text('short_description')->nullable(); // Summary for cards/listings
            $table->decimal('price', 12, 2)->default(0);   // Regular price in Naira
            $table->decimal('sale_price', 12, 2)->nullable(); // Discounted price (null = no sale)
            $table->string('sku')->unique()->nullable();   // Stock keeping unit
            $table->integer('stock')->default(999);        // Available quantity (999 = unlimited)
            $table->boolean('track_stock')->default(true); // Whether to track inventory
            $table->string('category')->nullable();        // Product category
            $table->string('image')->nullable();            // Main product image path
            $table->json('images')->nullable();           // Additional images (JSON array)
            $table->text('file_path')->nullable();         // Downloadable file path (storage/)
            $table->string('file_name')->nullable();       // Display name of downloadable file
            $table->enum('product_type', ['physical', 'digital', 'service'])->default('digital');
            $table->boolean('is_active')->default(true);   // Available for purchase
            $table->boolean('is_featured')->default(false); // Show on homepage/featured
            $table->integer('sort_order')->default(0);     // Display ordering
            $table->json('meta_title')->nullable();        // SEO title (per locale)
            $table->json('meta_description')->nullable();  // SEO description (per locale)
            $table->json('meta_keywords')->nullable();     // SEO keywords (per locale)
            $table->timestamps();
            $table->softDeletes();                        // Soft delete for recovery
        });

        // Indexes for performance
        Schema::table('products', function (Blueprint $table) {
            $table->index('is_active');
            $table->index('is_featured');
            $table->index('category');
            $table->index(['is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};