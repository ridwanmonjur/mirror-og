<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('participant_follows', function (Blueprint $table) {
            $table->renameColumn('participant1_user', 'participant_follower');
            $table->renameColumn('participant2_user', 'participant_followee');
            $table->dropUnique(['participant1_user', 'participant2_user']);
        });
    }

    public function down()
    {
        Schema::table('participant_follows', function (Blueprint $table) {
            $table->renameColumn('participant_follower', 'participant1_user');
            $table->renameColumn('participant_followee', 'participant2_user');
            $table->unique(['participant1_user', 'participant2_user']);
        });
    }
};

  
