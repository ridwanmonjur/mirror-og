<?php

namespace App\Console\Commands;

use App\Models\EventDetail;
use App\Models\Task;
use App\Console\Traits\PrinterLoggerTrait;
use App\Console\Traits\DeadlineTasksTrait;
use App\Models\BracketDeadline;
use App\Models\JoinEvent;
use App\Services\BracketDataService;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class DeadlineTasks extends Command
{
    use DeadlineTasksTrait, PrinterLoggerTrait;
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
    protected $signature = 'tasks:deadline {type=0 : The task type to process: 1=started, 2=ended, 3=org, 0=all} {--event_id= : Optional event ID to filter tasks}';

    protected $description = 'Respond tasks in the database';

    protected $bracketDataService;

    protected $taskIdParent;

    public function __construct(BracketDataService $bracketDataService)
    {
        parent::__construct();
        $now = Carbon::now();
        $firebaseConfig = Config::get('services.firebase');
        $disputeEnums = Config::get('constants.DISPUTE');
        $taskId = $this->logEntry($this->description, $this->signature, '*/30 * * * *', $now);
        $this->taskIdParent = $taskId;
        $this->initializeDeadlineTasksTrait($bracketDataService, $firebaseConfig, $disputeEnums);
    }

    public function handle()
    {
        $now = Carbon::now();
        $taskId = $this->taskIdParent;
        try {
            $type = (int) $this->argument('type');
            $eventId = $this->option('event_id');
            $tasks = null;
            $startedTaskIds = [];
            $endTaskIds = [];
            $orgTaskIds = [];

            if ($type === 0) {
                $tasks = Task::where('taskable_type', 'Deadline')
                    ->where('action_time', '>=', $now->copy()->subMinutes(5))
                    ->where('action_time', '<=', $now->copy()->addMinutes(29))
                    ->get();
            } else {
                $eventIdInt = (int) $eventId;
                $deadlines = BracketDeadline::where('event_details_id', $eventIdInt)->get();
                $deadlinesPast = $deadlines->pluck('id');
                $tasks = Task::where('taskable_id', $deadlinesPast)->where('taskable_type', 'Deadline')->get();
            }

            foreach ($tasks as $task) {
                switch ($task->task_name) {
                    case 'start_report':
                        $startedTaskIds[] = $task->taskable_id;
                        break;
                    case 'end_report':
                        $endTaskIds[] = $task->taskable_id;
                        break;
                    case 'org_report':
                        $orgTaskIds[] = $task->taskable_id;
                        break;
                }
            }

            if ($type == 0 || $type == 1) {
                $startedBracketDeadlines = BracketDeadline::whereIn('id', $startedTaskIds)->get();
                $startDetails = EventDetail::whereIn('id', $startedTaskIds)->withEventTierAndFilteredMatches($startedBracketDeadlines)->get();
                foreach ($startDetails as $detail) {
                    try {
                        $this->handleStartedTasks($detail->matches);
                    } catch (Exception $e) {
                        $this->logError($taskId, $e);
                    }
                }
            }

            if ($type == 0 || $type == 2) {
                $endBracketDeadlines = BracketDeadline::whereIn('id', $endTaskIds)->get();
                $endDetails = EventDetail::whereIn('id', $endTaskIds)->withEventTierAndFilteredMatches($endBracketDeadlines)->get();
                foreach ($endDetails as $detail) {
                    try {
                        $bracketInfo = $this->bracketDataService->produceBrackets($detail->tier->tierTeamSlot, false, null, null, 'all');
                        $this->handleEndedTasks($detail->matches, $bracketInfo, $detail->tier->id);
                    } catch (Exception $e) {
                        $this->logError($taskId, $e);
                    }
                }
            }

            if ($type == 0 || $type == 3) {
                $orgBracketDeadlines = BracketDeadline::whereIn('id', $orgTaskIds)->get();
                $orgDetails = EventDetail::whereIn('id', $orgTaskIds)->withEventTierAndFilteredMatches($orgBracketDeadlines)->get();
                foreach ($orgDetails as $detail) {
                    try {
                        $bracketInfo = $this->bracketDataService->produceBrackets($detail->tier->tierTeamSlot, false, null, null, 'all');
                        $this->handleOrgTasks($detail->matches, $bracketInfo, $detail->tier->id);
                    } catch (Exception $e) {
                        $this->logError($taskId, $e);
                    }
                }
            }

            $now = Carbon::now();
            $this->logExit($taskId, $now);
        } catch (Exception $e) {
            $this->logError($taskId, $e);
        }
    }
}
