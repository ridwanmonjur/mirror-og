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
        Schema::create('participant_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('team_members_id');
            $table->unsignedBigInteger('join_events_id');
            $table->unsignedBigInteger('user_id');

            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('team_members_id')->references('id')->on('team_members')->onDelete('cascade');
            $table->foreign('join_events_id')->references('id')->on('join_events')->onDelete('cascade');

            $table->timestamps();

            $table->string('payment_id')->nullable();
            $table->string('payment_request_id')->nullable;
            $table->string('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participant_payments');
    }
};
