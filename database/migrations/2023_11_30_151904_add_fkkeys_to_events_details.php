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
        Schema::table('event_details', function (Blueprint $table) {
            $table->unsignedBigInteger('event_type_id');
            $table->unsignedBigInteger('event_tier_id');
            $table->foreign('event_type_id')->references('id')->on('event_type');
            $table->foreign('event_tier_id')->references('id')->on('event_tier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_details', function (Blueprint $table) {
            $table->dropForeign(['event_type_id', 'event_tier_id']);
        });
    }
};
