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
        Schema::table('promotions', function (Blueprint $table) {
            $table->after('target_type', function($table){
                $table->string('applied_for')->nullable()->comment('categories/products');
                $table->string('offer_type')->nullable()->comment('fixed/%');
                $table->decimal('offer_value',10,2)->nullable();
                $table->boolean('has_coupon')->default(false);
                $table->string('coupon_code')->nullable();
                $table->integer('max_uses')->default(-1)->comment('-1 means unlimited');
                $table->boolean('in_homepage')->default(false);
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            $table->dropColumn('applicable_for');
            $table->dropColumn('offer_tye');
            $table->dropColumn('offer_value');
            $table->dropColumn('has_coupon');
            $table->dropColumn('coupon_code');
            $table->dropColumn('max_uses');
            $table->dropColumn('is_homepage');
        });
    }
};
