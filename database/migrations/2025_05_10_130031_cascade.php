<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('roster_history', function (Blueprint $table) {
            // Drop existing foreign keys
            $table->dropForeign(['user_id']);
            $table->dropForeign(['join_events_id']);
            
            // Add cascade foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('join_events_id')->references('id')->on('join_events')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('roster_history', function (Blueprint $table) {
            // Drop cascade foreign keys
            $table->dropForeign(['user_id']);
            $table->dropForeign(['join_events_id']);
            
            // Recreate regular foreign keys
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('join_events_id')->references('id')->on('join_events');
        });
    }
};