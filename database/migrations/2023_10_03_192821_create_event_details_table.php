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
        Schema::create('event_details', function (Blueprint $table) {
            $table->id();
            $table->string('eventName')->nullable();
            $table->date('startDate')->nullable();
            $table->date('endDate')->nullable();
            $table->time('startTime')->nullable();
            $table->time('endTime')->nullable();
            $table->string('eventDescription')->nullable();
            $table->string('eventBanner')->nullable();
            $table->string('eventTags')->nullable();
            $table->string('status')->nullable();
            $table->string('venue')->nullable();
            $table->string('sub_action_public_date')->nullable();
            $table->string('sub_action_public_time')->nullable();
            $table->string('sub_action_private')->nullable();
            $table->string('action')->nullable();
            $table->string('caption')->nullable();
            $table->foreignId('user_id')->constrained(
            table: 'users', indexName: 'event_details_user_id_foreign')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::table('event_details', function (Blueprint $table) {
        //     $table->dropForeign(['event_id']);
        // });
        Schema::dropIfExists('event_details');
    }
};
