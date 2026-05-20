<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('countries')->nullable()->comment('ISO country codes or country names');
            $table->json('states')->nullable()->comment('State names for matching');
            $table->string('country_codes')->nullable()->comment('Comma-separated ISO codes');
            $table->string('state_codes')->nullable()->comment('Comma-separated state codes');
            $table->boolean('is_international')->default(false);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_zones');
    }
};