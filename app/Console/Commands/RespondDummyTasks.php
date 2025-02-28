<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Console\Commands\PrinterLoggerTrait;
use App\Models\JoinEvent;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class RespondDummyTasks extends Command
{
    use PrinterLoggerTrait, RespondTrait;

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
    protected $signature = 'tasks:dummyRespond';

    protected $description = 'Dummy task respond in the database';


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
            $tasks = Task::all();
            $endedTaskIds= $liveTaskIds = $startedTaskIds = [];
            foreach ($tasks as $task) {
                switch ($task->task_name) {
                    case 'ended':
                        $endedTaskIds[] = $task->event_id;
                        break;
                    case 'live':
                        $liveTaskIds[] = $task->event_id;
                        break;
                    case 'started':
                        $startedTaskIds[] = $task->event_id;
                        break;
                }
            }

            $startedEvents = JoinEvent::whereIn('event_details_id', $startedTaskIds)
                ->with('members', 'members.user', 'eventDetails', 'eventDetails.user')
                ->get();

            $liveEvents = JoinEvent::whereIn('event_details_id', $liveTaskIds)
                ->with('members', 'members.user', 'eventDetails', 'eventDetails.user')
                ->get();
               
            $endedEvents = JoinEvent::whereIn('event_details_id', $endedTaskIds)
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

   
};
