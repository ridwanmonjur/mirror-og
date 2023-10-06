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
        Schema::create('event_categories', function (Blueprint $table) {
            $table->id();
            $table->string('gameTitle');
            $table->string('gameIcon');
            $table->string('eventType');
            $table->string('eventTier');
            $table->string('tierIcon');
            
            // $table->unsignedBigInteger('eventID');
            // $table->foreign('eventID')->references('id')->on('event_details');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_category');
    }
};
