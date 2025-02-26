<?php

namespace App\Jobs;

use App\Mail\EventConfirmMail;
use App\Mail\VoteEndMail;
use App\Models\ActivityLogs;
use App\Models\NotifcationsUser;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ConfirmStrategy
{
    public function handle($parameters)
    {
        ['selectTeam' => $selectTeam, 'user' => $user, 'event' => $event] = $parameters;
        $teamMembers = $selectTeam->members;
        $memberNotification = [];
        foreach ($teamMembers as $member) {
            $addressPart = $user->id == $member->user->id ? 'You have' : $user->name . ' has';
            $html = <<<HTML
                <span class="notification-gray">
                    {$addressPart} confirmed registration for  
                    <button class="btn-transparent px-0 border-0 notification-blue" data-href="/view/organizer/{$event->user->id}">
                        {$event->user->name}
                    </button>'s event,
                    <button class="btn-transparent px-0 border-0 notification-blue" data-href="/event/{$event->id}">
                        {$event->eventName}
                    </button>
                    with the team, 
                    <button class="btn-transparent px-0 border-0 notification-blue" data-href="/view/team/{$selectTeam->id}">
                        {$selectTeam->teamName}</button>. 
                </span>
            HTML;

            $memberNotification[] = [
                'user_id' => $member->user->id,
                'type' => 'teams',
                'link' =>  route('participant.register.manage', ['id' => $selectTeam->id]),
                'icon_type' => 'confirm',
                'html' => $html,
            ];

            Mail::to($member->user->email)->send(new EventConfirmMail([
                'team' => $selectTeam,
                'text' => $html,
                'link' =>  route('participant.register.manage', ['id' => $selectTeam->id]),
            ]));

            $addressPart2 = $member->user->id == $user->id ? 'You': $member->user->name;
            $allEventLogs[] = [
                'action' => 'confirm',
                'created_at' => now(),
                'updated_at' => now(),
                'subject_id' => $member->user->id,
                'subject_type' => User::class,
                'log' => <<<HTML
                    <a class="btn-transparent px-0 border-0 notification-blue" href="/view/team/{$selectTeam->id}">
                        <img src="/storage/{$selectTeam->teamBanner}"
                            width="30" height="30" 
                            onerror="this.src='/assets/images/404.png';"
                            class="object-fit-cover rounded-circle me-2"
                            alt="Team banner for {$selectTeam->teamName}"></a>
                    <span class="notification-gray">
                        {$addressPart2} confirmed registration for 
                        <a class="btn-transparent px-0 border-0 notification-blue" href="/view/organizer/{$event->user->id}">
                            {$event->user->name}</a>'s event,
                        <a class="btn-transparent px-0 border-0 notification-blue" href="/event/{$event->id}">
                            {$event->eventName}</a> with <a class="btn-transparent px-0 border-0 notification-blue" href="/view/team/{$selectTeam->id}">
                            {$selectTeam->teamName}</a>. 
                    </span>
                HTML,
            ];
        }
            
        $organizerNotification = [
            'user_id' => $event->user->id,
            'type' => 'teams',
            'link' =>  route('participant.register.manage', ['id' => $selectTeam->id]),
            'icon_type' => 'confirm',
            'html' => <<<HTML
                <span class="notification-gray">
                    <a class="btn-transparent px-0 border-0 notification-blue" href="/view/team/{$selectTeam->id}">
                        <span class="notification-black">{$selectTeam->teamName}</span> </a>
                    has confirmed registration for your event,
                    <a class="btn-transparent px-0 border-0 notification-blue" href="/event/{$event->id}">
                        {$event->eventName}</a>.
                </span>
            HTML,
        ];

        Mail::to($event->user->email)->send(new EventConfirmMail([
            'team' => $selectTeam,
            'text' => $html,
            'link' =>  route('participant.register.manage', ['id' => $selectTeam->id]),
        ]));
        ActivityLogs::insert($allEventLogs);
        NotifcationsUser::insertWithCount([$organizerNotification, ...$memberNotification]);
    }
}

class VoteStartStrategy
{
    public function handle($parameters)
    {
        ['selectTeam' => $selectTeam, 'user' => $user, 'event' => $event] = $parameters;
        $teamMembers = $selectTeam->members;
        $memberNotification = [];
        foreach ($teamMembers as $member) {
            $addressPart = $user->id == $member->user->id ? 'You have' : $user->name . ' has';
            $htmlNotif = <<<HTML
                <span class="notification-gray">
                    A vote to quit
                    <button class="btn-transparent px-0 border-0 notification-blue" data-href="/event/{$event->id}">
                        {$event->eventName}
                    </button>
                    has been started for your team, 
                    <button class="btn-transparent px-0 border-0 notification-blue" data-href="/view/team/{$selectTeam->id}">
                        {$selectTeam->teamName}
                    </button>. 
                </span>
            HTML;
            $htmlMail = <<<HTML
                <span class="notification-gray">
                    {$addressPart} have started a vote to quit  
                    <button class="btn-transparent px-0 border-0 notification-blue" data-href="/view/organizer/{$event->user->id}">
                        {$event->user->name}
                    </button>'s event,
                    <button class="btn-transparent px-0 border-0 notification-blue" data-href="/event/{$event->id}">
                        {$event->eventName}
                    </button>
                    for your team, 
                    <button class="btn-transparent px-0 border-0 notification-blue" data-href="/view/team/{$selectTeam->id}">
                        {$selectTeam->teamName}
                    </button>. 
                </span>
            HTML;

            $addressPart = $user->id == $member->user->id ? 'You have' : $user->name . ' has';
            $memberNotification[] = [
                'user_id' => $member->user->id,
                'type' => 'teams',
                'link' =>  route('participant.register.manage', ['id' => $selectTeam->id]),
                'icon_type' => 'vote',
                'html' => $htmlNotif,
            ];

            Mail::to($member->user->email)->send(new EventConfirmMail([
                'team' => $selectTeam,
                'text' => $htmlMail,
                'link' =>  route('participant.register.manage', ['id' => $selectTeam->id]),
            ]));
        }

        NotifcationsUser::insertWithCount($memberNotification);
    }
}

class OrgCancel{
    public function handle($parameters) {

    }
}


class VoteEndStrategy
{
    public function handle($parameters)
    {
        ['selectTeam' => $selectTeam, 
            'user' => $user, 
            'event' => $event, 
            'willQuit' => $willQuit,
            'discount' => $discountsByUserAndType
        ] = $parameters;
        $teamMembers = $selectTeam->members;
        $memberNotification = [];

        if ($willQuit) {
            foreach ($teamMembers as $member) {
                $discount = isset($discountsByUserAndType[$member->user_id]) ? $discountsByUserAndType[$member->user_id] : null;
                $discountText = '';

                $issetReleasedAmount = isset($discount['released_amount']) && $discount['released_amount'] > 0;
                $issetCouponedAmount = isset($discount['couponed_amount']) && $discount['couponed_amount'] > 0;    
                if ( $issetReleasedAmount || $issetCouponedAmount ) {
                    $discountText = "You have been returned half of your contribution: ";
                    
                    if ($issetReleasedAmount) {
                        $discountText .= "RM {$discount['released_amount']} in bank refunds" ;
                    }
                    
                    if ($issetReleasedAmount && $issetCouponedAmount) {
                        $discountText .= " &";
                    }
                
                    if ($issetCouponedAmount) {
                        $discountText .= " RM {$discount['couponed_amount']} in coupons.";
                    }
                }

                $htmlMail = <<<HTML
                    <span class="notification-gray">
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
                        Your team has chosen to leave. {$discountText}
                    </span>
                HTML;

                $htmlNotif = <<<HTML
                    <span class="notification-gray">
                        <button class="btn-transparent px-0 border-0 notification-blue" data-href="/view/team/{$selectTeam->id}">
                            {$selectTeam->teamName}</button> 
                        has voted to QUIT in the
                        <button class="btn-transparent px-0 border-0 notification-blue" data-href="/event/{$event->id}">
                            {$event->eventName}</button>.
                    </span>
                HTML;
                
                $memberNotification[] = [
                    'user_id' => $member->user->id,
                    'type' => 'teams',
                    'link' =>  route('participant.register.manage', ['id' => $selectTeam->id]),
                    'icon_type' => 'quit',
                    'html' => $htmlNotif,
                ];

                Mail::to($member->user->email)->send(new VoteEndMail([
                    'team' => $selectTeam,
                    'text' => $htmlMail,
                    'link' =>  route('participant.register.manage', ['id' => $selectTeam->id]),
                ]));
            }

            $htmlMail = <<<HTML
                <span class="notification-gray">
                    <button class="btn-transparent px-0 border-0 notification-blue" data-href="/view/team/{$selectTeam->id}">
                        <span class="notification-black">{$selectTeam->teamName}</span> 
                    </button>
                    has voted to cancel registration for your event,
                    <button class="btn-transparent px-0 border-0 notification-blue" data-href="/event/{$event->id}">
                        {$event->eventName}</button>.
                    They will be refunded half of their fees.
                </span>
            HTML;

            $htmlNotif = <<<HTML
                <span class="notification-gray">
                    <button class="btn-transparent px-0 border-0 notification-blue" data-href="/view/team/{$selectTeam->id}">
                        {$selectTeam->teamName}</button> 
                    has voted to QUIT in the
                    <button class="btn-transparent px-0 border-0 notification-blue" data-href="/event/{$event->id}">
                        {$event->eventName}</button>.
                </span>
            HTML;

            $organizerNotification = [
                'user_id' => $event->user->id,
                'type' => 'teams',
                'link' =>  route('participant.register.manage', ['id' => $selectTeam->id]),
                'icon_type' => 'quit',
                'html' => $htmlNotif
            ];
            
            Mail::to($event->user->email)->send(new VoteEndMail([
                'team' => $selectTeam,
                'text' => $htmlMail,
                'link' =>  route('participant.register.manage', ['id' => $selectTeam->id]),
            ]));

            NotifcationsUser::insertWithCount([$organizerNotification, ...$memberNotification]);
        } else {
            $htmlNotif = <<<HTML
                <span class="notification-gray">
                    <button class="btn-transparent px-0 border-0 notification-blue" data-href="/view/team/{$selectTeam->id}">
                        {$selectTeam->teamName}</button> 
                    has voted to QUIT in the
                    <button class="btn-transparent px-0 border-0 notification-blue" data-href="/event/{$event->id}">
                        {$event->eventName}</button>.
                    They will be refunded half of their fees.
                </span>
            HTML;

            foreach ($teamMembers as $member) {
                $htmlMail = <<<HTML
                    <span class="notification-gray">
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
                        Your team has voted to stay. Please complete the remaining registration to book your place, if your team hasn't done so.
                    </span>
                HTML;
                
                $memberNotification[] = [
                    'user_id' => $member->user->id,
                    'type' => 'teams',
                    'link' =>  route('participant.register.manage', ['id' => $selectTeam->id]),
                    'icon_type' => 'stay',
                    'html' => $htmlNotif,
                ];

                Mail::to($member->user->email)->send(new VoteEndMail([
                    'team' => $selectTeam,
                    'text' => $htmlMail,
                    'link' =>  route('participant.register.manage', ['id' => $selectTeam->id]),
                ]));
            }

            NotifcationsUser::insertWithCount($memberNotification);
        }
    }
}

class JoinPlaceStrategy {
    public function handle ($parameters) {
        ['selectTeam' => $selectTeam, 'user' => $user, 'event' => $event] = $parameters;
        $teamMembers = $selectTeam->members;
        $allEventLogs = [];
        $memberNotification = [];
        foreach ($teamMembers as $member) {
            $memberNotification[] = [
                'user_id' => $member->user->id,
                'type' => 'teams',
                'link' =>  route('participant.register.manage', ['id' => $selectTeam->id]),
                'icon_type' => 'signup',
                'html' => <<<HTML
                    <span class="notification-gray">
                        You have been placed in
                        <button class="btn-transparent px-0 border-0 notification-blue" data-href="/view/organizer/{$event->user->id}">
                            {$event->user->name}
                        </button>'s event,
                        <button class="btn-transparent px-0 border-0 notification-blue" data-href="/event/{$event->id}">
                            {$event->eventName}
                        </button>
                        with <button class="btn-transparent px-0 border-0 notification-blue" data-href="/view/team/{$selectTeam->id}">
                            {$selectTeam->teamName}</button>. 
                            Please complete and confirm your registration for this event.
                    </span>
                HTML,
            ];

            $address = $member->user->id == $user->id ? 'You': $member->user->name;
            $allEventLogs[] = [
                'action' => 'signup',
                'created_at' => now(),
                'updated_at' => now(),
                'subject_id' => $member->user->id,
                'subject_type' => User::class,
                'log' => <<<HTML
                    <a class="btn-transparent px-0 border-0 notification-blue" href="/view/team/{$selectTeam->id}">
                        <img src="/storage/{$selectTeam->teamBanner}"
                            width="30" height="30" 
                            onerror="this.src='/assets/images/404.png';"
                            class="object-fit-cover rounded-circle me-2"
                            alt="Team banner for {$selectTeam->teamName}"></a>
                    <span class="notification-gray">
                        {$address} signed up 
                        <a class="btn-transparent px-0 border-0 notification-blue" href="/view/organizer/{$event->user->id}">
                            {$event->user->name}</a>'s event,
                        <a class="btn-transparent px-0 border-0 notification-blue" href="/event/{$event->id}">
                            {$event->eventName}</a> with your team, 
                        <a class="btn-transparent px-0 border-0 notification-blue" href="/view/team/{$selectTeam->id}">
                            {$selectTeam->teamName}</a>. 
                    </span>
                HTML,
            ];
        }
            
        $organizerNotification = [
            'user_id' => $event->user->id,
            'type' => 'teams',
            'link' =>  route('public.team.view', ['id' => $selectTeam->id]),
            'icon_type' => 'signup',
            'html' => <<<HTML
                <span class="notification-gray">
                    <a class="btn-transparent px-0 border-0 notification-blue" href="/view/team/{$selectTeam->id}">
                        <span class="notification-black">{$selectTeam->teamName}</span> </a>
                    has signed up for your event,
                    <a class="btn-transparent px-0 border-0 notification-blue" href="/event/{$event->id}">
                        {$event->eventName}</a>
                    . After signing up, they must complete and confirm registration for this event.
                </span>
            HTML,
        ];

        ActivityLogs::insert($allEventLogs);
        NotifcationsUser::insertWithCount([$organizerNotification, ...$memberNotification]);
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
