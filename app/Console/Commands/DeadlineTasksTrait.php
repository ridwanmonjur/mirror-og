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
use Illuminate\Database\Eloquent\Collection;

trait DeadlineTasksTrait
{
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

    public function resolveNextStage(array $bracket, array $scores, BracketDeadline $deadline) {
        $winner_id =  null;
        $loser_id =  null;

        if ($scores[0] == $scores[1]) {
            $winner_chosen = rand(0, 1);
            if ($winner_chosen == 0) {
                $winner_id = $bracket['team1_id'] ?? null;
                $loser_id = $bracket['team2_id'] ?? null;
            } else {
                $winner_id = $bracket['team2_id'] ?? null;
                $loser_id = $bracket['team1_id'] ?? null;
            }
        } else {
            if ($scores[0] > $scores[1]) {
                $winner_id = $bracket['team1_id'] ?? null;
                $loser_id = $bracket['team2_id'] ?? null;
            } 
    
            if ($scores[0] < $scores[1]) {
                $winner_id = $bracket['team2_id'] ?? null;
                $loser_id = $bracket['team1_id'] ?? null;
            }
        }

        $next_position = $bracket['winner_next_position'];
        $winnerMatches = Matches::where('event_details_id', $deadline->event_details_id)
            ->where(function($query) use ($next_position) {
                $query->where('team1_position', $next_position)
                    ->orWhere('team2_position', $next_position);
            })
            ->get();

        foreach ($winnerMatches as $match) {
            $updated = false;
            
            if ($match->team1_position == $next_position && $match->team1_id != $winner_id) {
                $match->team1_id = $winner_id;
                $updated = true;
            }
            
            if ($match->team2_position == $next_position && $match->team2_id != $winner_id) {
                $match->team2_id = $winner_id;
                $updated = true;
            }
            
            if ($updated) {
                $match->save();
            }
        }

        $loserNextPosition = $bracket['loser_next_position'];
        if (!$loserNextPosition) return;
        $loserMatches = Matches::where('event_details_id', $deadline->event_details_id)
            ->where(function($query) use ($loserNextPosition) {
                $query->where('team1_position', $loserNextPosition)
                    ->orWhere('team2_position', $loserNextPosition);
            })
            ->get();
        
        foreach ($loserMatches as $match) {
            $updated = false;
            
            if ($match->team1_position == $loserNextPosition && $match->team1_id != $loser_id) {
                $match->team1_id = $loser_id;
                $updated = true;
            }
            
            if ($match->team2_position == $loserNextPosition && $match->team2_id != $loser_id) {
                $match->team2_id = $loser_id;
                $updated = true;
            }
            
            if ($updated) {
                $match->save();
            }
        }
    }

    public function equalizeScoreMissing($matchStatusData) {
        $team1Winners = $matchStatusData['team1Winners'] ?? [null, null, null];
        $team2Winners = $matchStatusData['team2Winners'] ?? [null, null, null];
        $realWinners = $matchStatusData['realWinners'] ?? [null, null, null];
        $scores = [0, 0];
        $updated = false;
        
        for ($i = 0; $i < 3; $i++) {
            if (!isset($team1Winners[$i]) && isset($team2Winners[$i])) {
                $updated = true;
                $realWinners[$i] = $team2Winners[$i];
            }
            if (!isset($team2Winners[$i]) && isset($team1Winners[$i])) {
                $updated = true;
                $realWinners[$i] = $team1Winners[$i];
            }
            
            if (isset($realWinners[$i])) {
                if ($realWinners[$i] === 0) {
                    $scores[0]++;
                } elseif ($realWinners[$i] === 1) {
                    $scores[1]++;
                }
            }
        }
        
        return [
            $realWinners,
            $scores,
            $updated
        ];
    }


    public function handleStartedTasks(Collection $startedBracketDeadlines) {
        foreach ($startedBracketDeadlines as $deadline) {
            $bracket = $bracketInfoMap[$deadline->event_details_id][$deadline->stage][$deadline->inner_stage_name] ?? null;
            if (!$bracket) {
                continue;
            }

            $matchStatusPath = $bracket['team1_position'] . '.' . $bracket['team2_position'];
            $docRef = $this->firestore->database()->collection('event')->document($deadline->event_details_id)->collection('match_status')->document($matchStatusPath);
            $snapshot = $docRef->snapshot();

            if ($snapshot->exists()) {
                $docRef->update([['path' => 'matchStatus', 'value' => 'ONGOING']]);
            }
        }
    }

    public function handleEndedTasks(Collection $endBracketDeadlines) {
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
                $updateValues = $this->interpretDeadlines( $matchStatusData, $updateValues, $bracket, $deadline );
                $docRef->update($updateValues);
            }
        }

    }
    
    public function handleOrgTasks(Collection $orgBracketDeadlines) {
        // penalize either missing or disputes not responded by participants
        // if not zero, randomly break break ties or null or or disputes not responded by organizers
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
                $updateValues = $this->interpretOrganizer( $matchStatusData, $updateValues, $bracket, $deadline );
                $docRef->update($updateValues);
            }
        }
    }

    protected function interpretDeadlines($matchStatusData, $updateValues, $bracket, $deadline) {
        $scores = $matchStatusData['scores'] ?? null;
        // penalize either missing or disputes not responded
        if (is_array($scores)) {
            if (($scores[0] == $scores[1]) ) {
                [ $realWinners, $scores, $updated ] = $this->equalizeScoreMissing($matchStatusData);
                if ($updated && $scores[0] != $scores[1]) {
                    $updateValues = [
                        ...$updateValues,
                        ['path' => 'realWinners', 'value' => $realWinners],
                        ['path' => 'scores', 'value' => $scores]
                    ];
                }

                if ($scores[0] == 0 && $scores[1] == 0) {
                    return $updateValues;
                }
            } 

            $this->resolveNextStage($bracket, $scores, $deadline);
        }

        return $updateValues;
    }

    public function interpretOrganizer( $matchStatusData, $updateValues, $bracket, $deadline ){
        $scores = $matchStatusData['scores'] ?? null;
        if (is_array($scores)) {
            if ($scores[0] == $scores[1]) {
                [ $realWinners, $scores, $updated ] = $this->equalizeScoreMissing($matchStatusData);
                if ($updated) {
                    if ($scores[0] == 0 && $scores[1] == 0) {
                        return $updateValues;
                    }

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
                }
            }
            
            $this->resolveNextStage( $bracket, $scores, $deadline );
        }

        return $updateValues;
    }
}
