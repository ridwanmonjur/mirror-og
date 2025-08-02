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
        if (! Schema::hasTable('cart_item_product_variants')) {
            Schema::create('cart_item_product_variants', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('cart_item_id');
                $table->unsignedBigInteger('variant_id');
                $table->timestamps();

                $table->foreign('cart_item_id')->references('id')->on('cart_items')->onDelete('cascade');
                $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('cascade');

                $table->unique(['cart_item_id', 'variant_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_item_product_variants');
    }
};
