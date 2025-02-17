<?php

namespace App\Listeners;

use App\Events\TeamMemberCreated;
use App\Models\NotifcationsUser;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TeamMemberCreatedListener implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $teamMember;

    public function handle(TeamMemberCreated $event)
    {
        $event->teamMember->load([
            'team:id,teamName,teamBanner,creator_id',  
            'user:id,name,userBanner',
            'team.members' => function($query) {
                $query->where('status', 'accepted');  // Fixed relationship and where clause
            }            
        ]);

        $selectTeam = $event->teamMember->team;
        $user = $event->teamMember->user;

        if ($event->teamMember->actor === 'team') {
            $userNotification = <<<HTML
                <span class="notification-gray">
                    The team, 
                    <a href="/view/team/{$selectTeam->id}" alt="Team Join Request link">
                        <span class="notification-blue">{$selectTeam->teamName}</span></a>
                    has invited you to join them.
                </span>
            HTML;
            $teamNotification = <<<HTML
                <span class="notification-gray">
                    <a href="/view/participant/{$user->id}" alt="Team Join Request link">
                        <span class="notification-blue">{$user->name}</span></a>
                    has requested to join your team, 
                    <a href="/view/team/{$selectTeam->id}" alt="Team Join Request link">
                        <span class="notification-blue">{$selectTeam->teamName}</span></a>.
                </span>
            HTML;
        } else {
            $userNotification = <<<HTML
                <span class="notification-gray">
                    You have requested to join the team, <a href="/view/team/{$selectTeam->id}" alt="Team Join Request link">
                        <span class="notification-blue">{$selectTeam->teamName}</span></a>.
                </span>
            HTML;
            $teamNotification = <<<HTML
                <span class="notification-gray">
                    The user 
                    <a href="/view/participant/{$user->id}" alt="Team Join Request link">
                        <span class="notification-blue">{$user->name}</span></a>
                    has requested to join your team, 
                    <a href="/view/team/{$selectTeam->id}" alt="Team Join Request link">
                        <span class="notification-blue">{$selectTeam->teamName}</span></a>.
                </span>
            HTML;
        }

        $memberNotification = [];
        foreach ($selectTeam->members as $member) {
            $route = $member->user->id == $selectTeam->creator_id ? 
                route('participant.team.manage', $selectTeam->id):
                route('public.participant.view', $user->id);

            $memberNotification[] = [
                'user_id' => $member->user->id,
                'type' => 'teams',
                'html' => $teamNotification,
                'link' => $route,
                'img_src' => $user->userBanner
            ];
        }

        $userNotification = [
            'user_id' => $user->id,
            'type' => 'teams',
            'html' => $userNotification,
            'link' => route('public.team.view', $selectTeam->id),
            'img_src' => $selectTeam->teamBanner
        ];

        NotifcationsUser::insertWithCount([...$memberNotification, $userNotification]);
           
        
    }

    public function failed(Exception $exception): void
    {
          // Log the error
          Log::error('TeamCreatedListener failed', [
            'exception' => $exception->getMessage(),
            'team_member_id' => $this->teamMember->id ?? null,
            'stack_trace' => $exception->getTraceAsString()
        ]);
    }
}
