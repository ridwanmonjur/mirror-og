<?php

namespace App\Services;

use App\Models\EventDetail;
use App\Models\Task;
use App\Traits\DeadlineTasksTrait;
use App\Traits\PrinterLoggerTrait;
use App\Models\BracketDeadline;
use App\Services\BracketDataService;
use App\Services\DataServiceFactory;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class DeadlineTaskService
{
    use DeadlineTasksTrait, PrinterLoggerTrait;

    protected $bracketDataService;

    protected $taskIdParent;

    protected $pythonApiUrl;

    public function __construct(BracketDataService $bracketDataService)
    {
        $this->bracketDataService = $bracketDataService;
        $now = Carbon::now();
        $firebaseConfig = Config::get('services.firebase');
        $disputeEnums = Config::get('constants.DISPUTE');
        $taskId = $this->logEntry('Respond tasks in the database', 'tasks:deadline {type=0 : The task type to process: 1=started, 2=ended, 3=org, 0=all} {--event_id= : Optional event ID to filter tasks}', '*/30 * * * *', $now);
        $this->taskIdParent = $taskId;
        $this->pythonApiUrl = config('cloud_function.url');
        $this->initializeDeadlineTasksTrait($bracketDataService, $firebaseConfig, $disputeEnums);
    }

    public function execute(int $type = 0, ?string $eventId = null): void
    {
        $now = Carbon::now();
        $taskId = $this->taskIdParent;
        try {
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
                        $this->callPythonStartedTasks($detail->id, $detail->matches, $gamesPerMatch);
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
                            $this->callPythonEndedTasks($detail->id, $detail->matches, $bracketInfo, $detail->tier->id, $isLeague, $gamesPerMatch);
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

                            $this->callPythonOrgTasks($detail->id, $detail->matches, $bracketInfo, $detail->tier->id, $isLeague, $gamesPerMatch);

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

    protected function callPythonStartedTasks($detailId, $matches, $gamesPerMatch = 3)
    {
        try {
            $matchesData = $matches->map(function ($match) {
                return [
                    'team1_position' => $match['team1_position'],
                    'team2_position' => $match['team2_position'],
                    'event_details_id' => $match['event_details_id'],
                ];
            })->toArray();

            $response = Http::timeout(30)->post($this->pythonApiUrl . '/deadline/started', [
                'detail_id' => $detailId,
                'matches' => $matchesData,
                'games_per_match' => $gamesPerMatch
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                Log::info('Python handleStartedTasks completed successfully', [
                    'processed_matches' => count($matchesData),
                    'response_status' => $responseData['status'] ?? 'unknown'
                ]);
            } else {
                Log::error('Python handleStartedTasks failed', [
                    'status' => $response->status(),
                    'error' => $response->body()
                ]);
                throw new Exception('Failed to process started tasks via Python API');
            }
        } catch (Exception $e) {
            Log::error('callPythonStartedTasks error: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function callPythonEndedTasks($detailId, $matches, $bracketInfo, $tierId, $isLeague = false, $gamesPerMatch = 3)
    {
        try {
            $matchesData = $matches->map(function ($match) {
                return [
                    'team1_position' => $match['team1_position'],
                    'team2_position' => $match['team2_position'],
                    'event_details_id' => $match['event_details_id'],
                    'stage_name' => $match['stage_name'],
                    'inner_stage_name' => $match['inner_stage_name'],
                    'order' => $match['order']
                ];
            })->toArray();

            $response = Http::timeout(30)->post($this->pythonApiUrl . '/deadline/ended', [
                'detail_id' => $detailId,
                'matches' => $matchesData,
                'bracket_info' => $bracketInfo,
                'tier_id' => $tierId,
                'is_league' => $isLeague,
                'games_per_match' => $gamesPerMatch
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                
                if (isset($responseData['next_stage_data']) && !empty($responseData['next_stage_data'])) {
                    foreach ($responseData['next_stage_data'] as $nextStageData) {
                        $this->resolveNextStage(
                            $nextStageData['bracket'],
                            $nextStageData['extra_bracket'],
                            $nextStageData['scores'],
                            $nextStageData['tier_id']
                        );
                    }
                }

                Log::info('Python handleEndedTasks completed successfully', [
                    'processed_matches' => count($matchesData),
                    'response_status' => $responseData['status'] ?? 'unknown'
                ]);
            } else {
                Log::error('Python handleEndedTasks failed', [
                    'status' => $response->status(),
                    'error' => $response->body()
                ]);
                throw new Exception('Failed to process ended tasks via Python API');
            }
        } catch (Exception $e) {
            Log::error('callPythonEndedTasks error: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function callPythonOrgTasks($detailId, $matches, $bracketInfo, $tierId, $isLeague = false, $gamesPerMatch = 3)
    {
        try {
            $matchesData = $matches->map(function ($match) {
                return [
                    'team1_position' => $match['team1_position'],
                    'team2_position' => $match['team2_position'],
                    'event_details_id' => $match['event_details_id'],
                    'stage_name' => $match['stage_name'],
                    'inner_stage_name' => $match['inner_stage_name'],
                    'order' => $match['order']
                ];
            })->toArray();

            $response = Http::timeout(30)->post($this->pythonApiUrl . '/deadline/org', [
                'detail_id' => $detailId,
                'matches' => $matchesData,
                'bracket_info' => $bracketInfo,
                'tier_id' => $tierId,
                'is_league' => $isLeague,
                'games_per_match' => $gamesPerMatch
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                
                if (isset($responseData['next_stage_data']) && !empty($responseData['next_stage_data'])) {
                    foreach ($responseData['next_stage_data'] as $nextStageData) {
                        $this->resolveNextStage(
                            $nextStageData['bracket'],
                            $nextStageData['extra_bracket'],
                            $nextStageData['scores'],
                            $nextStageData['tier_id']
                        );
                    }
                }

                Log::info('Python handleOrgTasks completed successfully', [
                    'processed_matches' => count($matchesData),
                    'response_status' => $responseData['status'] ?? 'unknown'
                ]);
            } else {
                Log::error('Python handleOrgTasks failed', [
                    'status' => $response->status(),
                    'error' => $response->body()
                ]);
                throw new Exception('Failed to process organizer tasks via Python API');
            }
        } catch (Exception $e) {
            Log::error('callPythonOrgTasks error: ' . $e->getMessage());
            throw $e;
        }
    }
}