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
            $table->dropConstrainedForeignId('user_id');

            $table->foreignId('team_id')
                ->constrained('teams')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('join_events', function (Blueprint $table) {
            $table->dropConstrainedForeignId('team_id');

            $table->foreign('user_id')
                ->references('id')->on('users');
        });
    }
};
