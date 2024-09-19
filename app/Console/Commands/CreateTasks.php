<?php

namespace App\Console\Commands;

use App\Models\EventDetail;
use App\Models\Task;
use App\Traits\TasksTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateTasks extends Command 
{
    use TasksTrait;
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
    protected $signature = 'tasks:create';

    protected $description = 'Create tasks in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::now();
        $commandName = 'tasks:create';
        $id = $this->logEntry($today, $commandName);
        try{
            $twoMonthsAgo = Carbon::now()->subMonths(2);

            DB::table('monitored_scheduled_tasks')->where('last_started_at', '<', $twoMonthsAgo)->delete();
            Task::where('created_at', '<', $twoMonthsAgo)->delete();
            DB::table(table: 'monitored_scheduled_task_log_items')->where('created_at', '<', $twoMonthsAgo)->delete();
            
            $twoWeeksAgo = $today->subWeeks(2);
            $todayDate = $today->toDateString();
            $todayTime = $today->toTimeString();

            $launchEvents = EventDetail::whereDate('sub_action_public_date', $todayDate)
                ->whereTime('sub_action_public_time', '<=', $todayDate)
                ->select(['id', 'sub_action_public_date', 'sub_action_public_time'])
                ->get();

            $endEvents = EventDetail::whereDate('endDate', $todayDate)
                ->select(['id', 'endDate'])
                ->get();

            $this->createTask($launchEvents, 'launch', $todayTime, $todayDate);
            $this->createTask($endEvents, 'ended', $todayTime, $todayDate);
                
            $now = Carbon::now();
            $this->logExit($id, $now);
        } catch (Exception $e) {
            $this->logError($id, $e->getMessage());
        }

    }

    private function createTask($eventList, $taskName, $actionTime, $actionDate)
    {
        $eventIds = $eventList->pluck('id')->toArray();
        $dateTime = date_create_from_format('Y-m-d H:i:s', $actionDate.' '.$actionTime);
        $tasksData = [];
        foreach ($eventIds as $eventId) {
            $tasksData[] = [
                'event_id' => $eventId,
                'task_name' => $taskName,
                'action_time' => $dateTime->format('Y-m-d H:i:s'),
            ];
        }

        Task::insert($tasksData);
    }

}
