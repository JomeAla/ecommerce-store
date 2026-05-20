<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('shipping_zone_id')->nullable()->after('notes')->constrained('shipping_zones')->onDelete('set null');
            $table->string('shipping_method')->nullable()->after('shipping_zone_id');
            $table->decimal('shipping_cost', 12, 2)->default(0)->after('shipping_method');
            $table->json('shipping_address')->nullable()->after('shipping_cost')->comment('Full shipping address as JSON');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['shipping_zone_id']);
            $table->dropColumn(['shipping_zone_id', 'shipping_method', 'shipping_cost', 'shipping_address']);
        });
    }
};