<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('warehouse_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id');
            $table->foreignId('product_id')->nullable();
            $table->foreignId('product_variant_id')->nullable();
            $table->decimal('stock_quantity', 10, 2)->nullable();
            $table->decimal('low_stock_alert', 10, 2)->nullable();
            $table->decimal('avg_unit_cost', 10, 2)->nullable();
            $table->decimal('avg_purchase_cost', 10, 2)->nullable();
            $table->decimal('purchase_price')->nullable();
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->decimal('restaurant_sale_price', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_stocks');
    }
};
