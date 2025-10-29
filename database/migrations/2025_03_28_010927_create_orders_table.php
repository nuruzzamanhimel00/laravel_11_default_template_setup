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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_man_id')->nullable()->constrained('users')->onDelete('cascade');
            // $table->integer('delivery_request_id')->nullable();
            $table->string('invoice_no', 20)->unique();
            $table->string('date');
            $table->foreignId('order_for_id')->constrained('users')->onDelete('cascade');
            $table->string('order_for')->nullable();
            $table->json('billing_info')->nullable();
            $table->json('shipping_info')->nullable();
            $table->decimal('tax_amount')->default(0)->nullable();
            $table->decimal('discount_amount')->default(0)->nullable();
            $table->decimal('global_discount')->default(0)->nullable();
            $table->string('global_discount_type')->nullable();
            $table->decimal('sub_total')->default(0)->nullable();
            $table->decimal('total')->default(0)->nullable();
            $table->decimal('total_paid')->default(0)->nullable();
            $table->string('payment_type', 50)->nullable();
            $table->string('order_status', 20)->nullable();
            $table->string('delivery_status', 20)->nullable();
            $table->string('payment_status', 20)->nullable();
            $table->date('cancel_date')->nullable();
            $table->foreignId('cancel_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->text('cancel_note')->nullable();
            $table->boolean('is_split_sale')->default(false);
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
        Schema::dropIfExists('orders');
    }
};
