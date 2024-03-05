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
        Schema::table('join_events', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        
            $table->foreignId('team_id')
                  ->constrained('teams')
                  ->index('join_events_team_id_foreign')
                  ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('join_events', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->foreign('user_id')
                ->references('id')->on('users');
        });
    }
};
