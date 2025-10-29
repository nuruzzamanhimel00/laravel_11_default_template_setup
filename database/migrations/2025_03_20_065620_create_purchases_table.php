<?php

use App\Models\Purchase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_number')->unique();
            $table->foreignId('supplier_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->string('company')->nullable();
            $table->date('date')->nullable();
            $table->string('address')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('zipcode')->nullable();
            $table->text('short_address')->nullable();
            $table->text('notes')->nullable();
            // $table->decimal('paid_amount', 10, 2)->nullable();
            $table->decimal('total', 10, 2)->nullable();
            // $table->decimal('due_amount', 10, 2)->nullable();
            $table->string('status')->nullable()->default(Purchase::STATUS_REQUESTED);
            $table->date('cancel_date')->nullable();
            $table->foreignId('cancel_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->text('cancel_note')->nullable();
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
        Schema::dropIfExists('purchases');
    }
};
