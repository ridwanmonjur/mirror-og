<?php

namespace App\Listeners;

use App\Events\TeamMemberCreated;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class TeamMemberCreatedListener implements ShouldQueue
{
    public $teamMember;

    public function handle(TeamMemberCreated $event)
    {
        try {
            $event->teamMember->load([
                'team:id,teamName,creator_id',  
                'user:id,name,userBanner'  
            ]);

            $teamName = $event->teamMember->team->teamName;
            $user = $event->teamMember->user;
            $userName = $user->name;
            $userId = $user->id;
            $status = $event->teamMember->status;
            $teamCreatorNotification = $userNotification = $action = $userLog = null;
            $links = [];
            $hostname = config('app.url');
            $routeName = "{$hostname}participant/team/{$event->teamMember->team_id}/manage";
            $links = [
                ['name' => 'View Team', 'url' => $routeName],
            ];

            switch ($status) {
                case 'pending':
                    if ($event->teamMember->actor === 'team') {
                        $action = 'invited';
                        $userNotification = [
                            'text' => <<<HTML
                                <span class="notification-gray">
                                    The team 
                                    <span class="notification-black">{$teamName}</span> 
                                    has invited you to join them.
                                </span>
                                HTML,
                            'subject' => 'Invitation to join a team',
                        ];
                    } else {
                        $action = 'pending';
                        $teamCreatorNotification = [
                            'text' => <<<HTML
                                <span class="notification-gray">
                                    The user 
                                    <span class="notification-black">{$userName}</span> 
                                    has invited you to join your team, 
                                    <span class="notification-black">{$teamName}</span>.
                                </span>
                                HTML,
                            'subject' => 'Requesting to join this team',
                        ];
                    }
                    break;
            }
           
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
