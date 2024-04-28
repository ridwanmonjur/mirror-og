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
        $hostname = config('app.url');
        DB::unprepared('
            CREATE TRIGGER update_teams AFTER UPDATE ON team_members FOR EACH ROW
            BEGIN
                DECLARE USER_LOG VARCHAR(255);
                DECLARE TEAM_LOG VARCHAR(255);
                DECLARE TEAM_NAME VARCHAR(255);
                DECLARE USER_NAME VARCHAR(255);
                DECLARE ACTION VARCHAR(255);
            
                SELECT teams.teamName INTO TEAM_NAME
                    FROM teams 
                    WHERE teams.id = NEW.team_id;
            
                SELECT users.name INTO USER_NAME
                    FROM users 
                    WHERE users.id = NEW.user_id;
                
                -- Set the title and links based on your requirements
                SET subject = \'Your title\';
                SET route_name = CONCAT(\'{$hostname}/participant/team/\', NEW.team_id, \'/manage\');
                SET links = JSON_ARRAY(
                    JSONOBJECT(\'name\', \'View Team\', \'url\', route_name)
                );
                    
                -- Set the route name
            
                IF NEW.status = \'accepted\' THEN
                    SET USER_LOG = CONCAT(\'You have accepted to join this team \', TEAM_NAME, \' !\');
                    SET TEAM_LOG = CONCAT(USER_NAME, \' has accepted to join this team!\');
                    SET ACTION = \'accepted\';
                ELSEIF NEW.status = \'rejected\' THEN
                    SET USER_LOG = CONCAT(\'You have rejected to join this team \', TEAM_NAME, \' !\');
                    SET TEAM_LOG = CONCAT(USER_NAME, \' has rejected to join this team!\');
                    SET ACTION = \'rejected_team\';
                END IF;
            
                -- Insert activity logs
                INSERT INTO activity_logs (action, subject_id, subject_type, log)
                VALUES (ACTION, NEW.user_id, \'\\App\\Models\\User\', USER_LOG);
            
                INSERT INTO activity_logs (action, subject_id, subject_type, log)
                VALUES (ACTION, NEW.team_id, \'\\App\\Models\\Team\', TEAM_LOG);

                INSERT INTO notifications (data, type, notifiable_id, notifiable_type, created_at, updated_at)
                VALUES (JSON_OBJECT(\'subject\', title, \'links\', links), route_name, NEW.user_id, \'App\\\\Models\\\\User\', NOW(), NOW());
            
                -- Insert notifications
               
            END;
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER `update_teams`');
    }
};
