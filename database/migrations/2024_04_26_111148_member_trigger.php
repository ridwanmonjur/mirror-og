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
        DB::unprepared("DELIMITER //
        CREATE TRIGGER update_teams 
        AFTER UPDATE ON `team_members` FOR EACH ROW
        BEGIN
            DECLARE USER_LOG VARCHAR(255);
            DECLARE TEAM_LOG VARCHAR(255);
            DECLARE TEAM_NAME VARCHAR(255);
            DECLARE USER_NAME VARCHAR(255);
            DECLARE ACTION VARCHAR(255);
            SELECT teams.teamName 
                FROM teams 
                INTO TEAM_NAME
                WHERE teams.id = NEW.team_id;
            SELECT users.name 
                FROM users 
                INTO USER_NAME
                WHERE users.id = NEW.user_id;
            IF NEW.status == 'accepted' THEN
                SET USER_LOG = CONCAT('You have accepted to join this team ', TEAM_NAME, ' !');
                SET TEAM_LOG = CONCAT(USER_NAME . ' has accepted to join this team!');
                SET ACTION = 'accepted';
            ELSE IF NEW.status == 'rejected' THEN
                SET USER_LOG = CONCAT('You have accepted to join this team ', TEAM_NAME, ' !');
                SET TEAM_LOG = CONCAT(USER_NAME . ' has accepted to join this team!');
                SET ACTION = 'rejected_team';
            END IF;
            INSERT INTO `activity_logs` (`action`, `subject_id`, `subject_type`, `log`) 
                VALUES (ACTION, NEW.user_id, `\App\Models\User`, USER_LOG);
            INSERT INTO `activity_logs` (`action`, `subject_id`, `subject_type`, `log`) 
                VALUES (ACTION, NEW.team_id, `\App\Models\Team`, TEAM_LOG);
            INSERT INTO `notifications` (`action`, `subject_id`, `subject_type`, `log`) 
                VALUES (ACTION, NEW.team_id, `\App\Models\Team`, TEAM_LOG);
        END;
        //
        DELIMITER ;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER `add_Item_city`');
    }
};
