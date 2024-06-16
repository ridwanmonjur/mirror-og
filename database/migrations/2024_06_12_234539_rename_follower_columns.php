<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('participant_follows')) {
            Schema::table('participant_follows', function (Blueprint $table) {
                if (Schema::hasColumn('participant_follows', 'participant1_user') &&
                    Schema::hasColumn('participant_follows', 'participant2_user')) {
                    $table->renameColumn('participant1_user', 'participant_follower');
                    $table->renameColumn('participant2_user', 'participant_followee');
                }
            });

            Schema::table('participant_follows', function (Blueprint $table) {
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('participant_follows')) {
            Schema::table('participant_follows', function (Blueprint $table) {
                if (Schema::hasColumn('participant_follows', 'participant_follower') &&
                    Schema::hasColumn('participant_follows', 'participant_followee')) {
                    $table->renameColumn('participant_follower', 'participant1_user');
                    $table->renameColumn('participant_followee', 'participant2_user');
                }
            });
        }
    }
};
