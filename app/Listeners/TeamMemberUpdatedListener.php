<?php
namespace App\Listeners;

use App\Events\TeamMemberUpdated;
use App\Models\ActivityLogs;
use App\Models\Notifications;
use App\Models\Team;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;

class TeamMemberUpdatedListener 
{

    public $teamMember;

    public function handle(TeamMemberUpdated $event)
    {
        try{
            Log::info("OF COURSE IT RAN!");
            Log::info($event->teamMember->toArray);
            $teamName = $event->teamMember->team->teamName;
            $userName = $event->teamMember->user->name;
            $status = $event->teamMember->status;

            $teamCreatorNotification = $userNotification  = $action = $userLog = null;
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
                        $userLog = "You join the team $teamName!";
                        $userNotification = [
                            'text' => "$teamName has accepted you to join this team!",
                            'subject' => "Successfully joined this team",
                        ];
                    } else {
                        $action = 'accepted';
                        $userLog = "You join the team $teamName!";
                        $teamCreatorNotification = [
                            'text' => "The user $userName has joined your team $teamName!",
                            'subject' => "Invited member joining this team",
                        ];
                    }
                    break;
                case 'pending':
                    if ($event->teamMember->actor == 'team') {
                        $action = 'invited';
                        $userNotification = [
                            'text' => "The team $teamName has invited you to join them!",
                            'subject' => "Invitation to join a team",
                        ];
                    } else {
                        $action = 'pending';
                        $teamCreatorNotification = [
                            'text' => "The user $userName has has asked to join your team $teamName!",
                            'subject' => "Requesting to join this team",
                        ];
                    }
                    break;
                case 'left':
                    $action = 'left';
                    if ($event->teamMember->actor == 'team') {
                        $userLog = "You have left this team $teamName!";
                        $userNotification = [
                            'text' => "You have been removed from this team $teamName!",
                            'subject' => "Removal from team",
                        ];
                        $userLog = "You have left this team $teamName!";
                    } else {
                        $userLog = "You have left this team $teamName!";
                        $teamCreatorNotification = [
                            'text' => "The user $userName has has left your team $teamName!",
                            'subject' => "Leaving the team",
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
                        $teamCreatorNotification = [
                            'text' => "The user $userName has has rejected the joining request to your team $teamName!",
                            'subject' => "Failed to recruit into your team",
                        ];
                    }

                    break;
                default:
                    $userLog = "Unknown status: $status";
            }

            if ($userLog) { 
                ActivityLogs::create([
                    'action' => $action,
                    'subject_id' => $event->teamMember->team_id,
                    'subject_type' => Team::class,
                    'log' => $userLog,
                ]);
            }

            if ($teamCreatorNotification) { 
                Notifications::create([
                    'data' => [
                        'data' => $teamCreatorNotification['text'],
                        'subject' => $teamCreatorNotification['subject'], 
                        'links' => $links
                    ],
                    'type' => Notifications::class,
                    'notifiable_id' => $event->teamMember->team->creator_id,
                    'notifiable_type' => User::class,
                ]);
            }

            if ($userNotification) { 
                Notifications::create([
                    'data' => [
                        'data' => $userNotification['text'],
                        'subject' => $userNotification['subject'], 
                        'links' => $links
                    ],
                    'type' => Notifications::class,                    
                    'notifiable_id' => $event->teamMember->user_id,
                    'notifiable_type' => User::class,
                ]);
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
