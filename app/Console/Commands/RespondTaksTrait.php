<?php

namespace App\Console\Commands;

use App\Models\ActivityLogs;
use App\Models\EventJoinResults;
use App\Models\NotifcationsUser;
use Illuminate\Support\Facades\DB;
use Exception;

use App\Models\PaymentTransaction;
use App\Models\StripePayment;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

trait RespondTaksTrait
{
    public function capturePayments(array $startedTaskIds, $taskId) {
        $paymentData = DB::table('event_details')
            ->join('join_events', 'event_details.id', '=', 'join_events.event_details_id')
            ->join('participant_payments', 'join_events.id', '=', 'participant_payments.join_events_id')
            ->join('all_payment_transactions',  'all_payment_transactions.id', '=', 'participant_payments.payment_id')
            ->whereIn('event_details.id', $startedTaskIds)
            ->where('all_payment_transactions.payment_status', 'requires_capture')
            ->get();

        $resultList = $paymentData->map(function ($item) {
            return [
                'id' => $item->id,
                'payment_id' => $item->payment_id
            ];
        })->toArray();

        $updatedPayments = [];
        foreach ($resultList as $item) {
            try {
                $stripe = new StripePayment();
                if (isset($item['payment_id'])) {
                    $paymentIntent = $stripe->retrieveStripePaymentByPaymentId($item['payment_id']);

                    if ($paymentIntent->status == 'requires_capture') {
                        $capturedPayment = $paymentIntent->capture();

                        $updatedPayments[] = [
                            'id' => $item['id'],
                            'payment_id' => $item['payment_id'],
                            'payment_status' => $capturedPayment['status']
                        ];
                    }
                }
            } catch (Exception $e) {
                $this->logError($taskId, $e);
            }
        }


        if (!empty($updatedPayments)) {
            DB::beginTransaction();
            try {
                foreach ($updatedPayments as $payment) {
                    PaymentTransaction::where('id', $payment['id'])
                        ->update(['payment_status' => $payment['payment_status']]);
                }
                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                $this->logError($taskId, $e);
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
                'member' => [
                    'type' => 'event',
                    'link' =>  route('public.event.view', ['id' => $join->eventDetails->id]),
                    'icon_type' => 'started',
                    'html' => $memberHtml,
                    'mail' => $memberEmail,
                    'mailClass' => 'EventStartMail',
                    'subject' => 'An event you joined has started.',
                    'created_at' => DB::raw('NOW()')
                ]
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

    public function handleEventTypes($notificationMap, $joinList, $taskId) {
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
                        'created_at' => DB::raw('NOW()')
                    ];
                    
                    $orgMailClass = 'App\\Mail\\'. $notification2['mailClass'];
                    
                    if (! class_exists($orgMailClass)) {
                        throw new \InvalidArgumentException("Strategy class {$orgMailClass} does not exist.");
                    }
                    
                    $orgMailInvocation = new $orgMailClass([
                        'text' => $notification2['mail'],
                        'link' => $notification2['link'],
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
                $this->logError($taskId, $e);
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

    public function handleEndedActivityLogs($logMap, $joinList, $memberIdMap, $taskId) {
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

                    if (!isset($logMap[$join->id])) {
                        continue;
                    }

                    ActivityLogs::createActivityLogs([
                        'subject_type' => User::class,
                        'object_type' => EventJoinResults::class,
                        'subject_id' => $memberIdMap[$join->id],
                        'object_id' => $join->id,
                        'action' => 'Position',
                        'log' => $logMap[$join->id]
                    ]);
                }

                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                $this->logError($taskId, $e);
            }
            
        } 
        
    }

    public function getEndedNotifications($joinList) {
        $playerNotif = []; $orgNotif = [];
        $memberIdList = [];
        $logs = [];
        foreach ($joinList as $join) {
            if ($join->position?->position) {
                $positionString = $this->ordinalPrefix($join->position->position);
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

        return [$playerNotif, $orgNotif, $logs, $memberIdList];
    }

   
}