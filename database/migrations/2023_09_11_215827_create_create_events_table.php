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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('eventName');
            $table->dateTime('startDateTime');
            $table->dateTime('endDateTime');
            $table->string('eventDescription');
            $table->string('organizerName');
            $table->string('eventBanner');
            $table->string('eventTags');
            $table->string('eventTier');
            $table->string('eventType');
            $table->string('eventStatus');
            $table->integer('totalParticipants');
            $table->integer('registeredParticipants');
            $table->float('fee');
            $table->string('region');
            $table->string('prize');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('create_events');
    }
};
