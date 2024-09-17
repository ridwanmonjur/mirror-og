<?php

namespace App\Console\Commands;

use App\Models\EventDetail;
use App\Models\Task;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckEvents extends Command
{
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
    protected $signature = 'events:check';

    protected $description = 'Check events in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::now();
        $taskName = 'events:check';
        $id = $this->logEntry($today, $taskName);
        try{
            $twoMonthsAgo = Carbon::now()->subMonths(2);

            DB::table('monitored_scheduled_tasks')->where('last_started_at', '<', $twoMonthsAgo)->delete();
            Task::where('created_at', '<', $twoMonthsAgo)->delete();
            DB::table(table: 'monitored_scheduled_task_log_items')->where('created_at', '<', $twoMonthsAgo)->delete();
            
            $id = $this->logEntry($today, $taskName);

            $twoWeeksAgo = $today->subWeeks(2);
            $todayDate = $today->toDateString();
            $todayTime = $today->toTimeString();

            $launchEvents = EventDetail::whereDate('launch_date', $todayDate)
                ->whereTime('launch_time', '<=', $todayDate)
                ->select(['id', 'launch_time', 'launch_time'])
                ->get();

            $endEvents = EventDetail::whereDate('end_date', $todayDate)
                ->select(['id', 'end_date'])
                ->get();

            $registrationOverEvents = EventDetail::where('launch_date', '<=', $twoWeeksAgo)
                ->select(['id', 'launch_date'])
                ->get();

            $this->createTask($launchEvents, 'launch', $todayTime, $todayDate);
            $this->createTask($endEvents, 'ended', $todayTime, $todayDate);
            $this->createTask($registrationOverEvents, 'registration_over', $todayTime, $todayDate);
                
            $now = Carbon::now();
            $this->logExit($id, $now, $taskName);
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

    private function logEntry($today, $taskName) {
        $id = DB::transaction(function () use ($taskName, $today) {
            $record = DB::table('monitored_scheduled_tasks')
                ->where('name', $taskName)
                ->where('type', 'Daily cron check')
                ->whereDate('created_at', $today->toDateString())
                ->first();
        
            if ($record) {
                DB::table('monitored_scheduled_tasks')
                    ->where('id', $record->id)
                    ->update([
                        'last_started_at' => $today,
                        'updated_at' => $today,
                    ]);
                return $record->id;
            } else {
                return DB::table('monitored_scheduled_tasks')->insertGetId([
                    'name' => $taskName,
                    'type' => 'Daily cron check',
                    'created_at' => $today,
                    'cron_expression' => '0 0 * * *',
                    'last_started_at' => $today,
                    'updated_at' => $today,
                ]);
            }
        });
        return $id;
    }

    private function logExit($id, $now, $task) {
        DB::table('monitored_scheduled_tasks')
            ->where('id', $id)
            ->update(
                [
                    'last_started_at' => $now,
                    'updated_at' => $now,
                ]
            );
    }

    private function logError ($id, $errorMsg) {
        DB::table('monitored_scheduled_task_log_items')->insert([
            'monitored_scheduled_task_id' => $id, 
            'type' => "Error", 
            'logs' => $errorMsg, 
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
