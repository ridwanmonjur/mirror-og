<?php

namespace App\Console\Commands;

use App\Models\EventDetail;
use App\Models\Task;
use App\Console\Commands\PrinterLoggerTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateTasks extends Command 
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
    protected $signature = 'tasks:create';

    protected $description = 'Create tasks in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::now();
        $commandName = 'tasks:create';
        $id = $this->logEntry('Generate Tasks', $commandName, '0 0 * *', $today, $commandName);
        try{
            $twelveMonthsAgo = Carbon::now()->subMonths(12);
            DB::table(table: 'monitored_scheduled_task_log_items')->where('created_at', '<', $twelveMonthsAgo)->delete();
            DB::table('monitored_scheduled_tasks')->where('last_started_at', '<', $twelveMonthsAgo)->delete();
            Task::where('created_at', '<', $twelveMonthsAgo)->delete();
            
            $todayDate = $today->toDateString();
            $todayTime = $today->toTimeString();

            // $launchEvents = EventDetail::whereDate('sub_action_public_date', $todayDate)
            //     ->whereTime('sub_action_public_time', '<=', $todayDate)
            //     ->select(['id', 'sub_action_public_date', 'sub_action_public_time'])
            //     ->get();

            // $startEvents = EventDetail::whereDate('startDate', $todayDate)
            //     ->select(['id', 'startDate'])
            //     ->get();

            // $endEvents = EventDetail::whereDate('endDate', $todayDate)
            //     ->select(['id', 'endDate'])
            //     ->get();

            $launchEvents = EventDetail::select(['id', 'sub_action_public_date', 'sub_action_public_time'])
                ->get();

            $startEvents = EventDetail::select(['id', 'startDate'])
                ->get();

            $endEvents = EventDetail::select(['id', 'endDate'])
                ->get();


            $this->createTask($launchEvents, 'live', $todayTime, $todayDate);
            $this->createTask($endEvents, 'ended', $todayTime, $todayDate);
            $this->createTask($startEvents, 'started', $todayTime, $todayDate);

            $now = Carbon::now();
            $this->logExit($id, $now);
        } catch (Exception $e) {
            $this->logError($id, $e);
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
