<?php

/**
 * Migration: Create customers table
 *
 * Stores customer information for registered and guest checkout users.
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
     * Creates the customers table with fields for customer profiles,
     * contact information, and optional account creation for guest checkout.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');                       // Customer full name
            $table->string('email')->unique();            // Unique email address
            $table->string('phone')->nullable();          // Contact phone number
            $table->string('password')->nullable();       // Hashed password (nullable for guest checkout)
            $table->text('address')->nullable();          // Full address text
            $table->string('city')->nullable();           // City
            $table->string('state')->nullable();          // State/Province
            $table->text('notes')->nullable();            // Internal notes about customer
            $table->timestamps();
        });

        // Indexes for performance
        Schema::table('customers', function (Blueprint $table) {
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};