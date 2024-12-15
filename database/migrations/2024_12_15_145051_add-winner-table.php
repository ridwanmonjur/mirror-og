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
            if (Schema::hasColumn('matches', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('matches', 'result')) {
                $table->dropColumn('result');
            }
            if (Schema::hasColumn('matches', 'team1_score')) {
                $table->dropColumn('team1_score');
            }
            if (Schema::hasColumn('matches', 'team2_score')) {
                $table->dropColumn('team2_score');
            }
            if (Schema::hasColumn('matches', 'team1_points')) {
                $table->dropColumn('team1_points');
            }
            if (Schema::hasColumn('matches', 'team2_points')) {
                $table->dropColumn('team2_points');
            }
            if (Schema::hasColumn('matches', 'team1_previous_position')) {
                $table->dropColumn('team1_previous_position');
            }
            if (Schema::hasColumn('matches', 'team1_opponent_previous_position')) {
                $table->dropColumn('team1_opponent_previous_position');
            }
            if (Schema::hasColumn('matches', 'team2_previous_position')) {
                $table->dropColumn('team2_previous_position');
            }
            if (Schema::hasColumn('matches', 'team2_opponent_previous_position')) {
                $table->dropColumn('team2_opponent_previous_position');
            }
        });
 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            if (!Schema::hasColumn('matches', 'status')) {
                $table->string('status')->nullable()->default('upcoming');
            }
            if (!Schema::hasColumn('matches', 'result')) {
                $table->string('result')->nullable();
            }
            if (!Schema::hasColumn('matches', 'team1_score')) {
                $table->unsignedInteger('team1_score')->default(0);
            }
            if (!Schema::hasColumn('matches', 'team2_score')) {
                $table->unsignedInteger('team2_score')->default(0);
            }
            if (!Schema::hasColumn('matches', 'team1_points')) {
                $table->unsignedInteger('team1_points')->default(0);
            }
            if (!Schema::hasColumn('matches', 'team2_points')) {
                $table->unsignedInteger('team2_points')->default(0);
            }
            if (!Schema::hasColumn('matches', 'team1_previous_position')) {
                $table->string('team1_previous_position')->nullable();
            }
            if (!Schema::hasColumn('matches', 'team1_opponent_previous_position')) {
                $table->string('team1_opponent_previous_position')->nullable();
            }
            if (!Schema::hasColumn('matches', 'team2_previous_position')) {
                $table->string('team2_previous_position')->nullable();
            }
            if (!Schema::hasColumn('matches', 'team2_opponent_previous_position')) {
                $table->string('team2_opponent_previous_position')->nullable();
            }
        });
 
    }
};
