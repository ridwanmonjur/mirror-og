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
        Schema::create('event_signup_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('event_details')->onDelete('cascade');
            $table->dateTime('signup_open');
            $table->dateTime('normal_signup_start_advanced_close');
            $table->dateTime('signup_close');
            $table->unique('event_id');
        });
        Schema::create('event_tier_type_signup_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tier_id')->constrained('event_tier');
            $table->foreignId('type_id')->constrained('event_type');
            $table->integer('signup_open');
            $table->integer('signup_close');
            $table->integer('normal_signup_start_advanced_close');
            $table->unique(['tier_id', 'type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_tier', function (Blueprint $table) {
            Schema::dropIfExists('event_signup_dates');
            Schema::dropIfExists('event_tier_type_signup_dates');

        });
    }
};
