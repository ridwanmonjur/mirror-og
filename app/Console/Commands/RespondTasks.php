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
                $tasks = Task
                    ::whereDate('action_time', $today)
                    ->where('taskable_type', EventDetail::class)
                    ->where('action_time', '>=', $now)
                    ->where('action_time', '<=', $now->addMinutes(30))
                    ->get();
            } else {
                $eventIdInt = (int) $eventId;
                $tasks = Task::where('taskable_id', $eventIdInt)
                    ->where('taskable_type', EventDetail::class)
                    ->get();
                // dd("sss", $eventIdInt, $tasks);  
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
                $this->capturePayments($startedTaskIds, $taskId);

                $startedEvents = JoinEvent::whereIn('event_details_id', $startedTaskIds)
                    ->where('join_status', 'confirmed')
                    ->with([
                        'eventDetails:id,eventName,startDate,startTime,event_tier_id,user_id',
                        ...$with,
                    ])
                    ->get();


                EventDetail::whereIn('id', $startedTaskIds)->update(['status' => 'ONGOING']);
                $this->handleEventTypes(
                    $this->getStartedNotifications($startedEvents),
                    $startedEvents,
                    $taskId
                );
            }

            if ($type == 0 || $type == 2) {

                $liveEvents = JoinEvent::whereIn('event_details_id', $liveTaskIds)
                    ->where('join_status', 'confirmed')
                    ->with([
                        'eventDetails:id,eventName,sub_action_public_date,sub_action_public_time,event_tier_id,user_id',
                        ...$with,
                    ])
                    ->get();

                EventDetail::whereIn('id', $liveTaskIds)->update(['status' => 'UPCOMING']);
                $this->handleEventTypes(
                    $this->getLiveNotifications($liveEvents),
                    $liveEvents,
                    $taskId
                );
            }

            if ($type == 0 || $type == 3) {
                $endedEvents = JoinEvent::whereIn('event_details_id', $endedTaskIds)
                    ->where('join_status', 'confirmed')
                    ->with([
                        'eventDetails:id,eventName,endDate,endTime,event_tier_id,user_id',
                        'position',
                        'team:id,teamName,teamBanner',
                        ...$with,
                    ])
                    ->get();


                EventDetail::whereIn('id', $endedTaskIds)->update(['status' => 'ENDED']);

                [
                    $processedEndedNotifications,
                    $logs,
                    $memberIdList
                ] = $this->getEndedNotifications($endedEvents);

                $this->handleEventTypes(
                    $processedEndedNotifications,
                    $endedEvents,
                    $taskId
                );

                $this->handleEndedActivityLogs(
                    $logs,
                    $endedEvents,
                    $memberIdList,
                    $taskId
                );
            }

            if ($type == 0 || $type == 4) {
                foreach ($regOverTaskIds as $regOverTaskId) {

                    $event = EventDetail::where('id', $taskId)
                        ->with('tier')
                        ->withCount(
                            ['joinEvents' => function ($q) {
                                $q->where('join_status', 'confirmed');
                            }]
                        )
                        ->first();

                    if ($event) {
                        if ($event->join_events_count != $event->tier->tierTeamSlot) {
                            $this->releaseToBeCapturedPaymentsAndDiscountCreate([$regOverTaskId], $taskId);

                            EventDetail::whereIn('id', $regOverTaskId)->update(['status' => 'ENDED']);
                            $deadlines = BracketDeadline::whereIn('event_details_id', $regOverTaskIds)->get();
                            $deadlinesPast = $deadlines->pluck("id");

                            Task::whereIn('taskable_id', $deadlinesPast)
                                ->where('taskable_type', BracketDeadline::class)
                                ->delete();

                            Task::whereIn('taskable_id', $regOverTaskId)
                                ->where('taskable_type', EventDetail::class)
                                ->delete();

                            BracketDeadline::whereIn('event_details_id', $regOverTaskIds)->delete();
                        }
                    }
                }
            }

            $now = Carbon::now();
            $this->logExit($taskId, $now);
        } catch (Exception $e) {
            $this->logError($taskId, $e);
        }
    }
};
