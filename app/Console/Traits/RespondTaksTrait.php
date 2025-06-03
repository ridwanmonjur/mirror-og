<?php

namespace App\Console\Traits;

use App\Models\ActivityLogs;
use App\Models\BracketDeadline;
use App\Models\Discount;
use App\Models\EventDetail;
use App\Models\EventJoinResults;
use App\Models\EventTier;
use App\Models\JoinEvent;
use App\Models\NotifcationsUser;
use App\Models\ParticipantCoupon;
use App\Models\ParticipantPayment;
use App\Models\UserCoupon;
use Illuminate\Support\Facades\DB;
use Exception;

use App\Models\RecordStripe;
use App\Models\StripeConnection;
use App\Models\Task;
use App\Models\TransactionHistory;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

trait RespondTaksTrait
{
    protected $stripeService;
    
    protected function initializeTrait(StripeConnection $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    public function checkAndCancelEvent(EventDetail $event)
    {
        if ($event->join_events_count != $event->tier->tierTeamSlot) {
            $joinEvents = JoinEvent::where('event_details_id', $event->id)
                ->where('join_status', 'confirmed')
                ->with([
                    'eventDetails:id,eventName,startDate,startTime,event_tier_id,user_id',
                    'payments.history', 'payments.tranaction'
                ])
                ->get();
            $this->releaseFunds($event, $joinEvents);
            $event->update(['status' => 'ENDED']);
            $deadlines = BracketDeadline::where('event_details_id', $event->id)->get();
            $deadlinesPast = $deadlines->pluck("id");
            Task::whereIn('taskable_id', $deadlinesPast)->where('taskable_type', BracketDeadline::class)->delete();
            Task::where('taskable_id', $event->id)->where('taskable_type', EventDetail::class)->delete();
            BracketDeadline::where('event_details_id', $event->id)->delete();
            return true;
        }
    
        return false;
    }


    public function releaseFunds(EventDetail $event, Collection $joinEvents) {
        DB::beginTransaction();
        try {
            
            foreach ($joinEvents as $join) {
                foreach ($join->payments as $participantPay) {  
                    if ($join->register_time == config('constants.SIGNUP_STATUS.NORMAL'))   {
                        if ($participantPay->transaction && $participantPay->type == 'stripe') {
                            $paymentIntent = $this->stripeService->retrieveStripePaymentByPaymentId(
                                $participantPay->transaction->payment_id
                            );
                            
                            if ($paymentIntent->status == 'requires_capture') {
                                $paymentIntent->cancel();
                            }
        
                            RecordStripe::where([
                                'id' => $participantPay->payment_id,
                               ])->update(['payment_status' => 'canceled'
                            ]);

                            
                        } elseif ($participantPay->type == 'wallet') {
                            $wallet = Wallet::firstOrCreate(
                                ['user_id' => $participantPay->id],
                                [
                                    'usable_balance' => 0,
                                    'current_balance' => 0,
                                ]
                            );
                            
                            $wallet->update([
                                'usable_balance' , $wallet->usable_balance + $participantPay->payment_amount,
                                'current_balance' , $wallet->usable_balance +  $participantPay->current_balance,
                            ]);

                        }


                        $participantPay->history?->delete();

                    }  else {
                        if ($participantPay->type == 'wallet') {
                            $wallet = Wallet::firstOrCreate(
                                ['user_id' => $participantPay->id],
                                [
                                    'usable_balance' => 0,
                                    'current_balance' => 0,
                                ]
                            );
                            
                            $wallet->update([
                                'usable_balance' , $wallet->usable_balance + $participantPay->payment_amount,
                                'current_balance' , $wallet->usable_balance +  $participantPay->current_balance,
                            ]);

                            $participantPay->history?->delete();
                        } else {
                            $participantCoupon = new ParticipantCoupon([
                                'code' => "Coupon{$event->eventName}{$participantPay->user_id}{$participantPay->id}",
                                'amount' ,
                                'description' => "Refund for {$event->eventName}",
                                'is_active' => true,
                                'is_public' => false,
                                'expires_at' => Carbon::now()->add(1)
                            ]);

                            $participantCoupon->save();

                            $newUserCoupon = new UserCoupon([
                                'user_id' => $participantPay->user_id,
                                'coupon_id' => $participantCoupon->id,
                                'redeemed_at' => false
                            ]);

                            $newUserCoupon->save();
                        }
                        
                    }        
                  
                      
                }
            }
            
            
            
            DB::commit();
            
        } catch (Exception $e) {
            DB::rollBack();
            $this->logError(null, $e);
            return [
                'released_payments' => 0,
                'created_discounts' => 0,
                'error' => $e->getMessage()
            ];
        }
    }

    public function capturePayments($event, $joinEvents) {
        $joins = $joinEvents->where('register_time', config('constants.SIGNUP_STATUS.NORMAL'));
        Log::info('<<< PaymentData : '. $joinEvents);
        Log::info('<<< startedEvents : '. $event);

        foreach ($joins as $join) {
            try {
                foreach ($join->payments as $participantPay) {
                    if ($participantPay->payment_id && $participantPay->type == 'stripe') {
                        // stripe capture hold
                        $paymentIntent = $this->stripeService->retrieveStripePaymentByPaymentId($participantPay->transaction->payment_id);

                        if ($paymentIntent->status === 'requires_capture') {
                            $paymentIntent->capture();
                        }

                        RecordStripe::where([
                            'id' => $participantPay->payment_id,
                           ])->update(['payment_status' => 'succeeded'
                        ]);
                    }  

                    if (isset ($participantPay->history)) {
                        TransactionHistory::where([
                            'id' => $participantPay->history->id,
                        ])->update([
                            'name' => "{$join->eventDetails->eventName}: Entry Fee",
                            'type' => 'Event Entry Fee',
                            'link' => route('public.event.view', ['id' => $join->eventDetails->id]),
                        ]);
                    }
                }
                
                
            } catch (Exception $e) {
                $this->logError(null, $e);
            }
        }
    }

    

    public function getLiveNotifications($joinList) {
        $playerNotif = []; $orgNotif = [];
        foreach ($joinList as $join) {
            $memberHtml = <<<HTML
                <span class="notification-gray">
                    <button class="btn-transparent px-0 border-0  Color-{$join->eventDetails->tier->eventTier}" data-href="/event/{$join->eventDetails->id}">
                    {$join->eventDetails->eventName}</button> is now live. 
                    </span>
                HTML;
            $memberEmail = <<<HTML
                <span class="notification-gray">
                    <span class="btn-transparent px-0 border-0 " >
                    <span class="notification-blue">{$join->eventDetails->eventName}</span></span> is now live. 
                    </span>
                HTML;
            $playerNotif[$join->id ] = [
                'type' => 'event',
                'link' =>  route('public.event.view', ['id' => $join->eventDetails->id]),
                'icon_type' => 'live',
                'html' => $memberHtml,
                'mail' => $memberEmail,
                'mailClass' => 'EventLiveMail',
                'subject' => 'An event you joined has gone live.',
                'created_at' => DB::raw('NOW()')
            ];

            if (!isset($orgNotif[$join->eventDetails->user_id])) {
                $orgNotif[$join->eventDetails->user_id] = [
                    'type' => 'event',
                    'link' =>  route('public.event.view', ['id' => $join->eventDetails->id]),
                    'icon_type' => 'live',
                    'html' => $memberHtml,
                    'user_id' => $join->eventDetails->user_id,
                    'user' => $join->eventDetails->user,
                    'mail' => $memberEmail,
                    'mailClass' => 'EventLiveMail',
                    'subject' => 'Your event has gone live.',
                    'created_at' => DB::raw('NOW()')
                ];
            }
        }

        return [$playerNotif, $orgNotif];
    }

    public function getStartedNotifications($joinList) {
        $playerNotif = []; $orgNotif = [];
        foreach ($joinList as $join) {

            $memberHtml = <<<HTML
                <span class="notification-gray">
                    <button class="btn-transparent px-0 border-0 Color-{$join->eventDetails->tier->eventTier}" data-href="/event/{$join->eventDetails->id}">
                    {$join->eventDetails->eventName}</button> has started and is now underway. 
                    </span>
                HTML;
            
            $memberEmail = <<<HTML
                <span class="notification-gray">
                    <a class="btn-transparent px-0 border-0" href="/event/{$join->eventDetails->id}">
                    <span class="notification-blue">{$join->eventDetails->eventName}</span></a> has started and is now underway. 
                    </span>
                HTML;
            
            $playerNotif[$join->id] = [
                    'type' => 'event',
                    'link' =>  route('public.event.view', ['id' => $join->eventDetails->id]),
                    'icon_type' => 'started',
                    'html' => $memberHtml,
                    'mail' => $memberEmail,
                    'mailClass' => 'EventStartMail',
                    'subject' => 'An event you joined has started.',
                    'created_at' => DB::raw('NOW()')
            ];

            if (!isset($orgNotif[$join->eventDetails->user_id])) {
                $orgNotif[$join->eventDetails->user_id] = [
                    'type' => 'event',
                    'link' =>  route('public.event.view', ['id' => $join->eventDetails->id]),
                    'icon_type' => 'started',
                    'html' => $memberHtml,
                    'user_id' => $join->eventDetails->user_id,
                    'user' => $join->eventDetails->user,
                    'mail' => $memberEmail,
                    'mailClass' => 'EventStartMail',
                    'subject' => 'Your event has started.',
                    'created_at' => DB::raw('NOW()')
                ];
            }
        }

        return [$playerNotif, $orgNotif];
    }

    public function handleEventTypes($notificationMap, $joinList) {
        [$playerNotif, $orgNotif] = $notificationMap;

        if (!empty($joinList)) {
            DB::beginTransaction();
            try {
                $memberNotification = $organizerNotification = [];
                foreach ($joinList as $join) {
                    if (!isset($playerNotif[$join->id])) {
                        continue;
                    }

                    $memberMailClass = 'App\\Mail\\'. $playerNotif[$join->id]['mailClass'];

                    if (! class_exists($memberMailClass)) {
                        throw new \InvalidArgumentException("Strategy class {$memberMailClass} does not exist.");
                    }

                    $memberMailInvocation = new $memberMailClass([
                        'text' => $playerNotif[$join->id]['mail'],
                        'link' =>  $playerNotif[$join->id]['link'],
                        'subject' =>  $playerNotif[$join->id]['subject'],
                    ]);
                    
                    $memberEmails = collect($join->roster)
                        ->map(function($member) {
                            return $member->user && $member->user->email 
                                ? $member->user->email 
                                : null;
                        })
                        ->filter()
                        ->all();

                    foreach ($join->roster as $member) {
                        $memberNotification[] = [
                            'user_id' => $member->user->id,
                            'type' => $playerNotif[$join->id]['type'],
                            'link' =>  $playerNotif[$join->id]['link'],
                            'icon_type' => $playerNotif[$join->id]['icon_type'],
                            'html' => $playerNotif[$join->id]['html'],
                            'created_at' => DB::raw('NOW()')
                        ];

                        if ($member->user->email) $memberEmails[] = $member->user->email;
                    }

                    if (!empty($memberEmails)) {
                        Mail::to($memberEmails)->send($memberMailInvocation);
                    }

                   
                }

                foreach ($orgNotif as $notification2) {
                    $organizerNotification[] = [
                        'user_id' => $notification2['user_id'],
                        'type' => $notification2['type'],
                        'link' => $notification2['link'],
                        'icon_type' => $notification2['icon_type'],
                        'html' => $notification2['html'],
                        'created_at' => DB::raw('NOW()'),
                    ];
                    
                    $orgMailClass = 'App\\Mail\\'. $notification2['mailClass'];
                    
                    if (! class_exists($orgMailClass)) {
                        throw new \InvalidArgumentException("Strategy class {$orgMailClass} does not exist.");
                    }
                    
                    $orgMailInvocation = new $orgMailClass([
                        'text' => $notification2['mail'],
                        'link' => $notification2['link'],
                        'subject' =>  $notification2['subject'],
                    ]);
                    
                    $user = $notification2['user'];
                    if ($user && $user->email) {
                        Mail::to($user->email)->send($orgMailInvocation);
                    }
                }

                NotifcationsUser::insertWithCount([
                    ...$memberNotification,
                    ...$organizerNotification
                ]);
                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                $this->logError(null, $e);
            }
            
        } 

    }

    public function ordinalPrefix($number)
    {
        $number = intval($number);

        if ($number % 100 >= 11 && $number % 100 <= 13) {
            return $number.'th';
        }

        switch ($number % 10) {
            case 1:
                return $number.'st';
            case 2:
                return $number.'nd';
            case 3:
                return $number.'rd';
            default:
                return $number.'th';
        }
    }

    public function handlePrizeAndActivityLogs($logMap, $joinList, $memberIdMap, $prizeDetails) {
        if (!empty($joinList)) {
            DB::beginTransaction();
            try {
                foreach ($joinList as $join) {
                    ActivityLogs::findActivityLog([
                        'subject_type' => User::class,
                        'object_type' => EventJoinResults::class,
                        'subject_id' => $memberIdMap[$join->id],
                        'object_id' => $join->id,
                        'action' => 'Position',
                    ])->delete();

                    if (isset($logMap[$join->id])) {
                        ActivityLogs::createActivityLogs([
                            'subject_type' => User::class,
                            'object_type' => EventJoinResults::class,
                            'subject_id' => $memberIdMap[$join->id],
                            'object_id' => $join->id,
                            'action' => 'Position',
                            'log' => $logMap[$join->id]
                        ]);
                    }


                    if (isset($prizeDetails[$join->id])) { 
                        foreach ($join->roster as $member) {
                            $transactionHistory[] = [
                                'user_id' => $member->user->id,
                                'name' => "Prize Money: RM {$prizeDetails[$join->id]}",
                                'type' => "Prize Money",
                                'link' => null,
                                'amount' => $prizeDetails[$join->id],
                                'summary' => "Prize Money for event",
                                'isPositive' => false,
                                'date' => DB::raw(NOW()),
                            ];

                            $walletData[] = [
                                'user_id' => $member->user->id,
                                'usable_balance' => DB::raw('COALESCE(usable_balance, 0) + ' . $prizeDetails[$join->id]),
                                'current_balance' => DB::raw('COALESCE(current_balance, 0) + ' . $prizeDetails[$join->id]),
                            ];
                        
                        }
                         
                    }

                    TransactionHistory::insert($transactionHistory);
                    Wallet::upsert(
                        $walletData,
                        ['user_id'], 
                        ['usable_balance', 'current_balance'] 
                    );
                }

                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                $this->logError(null, $e);
            }
            
        } 
        
    }

    private function getPrizeFromPositionTIer(string| int $position , EventTier $tier ) {
        $positionTierMap  = [
            'Dolphin' => [
                '1' => 200,
                '2' => 125,
                '3' => 75,
                '4' => 50,
                '5' => 25,
                '6' => 25,
            ],
            'Starfish' => [
                '1' => 200,
                '2' => 125,
                '3' => 75,
                '4' => 50,
                '5' => 25,
                '6' => 25
            ],
            'Turtle' => [
                '1' => 200,
                '2' => 125,
                '3' => 75,
                '4' => 50,
                '5' => 25,
                '6' => 25
            ]
        ];

        $prize = (isset($positionTierMap[$tier->eventTier]) 
            && isset($positionTierMap[$tier->eventTier][$position])) ? $positionTierMap[$tier->eventTier][$position] : null;
        return $prize;
    }

    public function getEndedNotifications(Collection $joinList, EventTier $tier) {
        $playerNotif = []; $orgNotif = [];
        $memberIdList = [];
        $prize = [];
        $logs = [];
        foreach ($joinList as $join) {
            if ($join->position?->position) {
                $position = $join->position->position;
                $currentPrize = $this->getPrizeFromPositionTier($position, $tier);
                if ($currentPrize) {
                    $prize[$join->id] = $currentPrize;
                }

                $positionString = $this->ordinalPrefix($position);
                $memberHtml = <<<HTML
                    <span class="notification-gray">
                        <button class="btn-transparent px-0 border-0  Color-{$join->eventDetails->tier->eventTier}" data-href="/event/{$join->eventDetails->id}">
                        {$join->eventDetails->eventName}</button> has now ended. You achieved 
                        <span class="notification-other"><span class="notification-{$join->position}">
                            {$positionString}</span></span> position with your team,
                        <button class="btn-transparent px-0 border-0 notification-entity" 
                            data-href="/view/team/{$join->team->id}" alt="Team Link"
                        >
                            {$join->team->teamName}</button>. 
                        </span>
                    HTML;
                
                $activityLog = <<<HTML
                    <span>
                        <a class="px-0 border-0" href="/view/team/{$join->team->id}" alt="Team View">
                            <img src="/storage/{$join->team->teamBanner}" 
                                width="30" height="30"
                                onerror="this.src='/assets/images/404.png';"
                                class="object-fit-cover rounded-circle me-2"
                                alt="Event View"
                            ></a>
                        <span class="notification-gray"> You achieved 
                        {$positionString} position with your team,
                        <a class="px-0 border-0" href="/view/team/{$join->team->id}" alt="Team Link">
                            <span class="notification-blue">{$join->team->teamName}</span></a> in the event, 
                            <a class="px-0 border-0" href="/event/{$join->eventDetails->id}" alt="Event Link">
                            <span class="notification-blue">{$join->eventDetails->eventName}</span></a>. 
                    </span>
                HTML;

                $memberEmail = <<<HTML
                    <span class="notification-gray">
                        <span class="px-0 border-0 notification-blue" >
                        {$join->eventDetails->eventName}</span> has now ended.  You achieved 
                        <span>{$positionString}</span> position with your team,
                        <span class="px-0 border-0 notification-blue" >{$join->team->teamName}</span>. 
                    </span>
                    HTML;
            } else {
                $memberHtml = <<<HTML
                    <span class="notification-gray">
                        <button class="btn-transparent px-0 border-0  Color-{$join->eventDetails->tier->eventTier}" data-href="/event/{$join->eventDetails->id}">
                        {$join->eventDetails->eventName}</button> has now ended.
                    </span>
                    HTML;
                
                $activityLog = null;

                $memberEmail = <<<HTML
                    <span class="notification-gray">
                        <span class="px-0 border-0 notification-blue" >
                        {$join->eventDetails->eventName}</span> has now ended.
                    </span>
                    HTML;
            }

            $orgHtml = <<<HTML
                <span class="notification-gray">
                    <button class="btn-transparent px-0 border-0  Color-{$join->eventDetails->tier->eventTier}" data-href="/event/{$join->eventDetails->id}">
                    {$join->eventDetails->eventName}</button> has now ended.
                </span>
                HTML;
        

            $orgEmail = <<<HTML
                <span class="notification-gray">
                    <span class="px-0 border-0 notification-blue" >
                    {$join->eventDetails->eventName}</span> has now ended.
                </span>
                HTML;

            if ($activityLog) {

                $logs[$join->id] = [
                    'subject_type' => User::class,
                    'object_type' => EventJoinResults::class,
                    'object_id' => $join->id,
                    'action' => 'Position',
                    'log' => $activityLog
                ];
            }

            $playerNotif[$join->id] = [
                'type' => 'event',
                'link' =>  route('public.event.view', ['id' => $join->eventDetails->id]),
                'icon_type' => 'ended',
                'html' => $memberHtml,
                'mail' => $memberEmail,
                'mailClass' => 'EventEndMail',
                'subject' => 'An event you joined has ended.',
                'created_at' => DB::raw('NOW()')
            ];

            if (!isset($orgNotif[$join->eventDetails->user_id])) {
                $orgNotif[$join->eventDetails->user_id] = [
                    'type' => 'event',
                    'link' =>  route('public.event.view', ['id' => $join->eventDetails->id]),
                    'icon_type' => 'ended',
                    'html' => $orgHtml,
                    'mail' => $orgEmail,
                    'user_id' => $join->eventDetails->user_id,
                    'user' => $join->eventDetails->user,
                    'mailClass' => 'EventEndMail',
                    'subject' => 'Your event has ended.',
                    'created_at' => DB::raw('NOW()')
                ];
            }

            foreach ($join->roster as $member) {
                if (!isset($memberIdList[$join->id])) {
                    $memberIdList[$join->id] = [];
                }

                $memberIdList[$join->id][] = $member->user_id;
            }
        }    

        return [$playerNotif, $orgNotif, $logs, $memberIdList, $prize];
    }

   
}