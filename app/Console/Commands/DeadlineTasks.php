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
    use PrinterLoggerTrait, RespondTaksTrait;

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

    private $bracketDataService;
    private $firestore;

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

            $startedTaskIds = $sentTaskIds = [];
            foreach ($tasks as $task) {
                switch ($task->task_name) {
                    case 'start_report':
                        $startedTaskIds[] = $task->taskable_id;
                        break;
                    case 'end_report':
                        $sentTaskIds[] = $task->taskable_id;
                        break;
                }
            }

            $bracketDeadlines = BracketDeadline::whereIn('id', $sentTaskIds)->get();
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

            foreach ($bracketDeadlines as $deadline) {
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

                    $docRef->update([['path' => 'matchStatus', 'value' => 'ENDED']]);

                    if (is_array($scores) && count($scores) >= 2) {
                        if ($scores[0] > $scores[1]) {
                            // Team 1 won
                            $match = Matches::updateOrCreate(
                                [
                                    'event_details_id' => $deadline->event_details_id,
                                    'team1_position' => $bracket['winner_next_position'],
                                ],
                                [
                                    'winner_id' => $bracket['team1_id'] ?? null,
                                    'loser_id' => $bracket['team2_id'] ?? null,
                                    'team0' => $bracket['winner_next_position'],
                                    'loser_next_position' => $bracket['loser_next_position'],
                                ],
                            );

                            Matches::updateOrCreate(
                                [
                                    'event_details_id' => $deadline->event_details_id,
                                    'team1_position' => $bracket['team0_position'],
                                ],
                                [
                                    'winner_id' => $bracket['team1_id'] ?? null,
                                    'loser_id' => $bracket['team2_id'] ?? null,
                                    'winner_next_position' => $bracket['winner_next_position'],
                                    'loser_next_position' => $bracket['loser_next_position'],
                                ],
                            );
                        } else {
                            // Team 2 won
                            Matches::updateOrCreate(
                                [
                                    'event_details_id' => $deadline->event_details_id,
                                    'team1_position' => $bracket['team1_position'],
                                ],
                                [
                                    'winner_id' => $bracket['team2_id'] ?? null,
                                    'loser_id' => $bracket['team1_id'] ?? null,
                                    'winner_next_position' => $bracket['winner_next_position'],
                                    'loser_next_position' => $bracket['loser_next_position'],
                                ],
                            );
                        }
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
