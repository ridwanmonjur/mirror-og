<?php

namespace App\Listeners;

use App\Events\TeamMemberUpdated;
use App\Models\ActivityLogs;
use App\Models\Notifications;
use App\Models\TeamMember;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;

class TeamMemberUpdatedListener
{
    public $teamMember;

    public function handle(TeamMemberUpdated $event)
    {
        try {
            $event->teamMember->load([
                'team:id,teamName,creator_id',  
                'user:id,name,userBanner'  
            ]);

            $teamName = $event->teamMember->team->teamName;
            $teamId = $event->teamMember->team->id;
            $teamBanner = $event->teamMember->teamBanner;
            $user = $event->teamMember->user;
            $userName = $user->name;
            $userId = $user->id;
            $userBanner = $user->userBanner;
            $status = $event->teamMember->status;
            $teamCreatorNotification = $userNotification = $action = $userLog = null;
            $links = [];
            $hostname = config('app.url');
            $routeName = "{$hostname}participant/team/{$event->teamMember->team_id}/manage";
            
            $links = [
                ['name' => 'View Team', 'url' => $routeName],
            ];

            switch ($status) {
                case 'accepted':
                    if ($event->teamMember->actor === 'team') {
                        $action = 'accepted';
                        $userLog = <<<HTML
                            <a href="/view/team/$teamId" alt="Team View">
                                <img src="/storage/$teamBanner" 
                                    onerror="this.src='/assets/images/404.png';"
                                    class="object-fit-cover rounded-circle me-2"
                                    width="30" height="30"
                                    alt="Team View"
                                >
                            </a>
                            <span class="notification-gray me-2">
                                You joined the team, 
                                <a href="/view/team/$teamId" alt="Team View">
                                    <span class="notification-blue">{$teamName}</span>
                                </a>.
                            </span>
                            HTML;

                        $userNotification = [
                            'text' => <<<HTML
                                <a href="/view/team/$teamId" alt="Team View">
                                    <img src="/storage/$teamBanner" 
                                        onerror="this.src='/assets/images/404.png';"
                                        class="object-fit-cover rounded-circle me-2"
                                        width="30" height="30"
                                        alt="Team View"
                                    >
                                </a>
                                <span class="notification-gray me-2">
                                <a href="/view/team/$teamId" alt="Team View">
                                    <span class="notification-blue">{$teamName}</span>
                                </a>
                                    has accepted you to join their team.
                                </span>
                                HTML,
                            'subject' => 'Successfully joined this team',
                        ];
                    } else {
                        $action = 'accepted';

                        $userLog = <<<HTML
                            <a href="/view/team/$teamId" alt="Team View">
                                <img src="/storage/$teamBanner" 
                                    width="30" height="30"    
                                    onerror="this.src='/assets/images/404.png';"
                                    class="object-fit-cover rounded-circle me-2"
                                    alt="Team View"
                                >
                            </a>
                            <span class="notification-gray">
                                You joined the team, 
                                <a href="/view/team/$teamId" alt="Team View">
                                    <span class="notification-blue">{$teamName}</span>
                                </a>.
                            </span>
                            HTML;
                        
                        $teamCreatorNotification = [
                            'text' => <<<HTML
                                <a href="/view/participant/$userId" 
                                    alt="User link"
                                > 
                                    <img class="object-fit-cover rounded-circle me-2" 
                                        width='30' height='30'  
                                        src="/storage/$userBanner" 
                                        onerror="this.src='/assets/images/404.png';"
                                    >
                                </a>
                                <span class="notification-gray">
                                    <a href="/view/team/$teamId" alt="Team View">
                                        <span class="notification-blue">{$userName}</span>
                                    </a>
                                    has joined your team 
                                    <a href="/view/team/$teamId" alt="Team View">
                                        <span class="notification-blue">{$teamName}</span>
                                    </a>
                                </span>
                                HTML,
                            'subject' => 'Invited member joining this team',
                        ];
                    }
                    break;
                case 'left':
                    $action = 'left';
                    if ($event->teamMember->actor === 'team') {
                        $userLog = <<<HTML
                            <a href="/view/team/$teamId" alt="Team View">
                                <img src="/storage/$teamBanner" 
                                    width="30" height="30"
                                    onerror="this.src='/assets/images/404.png';"
                                    class="object-fit-cover rounded-circle me-2"
                                    alt="Team View"
                                >
                            </a>
                            <span class="notification-gray">
                                You left the team, 
                                <a href="/view/team/$teamId" alt="Team View">
                                    <span class="notification-blue">{$teamName}</span>
                                </a>.
                            </span>
                            HTML;

                        $userNotification = [
                            'text' => <<<HTML
                                <a href="/view/team/$teamId" alt="Team View">
                                    <img src="/storage/$teamBanner" 
                                        width="30" height="30"
                                        onerror="this.src='/assets/images/404.png';"
                                        class="object-fit-cover rounded-circle me-2"
                                        alt="Team View"
                                    >
                                </a>
                                <span class="notification-gray">
                                    You have been removed from this team 
                                    <a href="/view/team/$teamId" alt="Team View">
                                        <span class="notification-blue">{$teamName}</span>
                                    </a>.
                                </span>
                                HTML,
                            'subject' => 'Removal from team',
                        ];
                    } else {
                        $userLog = <<<HTML
                            <a href="/view/team/$teamId" alt="Team View">
                                <img 
                                    width="30" height="30"
                                    src="/storage/$teamBanner" 
                                    onerror="this.src='/assets/images/404.png';"
                                    class="object-fit-cover rounded-circle me-2"
                                    alt="Team View"
                                >
                            </a>
                            <span class="notification-gray">
                                You left the team, 
                                <a href="/view/team/$teamId" alt="Team View">
                                    <span class="notification-blue">{$teamName}</span>
                                </a>.
                            </span>
                            HTML;

                        $teamCreatorNotification = [
                            'text' => <<<HTML
                                <a href="/view/participant/$userId" 
                                    alt="User link"
                                > 
                                    <img class="object-fit-cover rounded-circle me-2" 
                                        width='30' height='30'  
                                        src="/storage/$userBanner" 
                                        onerror="this.src='/assets/images/404.png';"
                                    >
                                </a>
                                <span class="notification-gray">
                                    The user, 
                                    <a href="/view/participant/$userId" 
                                        alt="User link"
                                    > 
                                    <span class="notification-blue">{$userName}</span> 
                                    </a>
                                    has left your team, 
                                    <a href="/view/team/$teamId" alt="Team View">
                                        <span class="notification-blue">{$teamName}</span>
                                    </a>.
                                </span>
                                HTML,
                            'subject' => 'Leaving the team',
                        ];
                    }
                    break;
                case 'rejected':
                    $action = 'rejected';
                    if ($event->teamMember->actor === 'team') {
                        $userNotification = [
                        'text' => <<<HTML
                            <a href="/view/team/$teamId" alt="Team View">
                                <img src="/storage/$teamBanner" 
                                    width="30" height="30"
                                    onerror="this.src='/assets/images/404.png';"
                                    class="object-fit-cover rounded-circle me-2"
                                    alt="Team View"
                                >
                            </a>
                            <span class="notification-gray">
                                <a href="/view/team/$teamId" alt="Team View">
                                    <span class="notification-blue">{$teamName}</span>
                                </a>
                                has rejected your request to join this team!
                            </span>
                            HTML,
                            'subject' => 'Failed to join this team',
                        ];
                    } else {
                        $teamCreatorNotification = [
                            'text' => <<<HTML
                                <a href="/view/participant/$userId" 
                                    alt="User link"
                                > 
                                    <img 
                                        class="object-fit-cover rounded-circle me-2" 
                                        width='30' height='30'  
                                        src="/storage/$userBanner" 
                                        onerror="this.src='/assets/images/404.png';"
                                    >
                                </a>
                                <span class="notification-gray">
                                    The user, 
                                    <a href="/view/participant/$userId" 
                                        alt="User link"
                                    > 
                                    <span class="notification-black">{$userName}</span> 
                                    </a>
                                    has rejected the invitation to your team, 
                                    <a href="/view/team/$teamId" alt="Team View">
                                        <span class="notification-blue">{$teamName}</span>
                                    </a>.
                                </span>
                                HTML,
                            'subject' => 'Failed to recruit into your team',
                        ];
                    }
                    break;
            }

            if ($userLog) {
                ActivityLogs::create([
                    'action' => $action,
                    'subject_id' => $userId,
                    'subject_type' => User::class,
                    'object_id' => $event->teamMember->id,
                    'object_type' => TeamMember::class,
                    'log' => $userLog,
                ]);
            }
            if ($teamCreatorNotification) {
                Notifications::create([
                    'id' => uuid_create(),
                    'data' => json_encode([
                        'data' => $teamCreatorNotification['text'],
                        'subject' => $teamCreatorNotification['subject'],
                        'links' => $links,
                    ]),
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
