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
        Schema::create('product_metas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id');
            $table->string('model')->nullable();
            $table->string('gender')->nullable();
            $table->text('measurement')->nullable();
            $table->string('unit_value')->nullable();
            $table->text('notes')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_metas');
    }
};
