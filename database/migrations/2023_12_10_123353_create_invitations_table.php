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
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organizer_id')->nullable();
            $table->foreign('organizer_id')->references('id')->on('users');
            $table->unsignedBigInteger('event_id')->nullable();
            $table->foreign('event_id')->references('id')->on('event_details');
            $table->unsignedBigInteger('participant_id')->nullable();
            $table->foreign('participant_id')->references('id')->on('users');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
