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
        Schema::create('event_tier', function (Blueprint $table) {
            $table->id();
            $table->string('eventTier')->nullable();
            $table->string('tierIcon')->nullable();
            $table->string('tierTeamSlot')->nullable();
            $table->string('tierPrizePool')->nullable();
            $table->string('tierEntryFee')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_tier', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
    }
};
