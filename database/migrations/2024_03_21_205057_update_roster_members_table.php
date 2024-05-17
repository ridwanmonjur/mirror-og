<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('roster_members', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->unique(['user_id', 'join_events_id']);
            $table->unique(['team_member_id', 'join_events_id']);
        });
    }

    public function down()
    {
        Schema::table('roster_members', function (Blueprint $table) {
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            // $table->dropUnique(['user_id', 'join_events_id']);
            // $table->dropUnique(['team_member_id', 'join_events_id']);
        });
    }
};
