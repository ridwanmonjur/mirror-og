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
            $table->dropColumn(['payment_request_id', 'payment_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participant_payments', function (Blueprint $table) {
            $table->string('payment_request_id')->nullable();
            $table->string('payment_status')->nullable();
        });
    }
};
