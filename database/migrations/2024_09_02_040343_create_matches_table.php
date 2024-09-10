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
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('order');
            $table->unsignedBigInteger('team1_id');
            $table->unsignedBigInteger('team2_id');
            $table->unsignedBigInteger('event_details_id');
            $table->unsignedInteger('team1_score')->default(0);
            $table->unsignedInteger('team2_score')->default(0);
            $table->string('team1_position')->nullable();
            $table->string('team2_position')->nullable();
            $table->unsignedBigInteger('winner_id')->nullable();
            $table->string('winner_next_position')->nullable();
            $table->string('loser_next_position')->nullable();
            $table->unsignedInteger('team1_points')->default(0);
            $table->unsignedInteger('team2_points')->default(0);
            $table->enum('match_type', ['league', 'tournament']);
            $table->string('stage_name')->nullable();
            $table->string('inner_stage_name')->nullable();
            $table->string('status')->nullable()->default("upcoming");
            $table->string('result')->nullable();
            $table->timestamps();

            $table->foreign('team1_id')->references('id')->on('teams');
            $table->foreign('team2_id')->references('id')->on('teams');
            $table->foreign('event_details_id')->references('id')
                ->on('event_details')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
