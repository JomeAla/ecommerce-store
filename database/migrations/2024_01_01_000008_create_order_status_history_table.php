<?php

/**
 * Migration: Create order_status_history table
 *
 * Stores the history of order status changes for audit trails
 * and tracking order progression.
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
     * Creates the order_status_history table for tracking all
     * status transitions of orders with optional notes and creator info.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('order_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                  ->constrained('orders')
                  ->onDelete('cascade');                // FK to orders
            $table->string('from_status')->nullable();  // Previous status
            $table->string('to_status');                 // New status
            $table->text('notes')->nullable();          // Optional notes about the change
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('admin_users')
                  ->onDelete('set null');              // FK to admin_users (who made the change)
            $table->timestamp('created_at');            // Timestamp of status change
        });

        // Indexes for performance
        Schema::table('order_status_history', function (Blueprint $table) {
            $table->index('order_id');
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
        Schema::dropIfExists('order_status_history');
    }
};