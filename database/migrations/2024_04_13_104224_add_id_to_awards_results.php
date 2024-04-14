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
        Schema::table('awards_results', function (Blueprint $table) {
            $table->id();
            $table->unique(['award_id', 'join_events_id']);
            $table->dropForeign(['event_details_id', ]);
            $table->dropColumn(['event_details_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('awards_results', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->dropUnique(['award_id', 'join_events_id']);
            $table->unsignedBigInteger('team_id');
            $table->foreign('team_id')->references('id')->on('teams');
        });
    }
};
