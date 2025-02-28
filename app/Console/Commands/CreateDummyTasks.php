<?php

namespace App\Console\Commands;

use App\Models\EventDetail;
use App\Models\Task;
use App\Console\Commands\PrinterLoggerTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateDummyTasks extends Command 
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
    protected $signature = 'tasks:dummyCreate';

    protected $description = 'Create dummy tasks in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::now();
        $id = $this->logEntry($this->description, $this->signature, '0 0 * *', $today);
        try{
            $todayDate = $today->toDateString();
            $todayTime = $today->toTimeString();

            $launchEvents = EventDetail
                ::whereNotIn('status', ['DRAFT', 'PENDING', 'PREVIEW'])
                ->whereNotNull('sub_action_public_date')
                ->select(['id', 'sub_action_public_date'])
                ->get();

            $startEvents = EventDetail
                ::whereNotIn('status', ['DRAFT', 'PENDING', 'PREVIEW'])
                ->select(['id', 'startDate'])
                ->get();

            $endEvents = EventDetail                
                ::whereNotIn('status', ['DRAFT', 'PENDING', 'PREVIEW'])
                ->select(['id', 'endDate'])
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
        $now = now();

        $eventIds = $eventList->pluck('id')->toArray();
        $dateTime = date_create_from_format('Y-m-d H:i:s', $actionDate.' '.$actionTime);
        $tasksData = [];
        foreach ($eventIds as $eventId) {
            $tasksData[] = [
                'event_id' => $eventId,
                'task_name' => $taskName,
                'action_time' => $dateTime->format('Y-m-d H:i:s'),
                'created_at' => $now,
            ];
        }

        Task::insert($tasksData);
    }

}
