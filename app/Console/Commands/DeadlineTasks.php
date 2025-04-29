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
    protected $signature = 'tasks:deadline';

    protected $description = 'Respond tasks in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->getTodayTasksByName();
    }

    protected $bracketDataService;

     
    public function __construct(BracketDataService $bracketDataService)
    {
        parent::__construct();
        
        $this->initializeDeadlineTasksTrait($bracketDataService);
    }


    public function getTodayTasksByName()
    {
        $now = Carbon::now();
        $taskId = $this->logEntry($this->description, $this->signature, '*/30 * * * *', $now);
        try {
            $today = Carbon::today();
            $tasks = Task::whereDate('action_time', $today)->where('action_time', '>=', $now)->where('action_time', '<=', $now->addMinutes(30))->get();

            $startedTaskIds = []; $endTaskIds = []; $orgTaskIds = [];
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
            
            $allTaskIds = array_merge($startedTaskIds, $endTaskIds, $orgTaskIds);
            $bracketDeadlines = BracketDeadline::whereIn('id', $allTaskIds)->get();

            $startedBracketDeadlines = $bracketDeadlines->whereIn('id', $startedTaskIds);
            $endBracketDeadlines = $bracketDeadlines->whereIn('id', $endTaskIds);
            $orgBracketDeadlines = $bracketDeadlines->whereIn('id', $orgTaskIds);
            
            $startDetails = EventDetail::whereIn('id', $startedTaskIds)
                ->withEventTierAndFilteredMatches($startedBracketDeadlines)
                ->get();

            $orgDetails = EventDetail::whereIn('id', $orgTaskIds)
                ->withEventTierAndFilteredMatches($orgBracketDeadlines)
                ->get();

            $endDetails = EventDetail::whereIn('id', $endTaskIds)
                ->withEventTierAndFilteredMatches($endBracketDeadlines)
                ->get();
            
            foreach ($startDetails as $detail) {
                $this->handleStartedTasks($detail->matches);
            }

            foreach ($endDetails as $detail) {
                $bracketInfo = $this->bracketDataService->produceBrackets($detail->eventTier->tierTeamSlot, false, null, null);
                $this->handleEndedTasks($detail->matches, $bracketInfo);
            }

            foreach ($orgDetails as $detail) {
                $bracketInfo = $this->bracketDataService->produceBrackets($detail->eventTier->tierTeamSlot, false, null, null);
                $this->handleOrg($detail->matches, $bracketInfo);
            }

            $now = Carbon::now();
            $this->logExit($taskId, $now);
            return $tasks;
        } catch (Exception $e) {
            $this->logError($taskId, $e);
        }
    }

}
