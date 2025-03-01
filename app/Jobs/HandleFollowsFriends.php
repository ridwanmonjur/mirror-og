<?php

namespace App\Jobs;

use App\Models\ActivityLogs;
use App\Models\NotifcationsUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FollowOrgStrategy
{
    public function handle($parameters)
    {
        ActivityLogs::create($parameters);
        ['organizer' => $organizer, 'participant' => $participant] = $parameters;

        NotifcationsUser::create([
            'user_id' => $organizer->id,
            'type' => 'social',
            'html' => <<<HTML
                <span class="notification-gray me-2">
                    <button class="btn-transparent px-0 border-0 notification-blue" data-href="/view/participant/{$participant->id}" alt="Follow link">
                        {$participant->name}
                    </button> is now following you.
                </span>
            HTML,
            'link' => route('public.participant.view', $participant->id),
            'img_src' => $participant->userBanner
        ]);
       
        NotifcationsUser::create([
            'user_id' => $participant->id,
            'type' => 'social',
            'html' => <<<HTML
                <span class="notification-gray me-2">
                    You started following an organizer,
                    <button class="btn-transparent px-0 border-0 notification-blue" data-href="/view/participant/{$organizer->id}" alt="Follow link">
                        {$organizer->name}.
                    </button>
                </span>
            HTML,
            'link' => route('public.organizer.view', $organizer->id),
            'img_src' => $organizer->userBanner
        ]);
    }
}



class FollowParticipantStrategy
{
    public function handle($parameters)
    {
        ['followee' => $followee, 'user' => $user] = $parameters;

        NotifcationsUser::create([
            'user_id' => $followee->id,
            'type' => 'social',
            'html' => <<<HTML
                <span class="notification-gray me-2">
                    <button class="btn-transparent px-0 border-0 notification-blue" data-href="/view/participant/{$user->id}" alt="Follow link">
                        {$user->name}
                    </button> is now following you.
                </span>
            HTML,
            'link' => route('public.participant.view', $user->id),
            'img_src' => $user->userBanner
        ]);
       
        NotifcationsUser::create([
            'user_id' => $user->id,
            'type' => 'social',
            'html' => <<<HTML
                <span class="notification-gray me-2">
                    You started following another player,
                    <button class="btn-transparent px-0 border-0 notification-blue" data-href="/view/participant/{$followee->id}" alt="Follow link">
                        {$followee->name}.
                    </button>
                </span>
            HTML,
            'link' => route('public.organizer.view', $followee->id),
            'img_src' => $followee->userBanner
        ]);
    }
}


class UnfollowOrgStrategy
{
    public function handle($parameters)
    {
        ActivityLogs::where($parameters)->delete();
    }
}

class NewFriendStrategy
{
    public function handle($parameters)
    {
        ['user' => $user, 'otherUser' => $otherUser] = $parameters;

        NotifcationsUser::create([
            'user_id' => $otherUser->id,
            'type' => 'social',
            'html' => <<<HTML
                <span class="notification-gray me-2">
                    <button class="btn-transparent px-0 border-0 notification-blue" data-href="/view/participant/{$user->id}" alt="Friend Request link">
                        {$user->name}</button> has send you a friend request.
                </span>
            HTML,
            'link' => route('public.participant.view', $user->id),
            'img_src' => $user->userBanner
        ]);
    }
}

class UpdateFriendStrategy
{
    public function handle($parameters)
    {
        ['user' => $user, 'otherUser' => $otherUser, 'status' => $status] = $parameters;

        if ($status == "accepted") {
            NotifcationsUser::create([
                'user_id' => $otherUser->id,
                'type' => 'social',
                'html' => <<<HTML
                    <span class="notification-gray me-2">
                        <button class="btn-transparent px-0 border-0 notification-blue" data-href="/view/participant/{$user->id}" alt="Friend Accept link">
                            {$user->name}
                        </button>and you are now friends. > Click to go to <button class="btn-transparent px-0 border-0 notification-blue" data-href="/view/participant/{$user->id}" alt="Friend Accept link">
                            {$user->name}
                        </button>'s profile.
                    </span>
                HTML,
                'link' => route('public.participant.view', $user->id),
                'img_src' => $user->userBanner
            ]);

            NotifcationsUser::create([
                'user_id' => $user->id,
                'type' => 'social',
                'html' => <<<HTML
                    <span class="notification-gray me-2">
                        <button class="btn-transparent px-0 border-0 notification-blue" data-href="/view/participant/{$otherUser->id}" alt="Friend Accept link">
                            {$otherUser->name}
                        </button>and you are now friends. > Click to go to <button class="btn-transparent px-0 border-0 notification-blue" data-href="/view/participant/{$otherUser->id}" alt="Friend Accept link">
                            {$otherUser->name}
                        </button>'s profile.
                    </span>
                HTML,
                'link' => route('public.participant.view', $otherUser->id),
                'img_src' => $otherUser->userBanner
            ]);
        }

        if ($status == "left") {
            NotifcationsUser::create([
                'user_id' => $otherUser->id,
                'type' => 'social',
                'html' => <<<HTML
                    <span class="notification-gray me-2">
                        <button class="btn-transparent px-0 border-0 notification-blue" data-href="/view/participant/{$user->id}" alt="Friend Accept link">
                            {$user->name}
                        </button>and you are no longer friends. > Click to go to <button class="btn-transparent px-0 border-0 notification-blue" data-href="/view/participant/{$user->id}" alt="Friend Accept link">
                            {$user->name}
                        </button>'s profile.
                    </span>
                HTML,
                'link' => route('public.participant.view', $user->id),
                'img_src' => $user->userBanner
            ]);

            NotifcationsUser::create([
                'user_id' => $user->id,
                'type' => 'social',
                'html' => <<<HTML
                    <span class="notification-gray me-2">
                        <button class="btn-transparent px-0 border-0 notification-blue" data-href="/view/participant/{$otherUser->id}" alt="Friend Accept link">
                            {$otherUser->name}
                        </button>and you are no longer friends. > Click to go to <button class="btn-transparent px-0 border-0 notification-blue" data-href="/view/participant/{$otherUser->id}" alt="Friend Accept link">
                            {$otherUser->name}
                        </button>'s profile.
                    </span>
                HTML,
                'link' => route('public.participant.view', $otherUser->id),
                'img_src' => $otherUser->userBanner
            ]);
        }
    }
}

class FollowTeamStrategy
{
    public function handle($parameters)
    {   
        ['user' => $user, 'team' => $selectTeam, 'isFollow' => $isFollow] = $parameters;

        if ($isFollow) {
            foreach ($selectTeam->members as $member) {
                $html = <<<HTML
                    <span class="notification-gray">
                        <button class="btn-transparent px-0 border-0 notification-blue" data-href="/view/participant/{$user->id}" alt="Team Follower link">
                            {$user->name}</button>
                        followed your team, 
                        <button class="btn-transparent px-0 border-0 notification-blue" data-href="/view/team/{$selectTeam->id}">
                            {$selectTeam->teamName}</button>. 
                    </span>
                HTML;

                $memberNotification[] = [
                    'user_id' => $member->user->id,
                    'type' => 'teams',
                    'link' =>  route('public.team.view', ['id' => $selectTeam->id]),
                    'icon_type' => 'follow',
                    'html' => $html,
                ];
            }

            NotifcationsUser::create([
                'user_id' => $user->id,
                'type' => 'teams',
                'html' => <<<HTML
                    <span class="notification-gray me-2">
                        You followed a team, 
                        <button class="btn-transparent px-0 border-0 notification-blue" data-href="/view/team/{$selectTeam->id}">
                            {$selectTeam->teamName}</button>. 
                    </span>
                HTML,
                'link' => route('public.team.view', $selectTeam->id),
                'icon_type' => 'follow'
            ]);
        }

        NotifcationsUser::insertWithCount($memberNotification);
    }
}

class HandleFollowsFriends implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $strategy;

    protected $parameters;

    public function __construct($strategy, $parameters)
    {
        $this->strategy = $strategy;
        $this->parameters = $parameters;
    }

    // Simple Strategy
    public function handle()
    {
        $strategyClass = __NAMESPACE__.'\\'.$this->strategy.'Strategy';

        if (! class_exists($strategyClass)) {
            throw new \InvalidArgumentException("Strategy class {$strategyClass} does not exist.");
        }
        $strategy = new $strategyClass();
        $strategy->handle($this->parameters);
    }
}
