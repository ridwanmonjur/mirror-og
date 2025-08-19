<?php

namespace App\Console\Commands;

use App\Models\EventDetail;
use App\Models\Task;
use App\Console\Traits\PrinterLoggerTrait;
use App\Console\Traits\DeadlineTasksTrait;
use App\Models\BracketDeadline;
use App\Services\BracketDataService;
use App\Services\DataServiceFactory;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
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
                $tasks = Task::whereIn('taskable_id', $deadlinesPast)->where('taskable_type', 'Deadline')->get();
            }

            // dd($tasks);

            $startedEventIds = [];
            $endEventIds = [];
            $orgEventIds = [];

            foreach ($tasks as $task) {
                switch ($task->task_name) {
                    case 'start_report':
                        $startedTaskIds[] = $task->taskable_id;
                        $startedEventIds[$task->event_id] = true;
                        break;
                    case 'end_report':
                        $endTaskIds[] = $task->taskable_id;
                        $endEventIds[$task->event_id] = true;
                        break;
                    case 'org_report':
                        $orgTaskIds[] = $task->taskable_id;
                        $orgEventIds[$task->event_id] = true;
                        break;
                }
            }

            $startedEventIds = array_keys($startedEventIds);
            $endEventIds = array_keys($endEventIds);
            $orgEventIds = array_keys($orgEventIds);

            if ($type == 0 || $type == 1) {
                $startedBracketDeadlines = BracketDeadline::whereIn('id', $startedTaskIds)->get();
                $startDetails = EventDetail::whereIn('id', $startedEventIds)->withEventTierAndFilteredMatches($startedBracketDeadlines)->with('game')->get();
                foreach ($startDetails as $detail) {
                    try {
                        $gamesPerMatch = $detail->game && $detail->game->games_per_match ? $detail->game->games_per_match : 3;
                        $this->handleStartedTasks($detail->matches, $gamesPerMatch);
                    } catch (Exception $e) {
                        $this->logError($taskId, $e);
                    }
                }
            }

            if ($type == 0 || $type == 2) {
                $endBracketDeadlines = BracketDeadline::whereIn('id', $endTaskIds)->get();
                $endDetails = EventDetail::whereIn('id', $endEventIds)->withEventTierAndFilteredMatches($endBracketDeadlines)->with(['type', 'game'])->get();
                foreach ($endDetails as $detail) {
                    try {
                        $isLeague = $detail->type && $detail->type->eventType === 'League';
                        $gamesPerMatch = $detail->game && $detail->game->games_per_match ? $detail->game->games_per_match : 3;
                        $eventType = $isLeague ? 'League' : 'Tournament';
                        $dataService = DataServiceFactory::create($eventType);
                        if ($detail->type?->eventType && $detail->tier?->tierTeamSlot) {
                            $cacheKey = "{$detail->type->eventType}_{$detail->id}_{$detail->tier->tierTeamSlot}_all_0";
                            
                            $bracketInfo = Cache::remember($cacheKey, config('cache.ttl', 3600), function () use (
                                $dataService, $detail
                            ) {
                                return $dataService->produceBrackets($detail->tier->tierTeamSlot, false, null, null, 'all');
                            });

                            Log::info($bracketInfo);
                            $this->handleEndedTasks($detail->matches, $bracketInfo, $detail->tier->id, $isLeague, $gamesPerMatch);
                        } 

                    } catch (Exception $e) {
                        $this->logError($taskId, $e);
                    }
                }
            }

            if ($type == 0 || $type == 3) {
                $orgBracketDeadlines = BracketDeadline::whereIn('id', $orgTaskIds)->get();
                $orgDetails = EventDetail::whereIn('id', $orgEventIds)->withEventTierAndFilteredMatches($orgBracketDeadlines)->with(['type', 'game'])->get();
                foreach ($orgDetails as $detail) {
                    try {
                        $isLeague = $detail->type && $detail->type->eventType === 'League';
                        $gamesPerMatch = $detail->game && $detail->game->games_per_match ? $detail->game->games_per_match : 3;
                        $eventType = $isLeague ? 'League' : 'Tournament';
                        $dataService = DataServiceFactory::create($eventType);
                        if ($detail->type?->eventType && $detail->tier?->tierTeamSlot) {
                            $cacheKey = "{$detail->type->eventType}_{$detail->id}_{$detail->tier->tierTeamSlot}_all_0";
                            
                            $bracketInfo = Cache::remember($cacheKey, config('cache.ttl', 3600), function () use (
                                $dataService, $detail
                            ) {
                                return $dataService->produceBrackets($detail->tier->tierTeamSlot, false, null, null, 'all');
                            });

                            $this->handleOrgTasks($detail->matches, $bracketInfo, $detail->tier->id, $isLeague, $gamesPerMatch);

                        }
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
