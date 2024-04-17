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
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropForeign('discount_id');
            $table->dropColumn(['discount_id', 'discount_amount']);
            $table->dropColumn('updated_at');
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('discount_id')->nullable();
            $table->double('discount_amount')->nullable();
            $table->foreign('discount_id')->references('id')->on('discounts');
            $table->timestamp('updated_at')->nullable();
        });
    }

    
    
};
