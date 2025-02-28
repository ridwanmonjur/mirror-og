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

    
};
