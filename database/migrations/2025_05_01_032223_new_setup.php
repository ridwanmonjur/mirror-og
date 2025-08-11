<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\EventTier;
use App\Services\BracketDataService;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brackets_setup', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_tier_id');
            $table->string('team1_position')->nullable();
            $table->string('stage_name')->nullable();
            $table->string('inner_stage_name')->nullable();
            $table->string('order')->nullable();

            $table->string('team2_position')->nullable();
            $table->string('winner_next_position')->nullable();
            $table->string('loser_next_position')->nullable();

            $table->foreign('event_tier_id')
                ->references('id')
                ->on('event_tier')
                ->onDelete('cascade');

            $table->index(['event_tier_id']);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('brackets_setup');
    }

    /**
     * Seed the brackets_setup table with data from BracketDataService
     */
    
};
