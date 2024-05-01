<?php
namespace App\Events;

use App\Models\ActivityLogs;
use App\Models\Notifications;
use App\Models\Team;
use App\Models\User;

class TeamMemberUpdatedListener
{

    public $teamMember;

    public function handle(TeamMemberUpdated $event)
    {
        $teamName = $event->teamMember->team->teamName;
        $userName = $event->teamMember->user->name;
        $status = $event->teamMember->status;

        $teamNotification = $userNotification 
            = $action = $userLog 
            // = $teamLog 
            = null;
        $links = [];
        $hostname = config('app.url');
        $routeName = "{$hostname}/participant/team/{$event->teamMember->team_id}/manage";
        $links = json_encode([
            ['name' => 'View Team', 'url' => $routeName]
        ]);

        switch ($status) {
            case 'accepted':
                if ($event->teamMember->actor == 'team') {
                    $action = 'accepted';
                    $userLog = "You have accepted to join this team $teamName!";
                    // $teamLog = "$userName has accepted to join this team!";
                    $teamNotification = [
                        'text' => "$userName has accepted to join this team!",
                        'subject' => "Invited member joining this team",
                    ];

                    $userNotification = [
                        'text' => "$teamName has accepted you to join this team!",
                        'subject' => "Successfully joined this team",
                    ];
                } else {

                }
                break;
            case 'invited':
                if ($event->teamMember->actor == 'team') {
                    $action = 'invited';
                    $userNotification = [
                        'text' => "$teamName has invited you to join this team!",
                        'subject' => "Invitation to join a team",
                    ];
                } else {
                    $action = 'pending';
                $teamNotification = [
                    'text' => "$userName has requested to join this team!",
                    'subject' => "Request from user to join this team",
                ];

                $userLog = "You have decided to join this team $teamName!";
                }
                break;
            case 'left':
                $action = 'left';
                if ($event->teamMember->actor == 'team') {
                    $userNotification = [
                        'text' => "$teamName has accepted you to join this team!",
                        'subject' => "Successfully joined this team",
                    ];

                    $userLog = "The team, $teamName has decided to remove you!";
                    // $teamLog = "The team, $teamName has decided to remove this $userName!";

                } else {
                    $userLog = "You have left this team $teamName!";
                    $teamLog = "$userName has left this team!";
                    $teamNotification = [
                        'text' => "$userName has requested to join this team!",
                        'subject' => "Request from user to join this team",
                    ];

                }
                break;
            case 'rejected':
                $action = 'rejected';
                if ($event->teamMember->actor == 'team') {
                    $userNotification = [
                        'text' => "$teamName has rejected your request to join this team!",
                        'subject' => "Failed to join this team",
                    ];
                } else {
                    $userLog = "You have decided to leave this team $teamName!";
                    // $teamLog = "$userName has decided to leave this team!";
                }

                break;
            default:
                $userLog = "Unknown status: $status";
                $teamLog = null;
                $action = null;
        }

        // if ($teamLog) { 
        //     ActivityLogs::create([
        //         'action' => $action,
        //         'subject_id' => $event->teamMember->team_id,
        //         'subject_type' => Team::class,
        //         'log' => $teamLog,
        //     ]);
        // }

        if ($userLog) { 
            ActivityLogs::create([
                'action' => $action,
                'subject_id' => $event->teamMember->team_id,
                'subject_type' => Team::class,
                'log' => $teamLog,
            ]);
        }

        if ($teamNotification) { 
            Notifications::create([
                'data' => json_encode([
                    'text' => $teamNotification['text'],
                    'subject' => $teamNotification['subject'], 
                    'links' => $links
                ]),
                'type' => $routeName,
                'notifiable_id' => $event->teamMember->user_id,
                'notifiable_type' => User::class,
            ]);
        }

        if ($userNotification) { 
            Notifications::create([
                'data' => json_encode([
                    'text' => $userNotification['text'],
                    'subject' => $userNotification['subject'], 
                    'links' => $links
                ]),
                'type' => $routeName,
                'notifiable_id' => $event->teamMember->user_id,
                'notifiable_type' => User::class,
            ]);
        }
    }
}
