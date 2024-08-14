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
            $teamName = $event->teamMember->team->teamName;
            $user = $event->teamMember->user;
            $userName = $user->name;
            $photo = $user->userBanner;
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
                case 'accepted':
                    if ($event->teamMember->actor === 'team') {
                        $action = 'accepted';
                        $userLog = <<<HTML
                            <span class="notification-gray">
                                You joined the team, 
                                <span class="notification-black">{$teamName}</span>.
                            </span>
                            HTML;

                        $userNotification = [
                            'text' => <<<HTML
                                <span class="notification-gray">
                                    <span class="notification-black">{$teamName}</span> 
                                    has accepted you to join their team.
                                </span>
                                HTML,
                            'subject' => 'Successfully joined this team',
                        ];
                    } else {
                        $action = 'accepted';

                        $userLog = <<<HTML
                            <span class="notification-gray">
                                You joined the team, 
                                <span class="notification-black">{$teamName}</span>.
                            </span>
                            HTML;
                        
                        $teamCreatorNotification = [
                            'text' => <<<HTML
                                <span class="notification-gray">
                                    <span class="notification-black">{$userName}</span> 
                                    has joined your team 
                                    <span class="notification-black">{$teamName}</span>.
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
                            <span class="notification-gray">
                                You left the team, 
                                <span class="notification-black">{$teamName}</span>.
                            </span>
                            HTML;

                        $userNotification = [
                            'text' => <<<HTML
                                <span class="notification-gray">
                                    You have been removed from this team 
                                    <span class="notification-black">{$teamName}</span>.
                                </span>
                                HTML,
                            'subject' => 'Removal from team',
                        ];
                    } else {
                        $userLog = <<<HTML
                            <span class="notification-gray">
                                You left the team, 
                                <span class="notification-black">{$teamName}</span>.
                            </span>
                            HTML;

                        $teamCreatorNotification = [
                            'text' => <<<HTML
                                <span class="notification-gray">
                                    The user, 
                                    <span class="notification-black">{$userName}</span> 
                                    has left your team, 
                                    <span class="notification-black">{$teamName}</span>.
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
                            <span class="notification-gray">
                                <span class="notification-black">{$teamName}</span> 
                                has rejected your request to join this team!
                            </span>
                            HTML,
                            'subject' => 'Failed to join this team',
                        ];
                    } else {
                        $teamCreatorNotification = [
                            'text' => <<<HTML
                                <span class="notification-gray">
                                    The user, 
                                    <span class="notification-black">{$userName}</span> 
                                    has rejected the invitation to your team, 
                                    <span class="notification-black">{$teamName}</span>.
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
