<?php

namespace App\Listeners;

use App\Events\TeamMemberCreated;
use App\Models\Notifications;
use App\Models\TeamMember;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;

class TeamMemberCreatedListener
{
    public $teamMember;

    public function handle(TeamMemberCreated $event)
    {
        try {
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
                            'text' => '<span class="notification-gray"> The team'
                                .' <span class="notification-black">'.$teamName
                                .'</span> has invited you'
                                .' to join them.</span>',
                            'subject' => 'Invitation to join a team',
                        ];
                    } else {
                        $action = 'pending';
                        $teamCreatorNotification = [
                            'text' => '<span class="notification-gray"> The user'
                                .' <span class="notification-black">'.$userName.'</span> has invited you'
                                .' to join your team , '.$teamName
                                .'</span></span>',
                            'subject' => 'Requesting to join this team',
                        ];
                    }
                    break;
            }
            if ($teamCreatorNotification) {
                Notifications::create([
                    'data' => json_encode([
                        'data' => $teamCreatorNotification['text'],
                        'subject' => $teamCreatorNotification['subject'],
                        'links' => $links,
                    ]),
                    'id' => uuid_create(),
                    'type' => Notifications::class,
                    'notifiable_id' => $event->teamMember->team->creator_id,
                    'notifiable_type' => User::class,
                    'object_id' => $event->teamMember->id,
                    'object_type' => TeamMember::class,
                ]);
            }
            if ($userNotification) {
                Notifications::create([
                    'data' => json_encode([
                        'data' => $userNotification['text'],
                        'subject' => $userNotification['subject'],
                        'links' => $links,
                    ]),
                    'id' => uuid_create(),
                    'type' => Notifications::class,
                    'notifiable_id' => $userId,
                    'notifiable_type' => User::class,
                    'object_id' => $event->teamMember->id,
                    'object_type' => TeamMember::class,
                ]);
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
