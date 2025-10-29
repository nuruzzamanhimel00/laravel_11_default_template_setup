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
        Schema::table('orders', function (Blueprint $table) {
            $table->after('global_discount_type', function($table): void {
                $table->foreignId('delivery_charge_id')->nullable()->constrained('delivery_charges')->onDelete('cascade');
                $table->decimal('delivery_cost')->default(0);
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('delivery_charge_id');
            $table->dropColumn('delivery_cost');
        });
    }
};
