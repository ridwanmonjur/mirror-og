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
        if (!Schema::hasTable('order_item_product_variants')) {
            Schema::create('order_item_product_variants', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('order_product_id');
                $table->unsignedBigInteger('variant_id');
                $table->timestamps();
                
                $table->foreign('order_product_id')->references('id')->on('order_product')->onDelete('cascade');
                $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('cascade');
                
                $table->unique(['order_product_id', 'variant_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_item_product_variants');
    }
};