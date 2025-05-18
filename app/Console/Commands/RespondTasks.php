<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Console\Traits\PrinterLoggerTrait;
use App\Console\Traits\RespondTaksTrait;
use App\Models\EventDetail;
use App\Models\JoinEvent;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class RespondTasks extends Command
{
    use PrinterLoggerTrait, RespondTaksTrait;

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
    protected $signature = 'tasks:respond {type=0 : The task type to process: 1=started, 2=live, 3=ended, 0=all} {--event_id= : Optional event ID to filter tasks}';

    protected $description = 'Respond tasks in the database';


    /**
     * Execute the console command.
     */
    public function handle() {
        $now = Carbon::now();
        $type = (int) $this->argument('type');
        $eventId = $this->option('event_id');

        $taskId = $this->logEntry($this->description, $this->signature, '*/30 * * * *', $now);
        try {
            $today = Carbon::today();
            $endedTaskIds= $liveTaskIds = $startedTaskIds = [];

            if ($type === 0) {
                $tasks = Task
                    ::whereDate('action_time', $today)
                    ->where('action_time', '>=', $now)
                    ->where('action_time', '<=', $now->addMinutes(30))
                    ->get();
            } else {
                $eventIdInt = (int) $eventId;
                $tasks = Task::where('taskable_id', $eventIdInt)->get();   
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
                $startedEvents = JoinEvent::whereIn('event_details_id', $startedTaskIds)
                    ->where('join_status', 'confirmed')
                    ->with([
                        'eventDetails:id,eventName,startDate,startTime,event_tier_id,user_id',
                        ...$with,
                    ])
                    ->get();


                EventDetail::whereIn('id', $startedTaskIds)->update(['status' => 'ONGOING']);
                $this->capturePayments($startedTaskIds, $taskId);
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
                    $processedEndedNotifications, $logs, $memberIdList
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

            $now = Carbon::now();
            $this->logExit($taskId, $now);
        } catch (Exception $e) {
            $this->logError($taskId, $e);
        }
    }


};
