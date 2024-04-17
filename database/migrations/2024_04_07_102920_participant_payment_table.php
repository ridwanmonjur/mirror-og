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
        Schema::table('participant_payments', function (Blueprint $table) {
            $table->dropForeign('payment_id');
            $table->dropColumn(['payment_request_id', 'payment_status']);
            $table->double('payment_amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participant_payments', function (Blueprint $table) {
            $table->string('payment_request_id')->unsigned();
            $table->double('payment_amount')->nullable();
            $table->foreign('payment_id')->references('id')->on('payments');
        });
    }
};
