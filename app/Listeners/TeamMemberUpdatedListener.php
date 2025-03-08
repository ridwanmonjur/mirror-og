<?php

namespace App\Listeners;

use App\Events\TeamMemberUpdated;
use App\Models\ActivityLogs;
use App\Models\NotifcationsUser;
use App\Models\TeamMember;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Throwable;

class TeamMemberUpdatedListener implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $tries = 3; 
    
    public $teamMember;

    public function handle(TeamMemberUpdated $event)
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
        $status = $event->teamMember->status;
        $routeCreator = route('participant.team.manage', $selectTeam->id);
        $routeMember = route('public.participant.view', $user->id);
        $userLog = null;

        switch ($status) {
            case 'accepted':
                $action = 'accepted';

                $userLog = <<<HTML
                    <a class="px-0 border-0 notification-blue" href="/view/team/{$selectTeam->id}" alt="Team View">
                        <img src="/storage/{selectTeam->teamBanner}" 
                            width="30" height="30"    
                            onerror="this.src='/assets/images/404q.png';"
                            class="object-fit-cover rounded-circle me-2"
                            alt="Team View"
                        ></a>
                    <span class="notification-gray me-2">
                        You joined the team, 
                        <a class="px-0 border-0" href="/view/team/{$selectTeam->id}" alt="Team View">
                            <span class="notification-blue">{$selectTeam->teamName}</span></a>.
                    </span>
                HTML;

                if ($event->teamMember->actor == 'team') {
                    $userNotification =  <<<HTML
                        <span class="notification-gray me-2">
                        <button class="btn-transparent px-0 border-0 notification-entity" data-href="/view/team/{$selectTeam->id}" alt="Team View">
                            {$selectTeam->teamName}</button>
                            has accepted your join request.
                        </span>
                    HTML;

                    $teamNotification = <<<HTML
                        <span class="notification-gray">
                            <button class="btn-transparent px-0 border-0 notification-entity" data-href="/view/team/{$selectTeam->id}" alt="Team View">
                                {$user->name}</button>
                            has accepted the join request of your team,
                            <button class="btn-transparent px-0 border-0 notification-entity" data-href="/view/team/{$selectTeam->id}" alt="Team View">
                                {$selectTeam->teamName}</button>
                        </span>
                    HTML;
                } else {
                    $teamNotification = <<<HTML
                        <span class="notification-gray">
                            <button class="btn-transparent px-0 border-0 notification-entity" data-href="/view/team/{$selectTeam->id}" alt="Team View">
                                {$user->name}</button>
                            has joined your team, 
                            <button class="btn-transparent px-0 border-0 notification-entity" data-href="/view/team/{$selectTeam->id}" alt="Team View">
                                {$selectTeam->teamName}</button>
                        </span>
                    HTML;

                    $userNotification =  <<<HTML
                            <span class="notification-gray">
                            You have joined the team, 
                            <button class="btn-transparent px-0 border-0 notification-entity" data-href="/view/team/{$selectTeam->id}" alt="Team View">
                                {$selectTeam->teamName}</button>
                        </span>
                    HTML;
                }
                break;
            case 'left':
                $action = 'left';

                $userLog = <<<HTML
                    <button class="btn-transparent px-0 border-0 notification-blue" data-href="/view/team/{$selectTeam->id}" alt="Team View">
                        <img src="/storage/{$selectTeam->teamBanner}" 
                            width="30" height="30"
                            onerror="this.src='/assets/images/404.png';"
                            class="object-fit-cover rounded-circle me-2"
                            alt="Team View"
                        >
                    </button>
                    <span class="notification-gray">
                        You left the team, 
                        <a class="btn-transparent px-0 border-0" href="/view/team/{$selectTeam->id}" alt="Team View">
                        <span class="notification-blue">{$selectTeam->teamName}</span></a>.
                    </span>
                HTML;


                if ($event->teamMember->actor == 'team') {
                    
                    $userNotification = <<<HTML
                        <span class="notification-gray">
                            You have been removed from the team, 
                            <button class="btn-transparent px-0 border-0 notification-entity" data-href="/view/team/{$selectTeam->id}" alt="Team View">
                                {$selectTeam->teamName}</button>.
                        </span>
                        HTML;
                    
                    $teamNotification = <<<HTML
                    <span class="notification-gray">
                        The user, 
                        <button class="btn-transparent px-0 border-0 notification-entity" data-href="/view/participant/{$user->id}" alt="User link"> 
                            {$user->name} </button> 
                        has been removed from your team, 
                        <button class="btn-transparent px-0 border-0 notification-entity" data-href="/view/team/{$selectTeam->id}" alt="Team View">
                            {$selectTeam->teamName}</button>.
                    </span>
                    HTML;
                } else {
                    $teamNotification = <<<HTML
                        <span class="notification-gray">
                            You have left the team, 
                            <button class="btn-transparent px-0 border-0 notification-entity" data-href="/view/team/{$selectTeam->id}" alt="Team View">
                                {$selectTeam->teamName}</button>.
                        </span>
                    HTML;
                    $teamNotification = <<<HTML
                        <span class="notification-gray">
                            The user, 
                            <button class="btn-transparent px-0 border-0 notification-entity" data-href="/view/participant/{$user->id}" 
                                alt="User link"
                            > {$user->name} </button>
                            has left your team, 
                            <button class="btn-transparent px-0 border-0 notification-entity" data-href="/view/team/{$selectTeam->id}" alt="Team View">
                                {$selectTeam->teamName}</button>.
                        </span>
                    HTML;
                }
                break;
            case 'rejected':
                $action = 'rejected';
                if ($event->teamMember->actor == 'team') {
                    $userNotification = <<<HTML
                        <span class="notification-gray">
                            <button class="btn-transparent px-0 border-0 notification-entity" data-href="/view/team/{$selectTeam->id}" alt="Team View">
                                {$selectTeam->teamName}</button>
                            has rejected your request to join this team!
                        </span>
                        HTML;
                    
                    $teamNotification = <<<HTML
                        <span class="notification-gray">
                            <button class="btn-transparent px-0 border-0 notification-entity" data-href="/view/team/{$selectTeam->id}" alt="Team View">
                                {$selectTeam->teamName}</button>
                            has rejected the user, 
                            <button class="btn-transparent px-0 border-0 notification-entity" data-href="/view/participant/{$user->id}" 
                                alt="User link"
                            > 
                            {$user->name}</button>
                            to join.
                        </span>
                        HTML;
                } else {
                    $teamNotification = <<<HTML
                        <span class="notification-gray">
                            The user, 
                            <button class="btn-transparent px-0 border-0 notification-entity" data-href="/view/participant/{$user->id}" 
                                alt="User link"
                            > 
                            {$user->name}</button>
                            has rejected the invitation to your team, 
                            <button class="btn-transparent px-0 border-0 notification-entity " data-href="/view/team/{$selectTeam->id}" alt="Team View">
                                {$selectTeam->teamName}</button>.
                        </span>
                        HTML;

                    $userNotification = <<<HTML
                        <span class="notification-gray">
                            You have rejected the invitation join the team, 
                            <button class="btn-transparent px-0 border-0 notification-entity" data-href="/view/team/{$selectTeam->id}" alt="Team View">
                                {$selectTeam->teamName}</button>.
                        </span>
                        HTML;
                }
                break;
        }

        if ($userLog) {
            ActivityLogs::create([
                'action' => $action,
                'subject_id' => $user->id,
                'subject_type' => User::class,
                'object_id' => $event->teamMember->id,
                'object_type' => TeamMember::class,
                'log' => $userLog,
            ]);
        }

        $memberNotification = [];
        foreach ($selectTeam->members as $member) {
            if ($member->user->id == $user->id) continue;
            $route = $member->user->id == $selectTeam->creator_id ? $routeCreator: $routeMember;

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

    
    public function failed(Throwable $exception): void
    {
        // Log the error
        Log::error('TeamUpdatedListener failed', [
            'exception' => $exception->getMessage(),
            'team_member_id' => $this->teamMember->id ?? null,
            'stack_trace' => $exception->getTraceAsString()
        ]);
    }
}
