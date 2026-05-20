<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_zone_id')->constrained('shipping_zones')->onDelete('cascade');
            $table->string('name');
            $table->string('description')->nullable();
            $table->decimal('base_cost', 12, 2)->default(0);
            $table->decimal('cost_per_kg', 12, 2)->default(0)->comment('Additional cost per kg above first kg');
            $table->decimal('free_shipping_threshold', 12, 2)->nullable()->comment('Free shipping if order above this amount');
            $table->integer('delivery_days_min')->default(1);
            $table->integer('delivery_days_max')->default(5);
            $table->string('delivery_time_display')->nullable()->comment('e.g., "2-5 business days"');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_methods');
    }
};