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
        Schema::create('rosters_captain', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_member_id')->constrained('team_members');
            $table->foreignId('join_events_id')->constrained('join_events')->onDelete('cascade');
            $table->foreignId('teams_id')->constrained('teams')->references('id')->onDelete('cascade');
            $table->unique(['teams_id', 'join_events_id', 'team_member_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rosters_captain');
    }
};
