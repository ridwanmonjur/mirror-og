<?php

namespace App\Jobs;

use App\Mail\EventConfirmMail;
use App\Mail\VoteEndMail;
use App\Mail\VoteStartMail;
use App\Models\ActivityLogs;
use App\Models\NotifcationsUser;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ConfirmStrategy
{
    public function handle($parameters)
    {
        ['selectTeam' => $selectTeam, 'user' => $user, 'event' => $event, 
            'join_id' => $join_id, 'joinEvent' => $joinEvent 
        ] = $parameters;
      
        $teamMembers = $joinEvent->roster;
        $memberNotification = []; $memberMail = [];
        foreach ($teamMembers as $member) {
            $addressPartHTML = $user->id == $member->user->id 
                ? "You have" 
                : <<<HTML
            <button class="btn-transparent px-0 border-0 notification-entity" data-href="/view/user/{$user->id}">
                {$user->name}</button> has
            HTML;

            $html = <<<HTML
                <span class="notification-gray">
                    {$addressPartHTML} confirmed registration for  
                    <button class="btn-transparent px-0 border-0 notification-entity" data-href="/view/organizer/{$event->user->id}">
                        {$event->user->name}
                    </button>'s event,
                    <button class="btn-transparent px-0 border-0 Color-{$event->tier->eventTier}" data-href="/event/{$event->id}">
                        {$event->eventName}
                    </button>
                    with the team, 
                    <button class="btn-transparent px-0 border-0 notification-entity" data-href="/view/team/{$selectTeam->id}">
                        {$selectTeam->teamName}</button>. 
                </span>
            HTML;

            $htmlMail = <<<HTML
                <span class="notification-gray">
                    <span> <b>Hi, {$member->user->name}.</b></span> <br><br>
                    {$addressPartHTML} confirmed registration for  
                    <button class="btn-transparent px-0 border-0 notification-blue">
                        {$event->user->name}
                    </button>'s event,
                    <button class="btn-transparent px-0 border-0 notification-blue">
                        {$event->eventName}
                    </button>
                    for your team, 
                    <button class="btn-transparent px-0 border-0 notification-blue" >
                        {$selectTeam->teamName}
                    </button>. 
                </span>
            HTML;

            $memberNotification[] = [
                'user_id' => $member->user->id,
                'type' => 'teams',
                'link' =>  route('participant.register.manage', [
                    'id' => $selectTeam->id,
                    'scroll' => $join_id
                ]),
                'icon_type' => 'confirm',
                'html' => $html,
                'created_at' => DB::raw('NOW()')
            ];

            if ($member->user->email) {
                $memberMail[] = $member->user->email;
            } 

            $addressPart2Log = $member->user->id == $user->id 
                ? 'You' 
                : <<<HTML
            <a href="/view/user/{$member->user->id}" class="notification-blue">
                <span>{$member->user->name}</span>
            </a>
            HTML;

            $allEventLogs[] = [
                'action' => 'confirm',
                'created_at' => now(),
                'updated_at' => now(),
                'subject_id' => $member->user->id,
                'subject_type' => User::class,
                'log' => <<<HTML
                    <a class="btn-transparent px-0 border-0" href="/view/team/{$selectTeam->id}">
                        <img src="/storage/{$selectTeam->teamBanner}"
                            width="30" height="30" 
                            onerror="this.src='/assets/images/404.png';"
                            class="object-fit-cover rounded-circle me-2"
                            alt="Team banner for {$selectTeam->teamName}"></a>
                    <span class="notification-gray">
                        {$addressPart2Log} confirmed registration for 
                        <a class="btn-transparent px-0 border-0 " href="/view/organizer/{$event->user->id}">
                            <span class="notification-blue">{$event->user->name}</span></a>'s event,
                        <a class="btn-transparent px-0 border-0" href="/event/{$event->id}">
                        <span class="notification-blue">{$event->eventName}</span></a> with <a class="btn-transparent px-0 border-0" href="/view/team/{$selectTeam->id}">
                            <span class="notification-blue">{$selectTeam->teamName}</span></a>. 
                    </span>
                HTML,
            ];
        }

        Mail::to($memberMail)->send(new EventConfirmMail([
            'team' => $selectTeam,
            'text' => $htmlMail,
            'link' =>  route('participant.register.manage', [
                'id' => $selectTeam->id,
                'scroll' => $join_id
            ]),
        ]));
       
        
        ActivityLogs::insert($allEventLogs);
        NotifcationsUser::insertWithCount($memberNotification);
    }
}

class VoteStartStrategy
{
    public function handle($parameters)
    {
        [   
            'selectTeam' => $selectTeam, 
            'user' => $user, 
            'event' => $event,  
            'join_id' => $join_id,
            'joinEvent' => $joinEvent,
        ] = $parameters;

        $teamMembers = $joinEvent->roster;
        $memberNotification = []; $memberMail = [];
        foreach ($teamMembers as $member) {
            $addressPart = $user->id == $member->user->id ? 'You have' : $user->name . ' has';
            $htmlNotif = <<<HTML
                <span class="notification-gray">
                    A vote to <span class="notification-danger" >QUIT</span> the 
                    event, <button class="btn-transparent px-0 border-0 Color-{$event->tier->eventTier}" data-href="/event/{$event->id}">
                        {$event->eventName}
                    </button>
                    has been started for your team, 
                    <button class="btn-transparent px-0 border-0 notification-entity" data-href="/view/team/{$selectTeam->id}">
                        {$selectTeam->teamName}
                    </button>. 
                </span>
            HTML;
            $htmlMail = <<<HTML
                <span class="notification-gray">
                    <span> <b>Hi, {$member->user->name}.</b></span> <br><br>
                    {$addressPart} have started a vote to <span class="notification-danger" >QUIT</span>  
                    <button class="btn-transparent px-0 border-0 notification-blue">
                        {$event->user->name}
                    </button>'s event,
                    <button class="btn-transparent px-0 border-0 notification-blue">
                        {$event->eventName}
                    </button>
                    for your team, 
                    <button class="btn-transparent px-0 border-0 notification-blue" >
                        {$selectTeam->teamName}
                    </button>. 
                </span>
            HTML;

            $addressPart = $user->id == $member->user->id ? 'You have' : $user->name . ' has';
            $memberNotification[] = [
                'user_id' => $member->user->id,
                'type' => 'teams',
                'link' =>  route('participant.register.manage', [
                    'id' => $selectTeam->id,
                    'scroll' => $join_id
                ]),
                'icon_type' => 'vote',
                'html' => $htmlNotif,
                'created_at' => DB::raw('NOW()')
            ];

            if ($member->user->email) {
                $memberMail[] = $member->user->email;
            }
        }

        Mail::to($memberMail)->send(new VoteStartMail([
            'team' => $selectTeam,
            'text' => $htmlMail,
            'link' =>  route('participant.register.manage', [
                'id' => $selectTeam->id,
                'scroll' => $join_id
            ]),
        ]));

        NotifcationsUser::insertWithCount($memberNotification);
    }
}

class OrgCancelStrategy{
    public function handle($parameters) {

    }
}


class VoteEndStrategy
{
    
    public function handle($parameters)
    {
        [
            'selectTeam' => $selectTeam, 
            'event' => $event, 
            'willQuit' => $willQuit,
            'discount' => $discountsByUserAndType,
            'join_id' => $join_id,
            'joinEvent' => $joinEvent,
        ] = $parameters;
        $teamMembers = $joinEvent->roster;
        $memberMail = [];
        $memberNotification = [];

        if ($willQuit) {
            foreach ($teamMembers as $member) {
                if ($joinEvent->status == 'confirmed') {
                    $htmlMail = <<<HTML
                        <span class="notification-gray">
                            <b>Hi, {$member->user->name}.</b><br>
                            Your team, 
                            <button class="btn-transparent px-0 border-0 notification-blue" data-href="/view/team/{$selectTeam->id}">
                                {$selectTeam->teamName}
                            </button>, recently started a vote to quit for 
                            <button class="btn-transparent px-0 border-0 notification-blue" data-href="/event/{$event->id}">
                                {$event->eventName}
                            </button> by 
                            <button class="btn-transparent px-0 border-0 notification-blue" data-href="/view/organizer/{$event->user->id}">
                                {$event->user->name}
                            </button>.
                            Your team has voted to QUIT.
                            <br>Since your team has already confirmed its registration for this event, your entry fees WILL NOT be refunded.
                        </span>
                    HTML;
                } else {
                    $htmlMail = <<<HTML
                        <span class="notification-gray">
                            <b>Hi, {$member->user->name}.</b><br>
                            Your team, 
                            <button class="btn-transparent px-0 border-0 notification-blue" data-href="/view/team/{$selectTeam->id}">
                                {$selectTeam->teamName}
                            </button>, recently started a vote to quit for 
                            <button class="btn-transparent px-0 border-0 notification-blue" data-href="/event/{$event->id}">
                                {$event->eventName}
                            </button> by 
                            <button class="btn-transparent px-0 border-0 notification-blue" data-href="/view/organizer/{$event->user->id}">
                                {$event->user->name}
                            </button>.
                            Your team has voted to QUIT.
                            <br>Since your team has not confirmed its registration for this event, any entry fees paid will be returned in full to the respective players within 5 business days.
                        </span>
                    HTML;
                }

            
                $htmlNotif = <<<HTML
                    <span class="notification-gray">
                        <button class="btn-transparent px-0 border-0 notification-entity" data-href="/view/team/{$selectTeam->id}">
                            {$selectTeam->teamName}</button> 
                        has voted to <span class="notification-danger">QUIT</span> in the
                        <button class="btn-transparent px-0 border-0 Color-{$event->tier->eventTier}" data-href="/event/{$event->id}">
                            {$event->eventName}</button>.
                    </span>
                HTML;
                
                $memberNotification[] = [
                    'user_id' => $member->user->id,
                    'type' => 'teams',
                    'link' =>  route('participant.register.manage', [
                        'id' => $selectTeam->id,
                        'scroll' => $join_id
                    ]),
                    'icon_type' => 'quit',
                    'html' => $htmlNotif,
                    'created_at' => DB::raw('NOW()')
                ];

                if ($member->user->email) {
                    $memberMail[] = $member->user->email;
                }


                $joinEvent->vote_ongoing = false;
                $joinEvent->join_status = "canceled";

                $joinEvent->save();
            }

            Mail::to($memberMail)->send(new VoteEndMail([
                'team' => $selectTeam,
                'text' => $htmlMail,
                'link' =>  route('participant.register.manage', [
                    'id' => $selectTeam->id,
                    'scroll' => $join_id
                ]),
            ]));

            $htmlMail = <<<HTML
                <span class="notification-gray">
                    <b>Hi, {$member->user->name}.</b><br>
                    <button class="btn-transparent px-0 border-0 notification-blue" data-href="/view/team/{$selectTeam->id}">
                        <span class="notification-blue">{$selectTeam->teamName}</span> 
                    </button>
                    has voted to cancel registration for your event,
                    <button class="btn-transparent px-0 border-0 notification-blue" data-href="/event/{$event->id}">
                        {$event->eventName}</button>.
                    They will be refunded half of their fees.
                </span>
            HTML;

            $htmlNotif = <<<HTML
                <span class="notification-gray">
                    <button class="btn-transparent px-0 border-0 notification-entity" data-href="/view/team/{$selectTeam->id}">
                        {$selectTeam->teamName}</button> 
                    has voted to <span class="notification-danger">QUIT</span> in the
                    <button class="btn-transparent px-0 border-0 Color-{$event->tier->eventTier}" data-href="/event/{$event->id}">
                        {$event->eventName}</button>.
                </span>
            HTML;

            NotifcationsUser::insertWithCount($memberNotification);

            $rosterHistoryData = $teamMembers->map(function ($member) {
                return [
                    'user_id' => $member->user_id,
                    'join_events_id' => $member->join_events_id,
                    'team_member_id' => $member->team_member_id,
                    'team_id' => $member->team_id,
                    'vote_to_quit' => $member->vote_to_quit,
                    'created_at' => $member->created_at,
                    'updated_at' => now(),
                ];
            })->toArray();
        
            DB::table('roster_history')->insert($rosterHistoryData);
        
            $memberIds = $teamMembers->pluck('id')->toArray();
        
            DB::table('roster_members')->whereIn('id', $memberIds)->delete();
        } else {
            $htmlNotif = <<<HTML
                <span class="notification-gray">
                    <button class="btn-transparent px-0 border-0 notification-entity" data-href="/view/team/{$selectTeam->id}">
                        {$selectTeam->teamName}</button> 
                    has voted to <span class="notification-danger">QUIT</span> in the
                    <button class="btn-transparent px-0 border-0 Color-{$event->tier->eventTier}" data-href="/event/{$event->id}">
                        {$event->eventName}</button>.
                    They will be refunded half of their fees.
                </span>
            HTML;

            foreach ($teamMembers as $member) {
                $htmlMail = <<<HTML
                    <span class="notification-gray">
                        <b>Hi, {$member->user->name}.</b><br>
                        You have taken part in a vote for participating 
                        <button class="btn-transparent px-0 border-0 notification-blue" data-href="/view/organizer/{$event->user->id}">
                            {$event->user->name}
                        </button>'s event,
                        <button class="btn-transparent px-0 border-0 notification-blue" data-href="/event/{$event->id}">
                            {$event->eventName}
                        </button>
                        with your team, 
                        <button class="btn-transparent px-0 border-0 notification-blue" data-href="/view/team/{$selectTeam->id}">
                            {$selectTeam->teamName}</button>. 
                        Your team has voted to <span class="notification-blue">STAY</span>. Please complete the remaining registration to book your place, if your team hasn't done so.
                    </span>
                HTML;
                
                $memberNotification[] = [
                    'user_id' => $member->user->id,
                    'type' => 'teams',
                    'link' =>  route('participant.register.manage', [
                        'id' => $selectTeam->id,
                        'scroll' => $join_id
                    ]),
                    'icon_type' => 'stay',
                    'html' => $htmlNotif,
                    'created_at' => DB::raw('NOW()')
                ];

                if ($member->user->email) {
                    $memberMail[] = $member->user->email;
                }
            }

            Mail::to($memberMail)->send(new VoteEndMail([
                'team' => $selectTeam,
                'text' => $htmlMail,
                'link' =>  route('participant.register.manage', [
                    'id' => $selectTeam->id,
                    'scroll' => $join_id
                ]),
            ]));

            $joinEvent->vote_ongoing = false;
            $joinEvent->save();

            NotifcationsUser::insertWithCount($memberNotification);
        }
    }
}


class HandleEventJoinConfirm implements ShouldQueue
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
