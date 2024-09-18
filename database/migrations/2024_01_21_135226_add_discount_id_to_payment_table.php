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
            $table->unsignedBigInteger(column: 'system_discount_id')->nullable();
            $table->unsignedBigInteger(column: 'user_discount_id')->nullable();
            
            $table->foreign(columns: 'system_discount_id')->references('id')->on('organizer_create_event_discounts');
            $table->foreign(columns: 'user_discount_id')->references('id')->on('user_discounts');

            $table->double('payment_amount')->nullable();
            $table->double('discount_amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            //
        });
    }
};
