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
        Schema::table('matches', function (Blueprint $table) {
            // Check if columns exist before trying to drop them
            if (Schema::hasColumn('matches', 'winner_id')) {
                $table->dropColumn('winner_id');
            }
            
            if (Schema::hasColumn('matches', 'winner_next_position')) {
                $table->dropColumn('winner_next_position');
            }
            
            if (Schema::hasColumn('matches', 'loser_next_position')) {
                $table->dropColumn('loser_next_position');
            }
            
            if (Schema::hasColumn('matches', 'match_type')) {
                $table->dropColumn('match_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('matches', 'winner_id')) {
                $table->unsignedBigInteger('winner_id')->nullable();
            }
            
            if (!Schema::hasColumn('matches', 'winner_next_position')) {
                $table->string('winner_next_position')->nullable();
            }
            
            if (!Schema::hasColumn('matches', 'loser_next_position')) {
                $table->string('loser_next_position')->nullable();
            }
            
            if (!Schema::hasColumn('matches', 'match_type')) {
                $table->enum('match_type', ['league', 'tournament']);
            }
        });
    }
};