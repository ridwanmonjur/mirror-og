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
    // TODO: CREATE FOR PENDING, REJECTED
    public function up()
    {
        Schema::table('team_members', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('team_members', function (Blueprint $table) {
            $table->enum('status', ['pending', 'accepted', 'rejected', 'invited', 'left'])
                ->default('pending');
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
        });
    }

};
