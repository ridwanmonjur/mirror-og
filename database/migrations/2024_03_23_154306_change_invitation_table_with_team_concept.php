<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('event_invitations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('participant_id');

            $table->unsignedBigInteger('team_id')->nullable();
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('event_invitations', function (Blueprint $table) {
            $table->unsignedBigInteger('participant_user_id')->nullable();
            $table->foreign('participant_id')->references('id')->on('users')->onDelete('cascade');

            $table->dropConstrainedForeignId('team_id');
        });
    }
};
