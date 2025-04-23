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
use App\Models\Matches;

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
    protected $firestore;

    public function __construct(BracketDataService $bracketDataService)
    {
        parent::__construct(); 
        
        $this->bracketDataService = $bracketDataService;
        
        $factory = new \Kreait\Firebase\Factory();
        $credentialsPath = env('FIREBASE_CREDENTIALS', storage_path('firebase/firebase_credentials.json'));
        $this->firestore = $factory->withServiceAccount($credentialsPath)->createFirestore();
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

            $eventDetailsIds = $bracketDeadlines->pluck('event_details_id')->unique()->filter()->toArray();
            $bracketInfoMap = [];
            $bracketPlaceMap = [];
            $eventDetails = EventDetail::whereIn('id', $eventDetailsIds)
                ->with(['eventTier', 'matches'])
                ->get();

            foreach ($eventDetails as $detail) {
                // check if doesnt exist then fill only
                if (!isset($bracketInfoMap[$detail->id]) && isset($detail->eventTier?->tierTeamSlot)) {
                    $bracketInfoMap[$detail->id] = $this->bracketDataService->produceBrackets($detail->eventTier->tierTeamSlot, false, null, null);

                    $detail->matches?->foreach(function ($match) use ($detail, &$bracketPlaceMap) {
                        $bracketPlaceMap[$detail->id . '.' . $match->team1_position . '.' . $match->team2_position] = $match;
                    });
                }
            }

            foreach ($startedBracketDeadlines as $deadline) {
                $bracket = $bracketInfoMap[$deadline->event_details_id][$deadline->stage][$deadline->inner_stage_name] ?? null;
                if (!$bracket) {
                    continue;
                }

                $matchStatusPath = $bracket['team1_position'] . '.' . $bracket['team2_position'];
                $docRef = $this->firestore->database()->collection('event')->document($deadline->event_details_id)->collection('match_status')->document($matchStatusPath);
                $snapshot = $docRef->snapshot();

                if ($snapshot->exists()) {
                    $matchStatusData = $snapshot->data();
                    $docRef->update([['path' => 'matchStatus', 'value' => 'ONGOING']]);
                }
            }

            foreach ($endBracketDeadlines as $deadline) {
                $updateValues = [['path' => 'matchStatus', 'value' => 'ENDED']];

                $bracket = $bracketInfoMap[$deadline->event_details_id][$deadline->stage][$deadline->inner_stage_name] ?? null;
                if (!$bracket) {
                    continue;
                }

                $matchStatusPath = $bracket['team1_position'] . '.' . $bracket['team2_position'];
                $docRef = $this->firestore->database()->collection('event')->document($deadline->event_details_id)->collection('match_status')->document($matchStatusPath);
                $snapshot = $docRef->snapshot();

                if ($snapshot->exists()) {
                    $matchStatusData = $snapshot->data();
                    $scores = $matchStatusData['scores'] ?? null;

                    if (is_array($scores)) {
                        if ($scores[0] == $scores[1]) {
                            [ $realWinners, $scores, $updated ] = $this->equalizeScoreMissing($matchStatusData);
                            if ($updated && $scores[0] != $scores[1]) {
                                $updateValues = [
                                    ...$updateValues,
                                    ['path' => 'realWinners', 'value' => $realWinners],
                                    ['path' => 'scores', 'value' => $scores]
                                ];
                            }
                        } 

                        $this->resolveScores( $bracket, $scores, $deadline);
                    }

                    $docRef->update($updateValues);
                }
            }

            foreach ($orgBracketDeadlines as $deadline) {
                $updateValues = [];
                $bracket = $bracketInfoMap[$deadline->event_details_id][$deadline->stage][$deadline->inner_stage_name] ?? null;
                if (!$bracket) {
                    continue;
                }

                $matchStatusPath = $bracket['team1_position'] . '.' . $bracket['team2_position'];
                $docRef = $this->firestore->database()->collection('event')->document($deadline->event_details_id)->collection('match_status')->document($matchStatusPath);
                $snapshot = $docRef->snapshot();

                if ($snapshot->exists()) {
                    $matchStatusData = $snapshot->data();
                    $scores = $matchStatusData['scores'] ?? null;
                    if (is_array($scores)) {
                        if ($scores[0] == $scores[1]) {
                            [ $realWinners, $scores, $updated ] = $this->equalizeScoreMissing($matchStatusData);
                            if ($updated) {
                                if ( $scores[0] == $scores[1]) {
                                    $updateValues = [
                                        ...$updateValues,
                                        ['path' => 'realWinners', 'value' => $realWinners],
                                        ['path' => 'scores', 'value' => $scores]
                                    ];
                                   
                                   
                                } else {
                                    $updateValues = [
                                        ...$updateValues,
                                       // todo something for new winner chosen at random
                                    ];
                                }

                                $docRef->update($updateValues);

                            }
                        }
                        
                        $this->resolveScores( $bracket, $scores, $deadline);
                    }
                }
            }

            $now = Carbon::now();
            $this->logExit($taskId, $now);
            return $tasks;
        } catch (Exception $e) {
            $this->logError($taskId, $e);
        }
    }

    
}
