<?php

/**
 * Migration: Create failed_jobs table
 *
 * Stores failed queue jobs for debugging and retry purposes.
 * Standard Laravel migration for queue job failure tracking.
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
     * Creates the failed_jobs table for storing information about
     * failed queue jobs including connection, queue, payload, and exceptions.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique()->nullable(); // Job UUID for identification
            $table->text('connection');                   // Queue connection name
            $table->text('queue');                        // Queue name
            $table->longText('payload');                  // Job payload (serialized data)
            $table->longText('exception');                // Exception message/stack trace
            $table->timestamp('failed_at')->useCurrent(); // Failure timestamp
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('failed_jobs');
    }
};