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
            $table->dropForeign(['join_events_id']);
            $table->dropColumn('join_events_id');
            $table->unsignedBigInteger('event_details_id');
            $table->foreign('event_details_id')->references('id')->on('event_details')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('awards_results', function (Blueprint $table) {
            $table->unsignedBigInteger('join_events_id');
            $table->foreign('join_events_id')->references('id')->on('join_events')->onDelete('cascade');
            $table->dropForeign(['event_details_id']);
            $table->dropColumn('event_details_id');
        });
    }
};
