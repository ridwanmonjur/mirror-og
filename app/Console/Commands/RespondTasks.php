<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Console\Commands\PrinterLoggerTrait;
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
        $taskId = $this->logEntry($this->description, $this->signature, '*/30 * * * *', $now);
        try {
            $today = Carbon::today();
            $tasks = Task
                ::whereDate('action_time', $today)
                ->where('action_time', '>=', $now)
                ->where('action_time', '<=', $now->addMinutes(30))
                ->get();

            $endedTaskIds= $liveTaskIds = $startedTaskIds = [];
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

            $with = [
                'roster:id,team_id,user_id',
                'members.user:id,name',
                'eventDetails.user:id,name',
                'eventDetails.tier:id,eventTier',
            ];

            $startedEvents = JoinEvent::whereIn('event_details_id', $startedTaskIds)
                ->where('join_status', 'confirmed')
                ->with([
                    'eventDetails:id,eventName,startDate,startTime,event_tier_id,user_id',
                    ...$with,
                ])
                ->get();

            $liveEvents = JoinEvent::whereIn('event_details_id', $liveTaskIds)
                ->where('join_status', 'confirmed')
                ->with([
                    'eventDetails:id,eventName,sub_action_public_date,sub_action_public_time,event_tier_id,user_id',
                    ...$with,
                ])
                ->get();

            $endedEvents = JoinEvent::whereIn('event_details_id', $endedTaskIds)
                ->where('join_status', 'confirmed')
                ->with([
                    'eventDetails:id,eventName,endDate,endTime,event_tier_id,user_id',
                    'position',
                    'team:id,teamName,teamBanner',
                    ...$with,
                ])
                ->get();

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

            $this->capturePayments($startedTaskIds, $taskId);

            $now = Carbon::now();
            $this->logExit($taskId, $now);
            return $tasks;
        } catch (Exception $e) {
            $this->logError($taskId, $e);
        }
    }


};
