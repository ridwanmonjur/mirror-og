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
        Schema::table('roster_members', function (Blueprint $table) {
            if (!Schema::hasColumn('roster_members', 'vote_to_quit')) {
                $table->boolean('vote_to_quit')->nullable();
            }
        });

        Schema::table('join_events', function (Blueprint $table) {
            if (!Schema::hasColumn('join_events', 'vote_ongoing')) {
                $table->boolean('vote_ongoing')->nullable();
            }

            if (!Schema::hasColumn('join_events', 'roster_captain_id')) {
                $table->unsignedBigInteger('roster_captain_id')->nullable();
                $table->foreign('roster_captain_id')
                    ->references('id')
                    ->on('roster_members')
                    ->onDelete('set null');
            }

            if (!Schema::hasColumn('join_events', 'vote_starter_id')) {
                $table->unsignedBigInteger('vote_starter_id')->nullable();
                $table->foreign('vote_starter_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('join_events', function (Blueprint $table) {
            if (Schema::hasColumn('join_events', 'roster_captain_id')) {
                $table->dropForeign(['roster_captain_id']);
                $table->dropColumn('roster_captain_id');
            }

            if (Schema::hasColumn('join_events', 'vote_ongoing')) {
                $table->dropColumn('vote_ongoing');
            }

            if (Schema::hasColumn('join_events', 'vote_starter_id')) {
                $table->dropForeign(['vote_starter_id']);
                $table->dropColumn('vote_starter_id');
            }
        });
    
        Schema::table('roster_members', function (Blueprint $table) {
            if (Schema::hasColumn('roster_members', 'vote_to_quit')) {
                $table->dropColumn('vote_to_quit');
            }
        });
    }
};