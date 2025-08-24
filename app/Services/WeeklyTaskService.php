<?php

namespace App\Services;

use App\Models\NotificationCounter;
use App\Models\Task;
use App\Traits\PrinterLoggerTrait;
use App\Models\NotifcationsUser;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class WeeklyTaskService
{
    use PrinterLoggerTrait;

    public function execute(): void
    {
        $today = Carbon::now();
        $taskId = $this->logEntry('Weekly tasks in the database', 'tasks:weekly', '0 0 * *', $today);
        try {
            $weekAgo = Carbon::now()->subDays(7);
            $monthAgo = Carbon::now()->copy()->subDays(29);
            
            DB::table('monitored_scheduled_task_log_items')
                ->whereIn('monitored_scheduled_task_id', function($query) use ($weekAgo) {
                    $query->select('id')
                          ->from('monitored_scheduled_tasks')
                          ->where('last_started_at', '<', $weekAgo);
                })
                ->delete();
                
            DB::table('monitored_scheduled_tasks')->where('last_started_at', '<', $weekAgo)->delete();
            NotifcationsUser::where('created_at', '<', $weekAgo)->delete();
            Task::where('action_time', '<=', $monthAgo)->delete();
            NotificationCounter::resetNegativeCounts();
            Task::where('created_at', '<', $weekAgo)->delete();
            $now = Carbon::now();
            $this->logExit($taskId, $now);
        } catch (Exception $e) {
            $this->logError($taskId, $e);
        }
    }
}