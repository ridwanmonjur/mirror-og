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
        Schema::create('organizer_follows', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('participant_user_id');
            $table->unsignedBigInteger('organizer_user_id');
            $table->timestamps();
            $table->unique(['participant_user_id', 'organizer_user_id']);
            $table->foreign('participant_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('organizer_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizer_follows');
    }
};
