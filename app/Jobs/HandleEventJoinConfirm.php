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
                    <a href="/view/organizer/{$event->user->id}">
                        <span class="notification-blue">{$event->user->name}</span>
                    </a>'s event,
                    <a href="/event/{$event->id}">
                        <span class="notification-blue">{$event->eventName}</span>
                    </a>
                    with the team, 
                    <a href="/view/team/{$selectTeam->id}">
                        <span class="notification-blue">{$selectTeam->teamName}</span></a>. 
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
                    <a href="/view/team/{$selectTeam->id}">
                        <img src="/storage/{$selectTeam->teamBanner}"
                            width="30" height="30" 
                            onerror="this.src='/assets/images/404.png';"
                            class="object-fit-cover rounded-circle me-2"
                            alt="Team banner for {$selectTeam->teamName}">
                    </a>
                    <span class="notification-gray">
                        {$addressPart2} confirmed registration for 
                        <a href="/view/organizer/{$event->user->id}">
                            <span class="notification-blue">{$event->user->name}</span>
                        </a>'s event,
                        <a href="/event/{$event->id}">
                            <span class="notification-blue">{$event->eventName}</span>
                        </a> with <a href="/view/team/{$selectTeam->id}">
                            <span class="notification-blue">{$selectTeam->teamName}</span></a>. 
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
                    <a href="/view/team/{$selectTeam->id}">
                        <span class="notification-black">{$selectTeam->teamName}</span> 
                    </a>
                    has confirmed registration for your event,
                    <a href="/event/{$event->id}">
                        <span class="notification-blue">{$event->eventName}</span></a>.
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
                    <a href="/event/{$event->id}">
                        <span class="notification-blue">{$event->eventName}</span>
                    </a>
                    has been started for your team, 
                    <a href="/view/team/{$selectTeam->id}">
                        <span class="notification-blue">{$selectTeam->teamName}</span>
                    </a>. 
                </span>
            HTML;
            $htmlMail = <<<HTML
                <span class="notification-gray">
                    {$addressPart} have started a vote to quit  
                    <a href="/view/organizer/{$event->user->id}">
                        <span class="notification-blue">{$event->user->name}</span>
                    </a>'s event,
                    <a href="/event/{$event->id}">
                        <span class="notification-blue">{$event->eventName}</span>
                    </a>
                    for your team, 
                    <a href="/view/team/{$selectTeam->id}">
                        <span class="notification-blue">{$selectTeam->teamName}</span>
                    </a>. 
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
                        <a href="/view/organizer/{$event->user->id}">
                            <span class="notification-blue">{$event->user->name}</span>
                        </a>'s event,
                        <a href="/event/{$event->id}">
                            <span class="notification-blue">{$event->eventName}</span>
                        </a>
                        with your team, 
                        <a href="/view/team/{$selectTeam->id}">
                            <span class="notification-blue">{$selectTeam->teamName}</span></a>. 
                        Your team has chosen to leave. {$discountText}
                    </span>
                HTML;

                $htmlNotif = <<<HTML
                    <span class="notification-gray">
                        <a href="/view/team/{$selectTeam->id}">
                            <span class="notification-blue">{$selectTeam->teamName}</span></a> 
                        has voted to QUIT in the
                        <a href="/event/{$event->id}">
                            <span class="notification-blue">{$event->eventName}</span></a>.
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
                    <a href="/view/team/{$selectTeam->id}">
                        <span class="notification-black">{$selectTeam->teamName}</span> 
                    </a>
                    has voted to cancel registration for your event,
                    <a href="/event/{$event->id}">
                        <span class="notification-blue">{$event->eventName}</span></a>.
                    They will be refunded half of their fees.
                </span>
            HTML;

            $htmlNotif = <<<HTML
                <span class="notification-gray">
                    <a href="/view/team/{$selectTeam->id}">
                        <span class="notification-blue">{$selectTeam->teamName}</span></a> 
                    has voted to QUIT in the
                    <a href="/event/{$event->id}">
                        <span class="notification-blue">{$event->eventName}</span></a>.
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
                    <a href="/view/team/{$selectTeam->id}">
                        <span class="notification-blue">{$selectTeam->teamName}</span></a> 
                    has voted to QUIT in the
                    <a href="/event/{$event->id}">
                        <span class="notification-blue">{$event->eventName}</span></a>.
                    They will be refunded half of their fees.
                </span>
            HTML;

            foreach ($teamMembers as $member) {
                $htmlMail = <<<HTML
                    <span class="notification-gray">
                        You have taken part in a vote for participating 
                        <a href="/view/organizer/{$event->user->id}">
                            <span class="notification-blue">{$event->user->name}</span>
                        </a>'s event,
                        <a href="/event/{$event->id}">
                            <span class="notification-blue">{$event->eventName}</span>
                        </a>
                        with your team, 
                        <a href="/view/team/{$selectTeam->id}">
                            <span class="notification-blue">{$selectTeam->teamName}</span></a>. 
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
                        <a href="/view/organizer/{$event->user->id}">
                            <span class="notification-blue">{$event->user->name}</span>
                        </a>'s event,
                        <a href="/event/{$event->id}">
                            <span class="notification-blue">{$event->eventName}</span>
                        </a>
                        with <a href="/view/team/{$selectTeam->id}">
                            <span class="notification-blue">{$selectTeam->teamName}</span></a>. 
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
                    <a href="/view/team/{$selectTeam->id}">
                        <img src="/storage/{$selectTeam->teamBanner}"
                            width="30" height="30" 
                            onerror="this.src='/assets/images/404.png';"
                            class="object-fit-cover rounded-circle me-2"
                            alt="Team banner for {$selectTeam->teamName}">
                    </a>
                    <span class="notification-gray">
                        {$address} signed up 
                        <a href="/view/organizer/{$event->user->id}">
                            <span class="notification-blue">{$event->user->name}</span>
                        </a>'s event,
                        <a href="/event/{$event->id}">
                            <span class="notification-blue">{$event->eventName}</span>
                        </a> with your team, 
                        <a href="/view/team/{$selectTeam->id}">
                            <span class="notification-blue">{$selectTeam->teamName}</span></a>. 
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
                    <a href="/view/team/{$selectTeam->id}">
                        <span class="notification-black">{$selectTeam->teamName}</span> 
                    </a>
                    has signed up for your event,
                    <a href="/event/{$event->id}">
                        <span class="notification-blue">{$event->eventName}</span></a>
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
