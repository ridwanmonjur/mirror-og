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
use Illuminate\Support\Facades\DB;
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
                    <button class="btn-transparent px-0 border-0 notification-entity" data-href="/view/team/{$selectTeam->id}" alt="Team Join Request link">
                        {$selectTeam->teamName}</button>
                    has invited you to join them.
                </span>
            HTML;
            $teamNotification = <<<HTML
                <span class="notification-gray">
                    <button class="btn-transparent px-0 border-0 notification-entity" data-href="/view/participant/{$user->id}" alt="Team Join Request link">
                        {$user->name}</button>
                    has requested to join your team, 
                    <button class="btn-transparent px-0 border-0 notification-entity" data-href="/view/team/{$selectTeam->id}" alt="Team Join Request link">
                        {$selectTeam->teamName}</button>.
                </span>
            HTML;
        } else {
            $userNotification = <<<HTML
                <span class="notification-gray">
                    You have requested to join the team, <button class="btn-transparent px-0 border-0 notification-entity" data-href="/view/team/{$selectTeam->id}" alt="Team Join Request link">
                        {$selectTeam->teamName}</button>.
                </span>
            HTML;
            $teamNotification = <<<HTML
                <span class="notification-gray">
                    The user 
                    <button class="btn-transparent px-0 border-0 notification-entity" data-href="/view/participant/{$user->id}" alt="Team Join Request link">
                        {$user->name}</button>
                    has requested to join your team, 
                    <button class="btn-transparent px-0 border-0 notification-entity" data-href="/view/team/{$selectTeam->id}" alt="Team Join Request link">
                        {$selectTeam->teamName}</button>.
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
                'img_src' => $user->userBanner,
                'created_at' => DB::raw('NOW()')
            ];
        }

        $userNotification = [
            'user_id' => $user->id,
            'type' => 'teams',
            'html' => $userNotification,
            'link' => route('public.team.view', $selectTeam->id),
            'img_src' => $selectTeam->teamBanner,
            'created_at' => DB::raw('NOW()')
        ];

        NotifcationsUser::insertWithCount([...$memberNotification, $userNotification]);
           
        
    }

    public function failed($event, $exception): void
    {
          // Log the error
          Log::error('TeamCreatedListener failed', [
            'exception' => $exception->getMessage(),
            'team_member_id' => $this->teamMember->id ?? null,
            'stack_trace' => $exception->getTraceAsString()
        ]);
    }
}
