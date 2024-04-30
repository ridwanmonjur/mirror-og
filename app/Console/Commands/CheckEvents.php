<?php

namespace App\Console\Commands;

use App\Models\EventDetail;
use App\Models\Task;
use Carbon\Carbon;
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
        $twoMonthsAgo = Carbon::now()->subMonths(2);
        Task::where('created_at', '<', $twoMonthsAgo)->delete();
        DB::table('monitored_scheduled_task_log_items')->where('created_at', '<', $twoMonthsAgo)->delete();

        $today = Carbon::now();
        $twoWeeksAgo = $today->subWeeks(2);
        $todayDate = $today->toDateString();
        $todayTime = $today->toTimeString();

        $launchEvents = EventDetail::whereDate('launch_date', $todayDate)
            ->whereTime('launch_time', '<=', $todayDate)
            ->select(['id'])
            ->get();

        $endEvents = EventDetail::whereDate('end_date', $todayDate)
            ->select(['id'])
            ->get();

        $registrationOverEvents = EventDetail::where('launch_date', '<=', $twoWeeksAgo)
            ->get();
        
        $this->createTask($launchEvents, 'launch', $todayTime, $todayDate);
        $this->createTask($endEvents, 'ended', $todayTime, $todayDate);
        $this->createTask($registrationOverEvents, 'registration_over', $todayTime, $todayDate);
    }

    private function createTask($eventList, $taskName, $actionTime, $actionDate)
    {
        $eventIds = $eventList->pluck('id')->toArray();
        $dateTime = date_create_from_format('Y-m-d H:i:s', $actionDate . ' ' . $actionTime);
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
