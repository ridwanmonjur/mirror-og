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
            $table->dropForeign(['event_details_id']);
            $table->dropColumn('event_details_id');
        });
    }
};
