<?php

namespace App\Console\Commands;

use App\Models\NotifcationsUser;
use App\Models\PaymentTransaction;
use App\Models\StripePayment;
use App\Models\Task;
use App\Console\Commands\PrinterLoggerTrait;
use App\Models\JoinEvent;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\StripeClient;

class RespondTasks extends Command
{
    use PrinterLoggerTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    /**
     * The console command description.
     *
     * @var string
     */
    protected $signature = 'tasks:respond';

    protected $description = 'Respond tasks in the database';


    /**
     * Execute the console command.
     */
    public function handle(){
        $this->getTodayTasksByName();
    }

    public function getTodayTasksByName()
    {
        $now = Carbon::now();
        $commandName = 'tasks:respond';
        $taskId = $this->logEntry('Respond Tasks', $commandName, '*/30 * * * *', $now, $commandName);        try {
            $today = Carbon::today();
            $tasks = Task
            // ::whereDate('action_time', $today)
            // ->where('action_time', '>=', $now)
            // ->where('action_time', '<=', $now->addMinutes(30))
                ::all();
            // $tasks = Task
            //     ::whereDate('action_time', $today)
            //     ->where('action_time', '>=', $now)
            //     ->where('action_time', '<=', $now->addMinutes(30))
            //     ->get();
            $endedTaskIds= $liveTaskIds = $startedTaskIds = [];
            foreach ($tasks as $task) {
                switch ($task->task_name) {
                    case 'ended':
                        $endedTasks[] = $task;
                        $endedTaskIds[] = $task->event_id;
                        break;
                    case 'live':
                        $liveTasks[] = $task;
                        $liveTaskIds[] = $task->event_id;
                        break;
                    case 'started':
                        $startedTasks[] = $task;
                        $startedTaskIds[] = $task->event_id;
                        break;
                }
            }

            $startedEvents = JoinEvent::whereIn('event_details_id', $startedTaskIds)
                ->where('join_status', 'confirmed')
                ->with('members', 'members.user', 'eventDetails', 'eventDetails.user')
                ->get();

            $liveEvents = JoinEvent::whereIn('event_details_id', $liveTaskIds)
                ->where('join_status', 'confirmed')
                ->with('members', 'members.user', 'eventDetails', 'eventDetails.user')
                ->get();
               
            $endedEvents = JoinEvent::whereIn('event_details_id', $endedTaskIds)
                ->where('join_status', 'confirmed')
                ->with('members', 'members.user', 'eventDetails', 'eventDetails.user')
                ->get();
      

            $this->handleEventTypes(
                $this->getEndedNotifications($endedEvents), 
                $endedEvents, 
                $taskId
            );

            $this->handleEventTypes(
                $this->getLiveNotifications($liveEvents), 
                $liveEvents, 
                $taskId
            );

            $this->handleEventTypes(
                $this->getStartedNotifications($startedEvents), 
                $startedEvents, 
                $taskId
            );

            $this->handleEndedPayments($endedTaskIds, $taskId);

            $now = Carbon::now();
            $this->logExit($taskId, $now);
            return $tasks;
        } catch (Exception $e) {
            $this->logError($taskId, $e);
        }
    }

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
            return [
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
    }

    public function getLiveNotifications($joinList) {
        foreach ($joinList as $join) {
            $memberHtml = <<<HTML
                <span class="notification-gray">
                    <button class="btn-transparent px-0 border-0 notification-blue" data-href="/event/{$join->eventDetails->id}">
                    {$join->eventDetails->eventName}</button> is now live. 
                    </span>
                HTML;
            $memberEmail = <<<HTML
                <span class="notification-gray">
                    <a class="btn-transparent px-0 border-0 notification-blue" href="/event/{$join->eventDetails->id}">
                    {$join->eventDetails->eventName}</a> is now live. 
                    </span>
                HTML;
            return [
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
    }

    public function getStartedNotifications($joinList) {
      
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
                    <a class="btn-transparent px-0 border-0 notification-blue" href="/event/{$join->eventDetails->id}">
                    {$join->eventDetails->eventName}</a> starts in {$timeDate->diffForHumans()} at {$timeDate->format('g:i A')} 
                    on {$timeDate->format('M d, Y')}. 
                    </span>
                HTML;
            
            return [
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
    }

    public function handleEventTypes($notificationMap, $joinList, $taskId) {
        if (!empty($joinList)) {
            DB::beginTransaction();
            try {
                $mailClass = 'App\\Mail\\'. $notificationMap['member']['mailClass'];

                if (! class_exists($mailClass)) {
                    throw new \InvalidArgumentException("Strategy class {$mailClass} does not exist.");
                }

                $mailInvocation = new $mailClass([
                    'text' => $notificationMap['member']['mail'],
                    'link' =>  $notificationMap['member']['link'],
                ]);

                $memberNotification = $organizerNotification = [];
                foreach ($joinList as $join) {
                    foreach ($join->members as $member) {
                        $memberNotification[] = [
                            'user_id' => $member->user->id,
                            'type' => $notificationMap['member']['type'],
                            'link' =>  $notificationMap['member']['link'],
                            'icon_type' => $notificationMap['member']['icon_type'],
                            'html' => $notificationMap['member']['html'],
                        ];

                    Mail::to($member->user->email)->send($mailInvocation);
                    }
                }

                $mailClass = 'App\\Mail\\'. $notificationMap['organizer']['mailClass'];

                if (! class_exists($mailClass)) {
                    throw new \InvalidArgumentException("Strategy class {$mailClass} does not exist.");
                }
                $mailInvocation = new $mailClass([
                    'text' => $notificationMap['organizer']['mail'],
                    'link' =>  $notificationMap['organizer']['link'],
                ]);
                
                foreach ($joinList as $join) {
                    $organizerNotification[] = [
                        'user_id' => $join->eventDetails->user_id,
                        'type' => $notificationMap['organizer']['type'],
                        'link' =>  $notificationMap['organizer']['link'],
                        'icon_type' => $notificationMap['organizer']['icon_type'],
                        'html' => $notificationMap['organizer']['html'],
                    ];

                    Mail::to($join->eventDetails->user->email)->send($mailInvocation);
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
};
