<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Console\Traits\PrinterLoggerTrait;
use App\Console\Traits\RespondTaksTrait;
use App\Jobs\HandleEventJoinConfirm;
use App\Models\BracketDeadline;
use App\Models\EventDetail;
use App\Models\JoinEvent;
use App\Models\StripeConnection;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RespondTasks extends Command
{
    use PrinterLoggerTrait, RespondTaksTrait;

    protected $stripeService;

    public function __construct(StripeConnection $stripeService)
    {
        parent::__construct();

        $this->initializeTrait($stripeService);
    }

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
    protected $signature = 'tasks:respond {type=0 : The task type to process: 1=started, 2=live, 3=ended, 4=reg_over, 0=all} {--event_id= : Optional event ID to filter tasks}';

    protected $description = 'Respond tasks in the database';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $type = (int) $this->argument('type');
        $eventId = $this->option('event_id');

        $taskId = $this->logEntry($this->description, $this->signature, '*/30 * * * *', $now);
        try {
            $today = Carbon::today();
            $endedTaskIds = [];
            $liveTaskIds = [];
            $startedTaskIds = [];
            $regOverTaskIds = [];

            if ($type === 0) {
                $tasks = Task::whereDate('action_time', $today)
                    ->where('taskable_type', EventDetail::class)
                    ->where('action_time', '>=', $now)
                    ->where('action_time', '<=', $now->addMinutes(30))
                    ->get();
            } else {
                $eventIdInt = (int) $eventId;
                $tasks = Task::where('taskable_id', $eventIdInt)
                    ->where('taskable_type', EventDetail::class)
                    ->get();
            }

            foreach ($tasks as $task) {
                switch ($task->task_name) {
                    case 'ended':
                        $endedTaskIds[] = $task->taskable_id;
                        break;
                    case 'live':
                        $liveTaskIds[] = $task->taskable_id;
                        break;
                    case 'started':
                        $startedTaskIds[] = $task->taskable_id;
                        break;
                    case 'reg_over':
                        $regOverTaskIds[] = $task->taskable_id;
                        break;
                }
            }

            // dd(vars: $tasks);

            $with = [
                'roster:id,team_id,user_id',
                'members.user:id,name',
                'eventDetails.user:id,name',
                'eventDetails.tier:id,eventTier',
            ];

            if ($type == 0 || $type == 1) {
                $withNew = [
                  ...$with,
                  'payments.history',
                  'payments.transaction'
                ];

                $shouldCancel = false;

                foreach ($startedTaskIds as $eventId) {
                    $event = EventDetail::where('id', $eventId)
                        ->with('tier')
                        ->withCount(
                            ['joinEvents' => function ($q) {
                                $q->where('join_status', 'confirmed');
                            }]
                        )
                        ->first();

                    if ($event) {
                        $shouldCancel = $this->checkAndCancelEvent($event);
                        $joinEvents = JoinEvent::where('event_details_id', $event->id)
                            ->whereNot('join_status', 'confirmed')
                            ->where('payment_status', 'completed')
                            ->with([
                                'eventDetails:id,eventName,startDate,startTime,event_tier_id,user_id',
                                'payments.history', 'payments.tranaction'
                            ])
                            ->get();
                        $this->releaseFunds($event, $joinEvents);
                        if (!$shouldCancel) {
                            $joinEvents = JoinEvent::where('event_details_id', $eventId)
                                ->where('join_status', 'confirmed')
                                ->with([
                                    'eventDetails:id,eventName,startDate,startTime,event_tier_id,user_id',
                                    ...$withNew,
                                ])
                                ->get();
    
                            $this->capturePayments($event, $joinEvents);
                            $event->update(['status' => 'ONGOING']);
                            $this->handleEventTypes(
                                $this->getStartedNotifications($joinEvents),
                                $joinEvents,
                            );
                        }
                    }
                }

            }

            if ($type == 0 || $type == 2) {
                
                foreach ($liveTaskIds as $eventId) {
                    $joinEvents = JoinEvent::where('event_details_id', $eventId)
                        ->where('join_status', 'confirmed')
                        ->with([
                            'eventDetails:id,eventName,sub_action_public_date,sub_action_public_time,event_tier_id,user_id',
                            ...$with,
                        ])
                        ->get();
                    EventDetail::where('id', $eventId)->update(['status' => 'UPCOMING']);
                    $this->handleEventTypes(
                        $this->getLiveNotifications($joinEvents),
                        $joinEvents,
                    );
                }
               

              
            }

            if ($type == 0 || $type == 3) {
                foreach ($endedTaskIds as $eventId) {
                $endedEvents = JoinEvent::where('event_details_id', $eventId)
                    ->where('join_status', 'confirmed')
                    ->with([
                        'eventDetails:id,eventName,endDate,endTime,event_tier_id,user_id',
                        'position',
                        'team:id,teamName,teamBanner',
                        'eventDetails.tier',
                        ...$with,
                    ])
                    ->get();

                EventDetail::where('id', $eventId)->update(['status' => 'ENDED']);
                
                $event = EventDetail::where('id', $eventId)->with('tier')->first();

                [
                    $playerNotif,
                    $orgNotif,
                    $logs,
                    $memberIdList,
                    $prizeDetails
                ] = $this->getEndedNotifications($endedEvents, $event->tier);

                $this->handleEventTypes(
                    [ $playerNotif, $orgNotif ],
                    $endedEvents,
                );

                $this->handlePrizeAndActivityLogs(
                    $logs,
                    $endedEvents,
                    $memberIdList,
                    $prizeDetails                
                );
            }
            }

            
            if ($type == 0 || $type == 4) {
                foreach ($regOverTaskIds as $regOverTaskId) {

                    $event = EventDetail::where('id', $regOverTaskId)
                        ->with('tier')
                        ->withCount(
                            ['joinEvents' => function ($q) {
                                $q->where('join_status', 'confirmed');
                            }]
                        )
                        ->first();

                    if ($event) {
                        $this->checkAndCancelEvent($event);
                    }
                }
            }

            $now = Carbon::now();
            $this->logExit($taskId, $now);
        } catch (Exception $e) {
            $this->logError(null, $e);
        }
    }
};
