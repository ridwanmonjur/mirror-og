<?php

namespace App\Console\Commands;

use App\Models\EventDetail;
use App\Models\Task;
use App\Console\Commands\PrinterLoggerTrait;
use App\Models\BracketDeadline;
use App\Models\JoinEvent;
use App\Services\BracketDataService;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
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

    public function __construct(BracketDataService $bracketDataService)
    {
        parent::__construct();

        $this->initializeDeadlineTasksTrait($bracketDataService);
    }

    public function handle()
    {
        $now = Carbon::now();
        $taskId = $this->logEntry($this->description, $this->signature, '*/30 * * * *', $now);
        try {
            $today = Carbon::today();
            $type = (int) $this->argument('type');
            $eventId = $this->option('event_id');
            $tasks = null;
            $startedTaskIds = [];
            $endTaskIds = [];
            $orgTaskIds = [];

            if ($type === 0) {
                $tasks = Task::whereDate('action_time', $today)->where('action_time', '>=', $now)->where('action_time', '<=', $now->addMinutes(30))->get();
            } else {
                $eventIdInt = (int) $eventId;
                $tasks = Task::where('taskable_id', $eventIdInt)->get();
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
                $startDetails = EventDetail::whereIn('id', $startedTaskIds)
                    ->withEventTierAndFilteredMatches($startedBracketDeadlines)
                    ->get();
                foreach ($startDetails as $detail) {
                    $this->handleStartedTasks($detail->matches);
                }
            }

            if ($type == 0 || $type == 2) {
                $endBracketDeadlines = BracketDeadline::whereIn('id', $endTaskIds)->get();
                $endDetails = EventDetail::whereIn('id', $endTaskIds)
                    ->withEventTierAndFilteredMatches($endBracketDeadlines)
                    ->get();
                foreach ($endDetails as $detail) {
                    $bracketInfo = $this->bracketDataService->produceBrackets($detail->eventTier->tierTeamSlot, false, null, null);
                    $this->handleEndedTasks($detail->matches, $bracketInfo, $detail->eventTier->id);
                }
            }

            if ($type == 0 || $type == 3) {
                $orgBracketDeadlines = BracketDeadline::whereIn('id', $orgTaskIds)->get();
                $orgDetails = EventDetail::whereIn('id', $orgTaskIds)
                    ->withEventTierAndFilteredMatches($orgBracketDeadlines)
                    ->get();
                foreach ($orgDetails as $detail) {
                    $bracketInfo = $this->bracketDataService->produceBrackets($detail->eventTier->tierTeamSlot, false, null, null);
                    $this->handleOrgTasks($detail->matches, $bracketInfo, $detail->eventTier->id);
                }
            }

            $now = Carbon::now();
            $this->logExit($taskId, $now);
        } catch (Exception $e) {
            $this->logError($taskId, $e);
        }
    }
}