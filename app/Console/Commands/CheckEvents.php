<?php

namespace App\Console\Commands;

use App\Models\EventDetail;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Console\Command;

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

        $today = Carbon::now();
        $todayDate = $today->toDateString();

        $launchEvents = EventDetail::whereDate('launch_date', $todayDate)
            ->whereTime('launch_time', '<=', $today->toTimeString())
            ->select(['id'])
            ->get();

        $endEvents = EventDetail::whereDate('end_date', $todayDate)
            ->select(['id'])
            ->get();

        $twoWeeksAgo = $today->subWeeks(2);
        $registrationOverEvents = EventDetail::where('launch_date', '<=', $twoWeeksAgo)
            ->get();
        
        $this->createTask($launchEvents, 'launch', $today->toTimeString(), $today->toDateString());
        $this->createTask($endEvents, 'ended', $today->toTimeString(), $today->toDateString());
        $this->createTask($registrationOverEvents, 'registration_over', $today->toTimeString(), $today->toDateString());
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
