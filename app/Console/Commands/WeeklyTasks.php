<?php

namespace App\Console\Commands;

use App\Models\NotificationCounter;
use App\Models\Task;
use App\Console\Traits\PrinterLoggerTrait;
use App\Models\NotifcationsUser;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class WeeklyTasks extends Command
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
    protected $signature = 'tasks:weekly';

    protected $description = 'Weekly tasks in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::now();
        $id = $this->logEntry($this->description, $this->signature, '0 0 * *', $today);
        try {
            $weekAgo = Carbon::now()->subDays(7);
            $monthAgo = Carbon::now()->subDays(30);
            DB::table(table: 'monitored_scheduled_task_log_items')->where('created_at', '<', $weekAgo)->delete();
            DB::table('monitored_scheduled_tasks')->where('last_started_at', '<', $weekAgo)->delete();
            NotifcationsUser::where('created_at', '<', $weekAgo)->delete();
            Task::where('action_time', '<=', $monthAgo)->delete();
            NotificationCounter::resetNegativeCounts();
            Task::where('created_at', '<', $weekAgo)->delete();
            $now = Carbon::now();
            $this->logExit($id, $now);
        } catch (Exception $e) {
            $this->logError(null, $e);
        }
    }
}
