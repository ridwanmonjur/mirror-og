<?php

namespace App\Console\Commands;

use App\Models\NotifcationsUser;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

use App\Models\PaymentTransaction;
use App\Models\StripePayment;
use Illuminate\Support\Facades\Mail;
use Stripe\StripeClient;

trait RespondTrait
{
    public function handleEndedPayments(array $endedTaskIds, $taskId) {
        $paymentData = DB::table('event_details')
            ->join('join_events', 'event_details.id', '=', 'join_events.event_details_id')
            ->join('participant_payments', 'join_events.id', '=', 'participant_payments.join_events_id')
            ->join('all_payment_transactions',  'all_payment_transactions.id', '=', 'participant_payments.payment_id')
            ->whereIn('event_details.id', $endedTaskIds)
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
                $paymentIntent = $stripe->retrieveStripePaymentByPaymentId($item['payment_id']);

                if ($paymentIntent->status === 'requires_capture') {
                    $capturedPayment = $paymentIntent->capture();

                    $updatedPayments[] = [
                        'id' => $item['id'],
                        'payment_id' => $item['payment_id'],
                        'payment_status' => $capturedPayment['status']
                    ];
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

    public function getEndedNotifications($joinList) {
        $notifications = [];
        foreach ($joinList as $join) {
            $memberHtml = <<<HTML
                <span class="notification-gray">
                    <button class="btn-transparent px-0 border-0 notification-blue" data-href="/event/{$join->eventDetails->id}">
                    {$join->eventDetails->eventName}</button> has now ended. 
                    </span>
                HTML;
            $memberEmail = <<<HTML
                <span class="notification-gray">
                    <a class="px-0 border-0 notification-blue" href="/event/{$join->eventDetails->id}">
                    {$join->eventDetails->eventName}</a> has now ended. 
                    </span>
                HTML;
            $notifications[$join->id] = [
                    'member' => [
                        'type' => 'event',
                        'link' =>  route('public.event.view', ['id' => $join->eventDetails->id]),
                        'icon_type' => 'ended',
                        'html' => $memberHtml,
                        'mail' => $memberEmail,
                        'mailClass' => 'EventEndMail'
                    ], 
                    'organizer' => [
                        'type' => 'event',
                        'link' =>  route('public.event.view', ['id' => $join->eventDetails->id]),
                        'icon_type' => 'ended',
                        'html' => $memberHtml,
                        'mail' => $memberEmail,
                        'mailClass' => 'EventEndMail'
                    ]
            ];
        }

        return $notifications;
    }

    public function getLiveNotifications($joinList) {
        $notifications = [];
        foreach ($joinList as $join) {
            $memberHtml = <<<HTML
                <span class="notification-gray">
                    <button class="btn-transparent px-0 border-0 notification-blue" data-href="/event/{$join->eventDetails->id}">
                    {$join->eventDetails->eventName}</button> is now live. 
                    </span>
                HTML;
            $memberEmail = <<<HTML
                <span class="notification-gray">
                    <a class="btn-transparent px-0 border-0 " href="/event/{$join->eventDetails->id}">
                    <span class="notification-blue">{$join->eventDetails->eventName}</span></a> is now live. 
                    </span>
                HTML;
            $notifications[$join->id ] = [
                    'member' => [
                        'type' => 'event',
                        'link' =>  route('public.event.view', ['id' => $join->eventDetails->id]),
                        'icon_type' => 'live',
                        'html' => $memberHtml,
                        'mail' => $memberEmail,
                        'mailClass' => 'EventEndMail'
                    ], 
                    'organizer' => [
                        'type' => 'event',
                        'link' =>  route('public.event.view', ['id' => $join->eventDetails->id]),
                        'icon_type' => 'live',
                        'html' => $memberHtml,
                        'mail' => $memberEmail,
                        'mailClass' => 'EventEndMail'
                    ]
            ];
        }

        return $notifications;
    }

    public function getStartedNotifications($joinList) {
        $notifications = [];
        foreach ($joinList as $join) {
            $timeDate = Carbon::parse($join->eventDetails->startDate . ' ' . $join->eventDetails->startTime);

            $memberHtml = <<<HTML
                <span class="notification-gray">
                    <button class="btn-transparent px-0 border-0 notification-blue" data-href="/event/{$join->eventDetails->id}">
                    {$join->eventDetails->eventName}</button> starts in {$timeDate->diffForHumans()} at {$timeDate->format('g:i A')} 
                    on {$timeDate->format('M d, Y')}. 
                    </span>
                HTML;
            
            $memberEmail = <<<HTML
                <span class="notification-gray">
                    <a class="btn-transparent px-0 border-0" href="/event/{$join->eventDetails->id}">
                    <span class="notification-blue">{$join->eventDetails->eventName}</span></a> starts in {$timeDate->diffForHumans()} at {$timeDate->format('g:i A')} 
                    on {$timeDate->format('M d, Y')}. 
                    </span>
                HTML;
            
            $notifications[$join->id] = [
                    'member' => [
                        'type' => 'event',
                        'link' =>  route('public.event.view', ['id' => $join->eventDetails->id]),
                        'icon_type' => 'started',
                        'html' => $memberHtml,
                        'mail' => $memberEmail,
                        'mailClass' => 'EventStartMail'
                    ], 
                    'organizer' => [
                        'type' => 'event',
                        'link' =>  route('public.event.view', ['id' => $join->eventDetails->id]),
                        'icon_type' => 'started',
                        'html' => $memberHtml,
                        'mail' => $memberEmail,
                        'mailClass' => 'EventStartMail'
                    ]
            ];
        }

        return $notifications;
    }

    public function handleEventTypes($notificationMap, $joinList, $taskId) {
        if (!empty($joinList)) {
            DB::beginTransaction();
            try {
                $memberNotification = $organizerNotification = [];
                foreach ($joinList as $join) {
                    if (!isset($notificationMap[$join->id])) {
                        continue;
                    }
                    $memberMailClass = 'App\\Mail\\'. $notificationMap[$join->id]['member']['mailClass'];

                    if (! class_exists($memberMailClass)) {
                        throw new \InvalidArgumentException("Strategy class {$memberMailClass} does not exist.");
                    }

                    $memberMailInvocation = new $memberMailClass([
                        'text' => $notificationMap[$join->id]['member']['mail'],
                        'link' =>  $notificationMap[$join->id]['member']['link'],
                    ]);
                    
                    foreach ($join->members as $member) {
                        $memberNotification[] = [
                            'user_id' => $member->user->id,
                            'type' => $notificationMap[$join->id]['member']['type'],
                            'link' =>  $notificationMap[$join->id]['member']['link'],
                            'icon_type' => $notificationMap[$join->id]['member']['icon_type'],
                            'html' => $notificationMap[$join->id]['member']['html'],
                        ];

                        if ($member->user->mail) Mail::to($member->user->email)->send($memberMailInvocation);
                    }

                    $organizerNotification[] = [
                        'user_id' => $join->eventDetails->user_id,
                        'type' => $notificationMap[$join->id]['organizer']['type'],
                        'link' =>  $notificationMap[$join->id]['organizer']['link'],
                        'icon_type' => $notificationMap[$join->id]['organizer']['icon_type'],
                        'html' => $notificationMap[$join->id]['organizer']['html'],
                    ];

                    $orgMailClass = 'App\\Mail\\'. $notificationMap[$join->id]['organizer']['mailClass'];

                    if (! class_exists($orgMailClass)) {
                        throw new \InvalidArgumentException("Strategy class {$orgMailClass} does not exist.");
                    }

                    $orgMailInvocation = new $orgMailClass([
                        'text' => $notificationMap[$join->id]['organizer']['mail'],
                        'link' =>  $notificationMap[$join->id]['organizer']['link'],
                    ]);
                    

                    if ($join->eventDetails->user->email) 
                        Mail::to($join->eventDetails->user->email)->send($orgMailInvocation);
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
}