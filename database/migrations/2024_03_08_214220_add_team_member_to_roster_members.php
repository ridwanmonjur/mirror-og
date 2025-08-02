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
            $table->foreignId('team_member_id')
                ->constrained('team_members')
                ->onDelete('cascade');

            $table->foreignId('team_id')
                ->constrained('teams')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roster_members', function (Blueprint $table) {
            if (Schema::hasColumn('roster_members', 'team_member_id')) {
                $table->dropConstrainedForeignId('team_member_id');
            }

            if (Schema::hasColumn('roster_members', 'team_id')) {
                $table->dropConstrainedForeignId('team_id');
            }
        });
    }
};
