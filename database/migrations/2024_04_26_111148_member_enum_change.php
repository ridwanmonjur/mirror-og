<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('team_members', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('team_members', function (Blueprint $table) {
            $table->enum('status', ['pending', 'accepted', 'rejected', 'left'])
                ->default('pending');
            $table->renameColumn('rejector', 'actor');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('team_members', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('team_members', function (Blueprint $table) {
            $table->enum('status', ['pending', 'accepted', 'rejected', 'invited'])
                ->default('pending');
            $table->renameColumn('actor', 'rejector');
        });
    }

};
