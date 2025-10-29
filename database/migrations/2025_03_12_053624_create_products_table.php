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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_condition_id')->nullable();
            $table->foreignId('category_id')->nullable();
            $table->foreignId('brand_id')->nullable();
            $table->foreignId('product_unit_id')->nullable();
            $table->string('name');
            $table->string('image')->nullable();
            $table->string('sku')->nullable();
            $table->string('barcode')->nullable();
            $table->string('barcode_image')->nullable();
            $table->string('status')->nullable();
            $table->double('total_stock_quantity')->default(0);
            $table->double('low_stock_alert')->default(0);
            $table->boolean('is_variant')->default(false);
            $table->string('available_for')->nullable();
            $table->boolean('is_split_sale')->default(false);
            $table->json('taxes')->nullable();
            $table->json('meta')->nullable();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users', 'id')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users', 'id')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
