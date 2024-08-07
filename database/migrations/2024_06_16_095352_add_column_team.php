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
        Schema::table('team_profile', function (Blueprint $table) {
            if (! Schema::hasColumn('team_profile', 'id')) {
                $table->id();
            }

            if (! Schema::hasColumn('team_profile', 'user_id')) {
                $table->unsignedBigInteger('team_id');
                $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('team_profile', function (Blueprint $table) {
            if (Schema::hasColumn('team_profile', 'id')) {
                $table->dropColumn('id');
            }

            if (Schema::hasColumn('team_profile', 'team_id')) {
                $table->dropConstrainedForeignId('team_id');
            }
        });
    }
};
