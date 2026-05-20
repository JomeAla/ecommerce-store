<?php

/**
 * Migration: Create admin_users table
 *
 * Stores administrator users for the backend management panel
 * with role-based access control.
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
     * Creates the admin_users table with fields for authentication,
     * role management, and login tracking.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('admin_users', function (Blueprint $table) {
            $table->id();
            $table->string('name');                       // Admin user full name
            $table->string('email')->unique();            // Unique email address
            $table->string('password');                   // Hashed password
            $table->enum('role', ['admin', 'manager', 'support']); // User role
            $table->boolean('is_active')->default(true); // Account active status
            $table->timestamp('last_login_at')->nullable(); // Last login timestamp
            $table->timestamps();
        });

        // Indexes for performance
        Schema::table('admin_users', function (Blueprint $table) {
            $table->index('email');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_users');
    }
};