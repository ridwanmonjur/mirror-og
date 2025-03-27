<?php

namespace App\Console\Commands;

use App\Models\EventDetail;
use App\Models\Task;
use App\Console\Commands\PrinterLoggerTrait;
use App\Models\NotifcationsUser;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MonthlyTasks extends Command 
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
    protected $signature = 'tasks:monthly';

    protected $description = 'Monthly tasks in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::now();
        $id = $this->logEntry($this->description, $this->signature, '0 0 * *', $today);
        try{
            $twelveMonthsAgo = Carbon::now()->subMonths(12);
            DB::table(table: 'monitored_scheduled_task_log_items')->where('created_at', '<', $twelveMonthsAgo)->delete();
            DB::table('monitored_scheduled_tasks')->where('last_started_at', '<', $twelveMonthsAgo)->delete();
            NotifcationsUser::where('created_at', '<', $twelveMonthsAgo)
                ->where('type', 'social')->delete();
            Task::where('created_at', '<', $twelveMonthsAgo)->delete();
            $now = Carbon::now();
            $this->logExit($id, $now);
        } catch (Exception $e) {
            $this->logError($id, $e);
        }

    }
}
