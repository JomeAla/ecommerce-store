<?php

/**
 * Migration: Create settings table
 *
 * Stores application settings and configuration options including
 * general, payment, and email settings.
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
     * Creates the settings table with support for different value types
     * and public/private settings access control.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();             // Unique setting key
            $table->text('value')->nullable();           // Setting value
            $table->string('group')->nullable();         // Setting group (general, payment, email)
            $table->enum('type', ['text', 'textarea', 'boolean', 'json']); // Value type
            $table->boolean('is_public')->default(false); // Publicly accessible without auth
            $table->timestamps();
        });

        // Indexes for performance
        Schema::table('settings', function (Blueprint $table) {
            $table->index('key');
            $table->index('group');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};