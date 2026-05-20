<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('weight', 8, 2)->nullable()->after('stock')->comment('Weight in grams');
            $table->json('dimensions')->nullable()->after('weight')->comment('{"length": 10, "width": 10, "height": 10} in cm');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['weight', 'dimensions']);
        });
    }
};