<?php
namespace App\Models\Events;

use App\Models\ActivityLogs;
use App\Models\Notifications;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

class TeamMemberUpdated implements ShouldQueue
{
    use SerializesModels;

    public $teamMember;

    public function __construct(TeamMember $teamMember)
    {
        $this->teamMember = $teamMember;
    }

    public function handle()
    {
        $teamName = $this->teamMember->team->teamName;
        $userName = $this->teamMember->user->name;
        $status = $this->teamMember->status;

        $subject = 'Your title';
        $hostname = config('app.url');
        $routeName = "{$hostname}/participant/team/{$this->teamMember->team_id}/manage";
        $links = json_encode([
            ['name' => 'View Team', 'url' => $routeName]
        ]);

        if ($status === 'accepted') {
            $userLog = "You have accepted to join this team $teamName!";
            $teamLog = "$userName has accepted to join this team!";
            $action = 'accepted';
        } elseif ($status === 'rejected') {
            $userLog = "You have rejected to join this team $teamName!";
            $teamLog = "$userName has rejected to join this team!";
            $action = 'rejected_team';
        }

        if (in_array($status, ['accepted', 'rejected'])) { 
            ActivityLogs::create([
                'action' => $action,
                'subject_id' => $this->teamMember->user_id,
                'subject_type' => User::class,
                'log' => $userLog,
            ]);
        }

        if (in_array($status, ['accepted', 'rejected'])) { 
            ActivityLogs::create([
                'action' => $action,
                'subject_id' => $this->teamMember->team_id,
                'subject_type' => Team::class,
                'log' => $teamLog,
            ]);
        }

        if (in_array($status, ['accepted', 'rejected'])) { 
            Notifications::create([
                'data' => json_encode(['subject' => $subject, 'links' => $links]),
                'type' => $routeName,
                'notifiable_id' => $this->teamMember->user_id,
                'notifiable_type' => User::class,
            ]);
        }
    }
}
