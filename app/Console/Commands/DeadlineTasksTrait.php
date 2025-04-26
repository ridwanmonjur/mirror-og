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
       
        if ($scores[0] > $scores[1]) {
            $winner_id = $bracket['team1_id'] ?? null;
            $loser_id = $bracket['team2_id'] ?? null;
        } 

        if ($scores[0] < $scores[1]) {
            $winner_id = $bracket['team2_id'] ?? null;
            $loser_id = $bracket['team1_id'] ?? null;
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

    public function handleDisputes($matchStatusData, $bracket, $eventId, $willBreakConflicts = false) {
        /**
         * Handles match dispute resolution in a tournament bracket
         * 
         * @param array $matchStatusData Current match status including winners and dispute state
         * @param array $bracket Information about the current bracket/match
         * @param string $eventId ID of the tournament event
         * @param bool $willBreakConflicts Whether to randomly resolve conflicting dispute claims
         * @return array Returns updates for match report, dispute references, dispute value updates, and dispute status
         */
        $realWinners = $matchStatusData['realWinners'] ?? [null, null, null];
        $disputeResolved = $matchStatusData['disputeResolved'] ?? [null, null, null];
        $scores = [0, 0];
        $isUpdatedDispute = false;
        $updateReportValues = [];
        $updateDisputeValues = [null, null, null];
        $disputeRefList = [null, null, null];
        for ($i = 0; $i < 3; $i++) { 
            $newRef = null;
            
            if (!isset($realWinners[$i])) {
                if (!isset($disputeResolved[$i])) {
                    $disputePath =  $bracket['team1_position'] . '.' . $bracket['team2_position'] . '.' . $i;
                    $disputeRef = $this->firestore->database()->collection('event')->document($eventId)->collection('disputes')->document($disputePath);
                    $disputeDoc = $disputeRef->snapshot();
                    if ($disputeDoc->exists()) {
                        $data = $disputeDoc->data();
                        // Case 1: One team filed a dispute but the other hasn't responded yet
                        if ($data['dispute_teamNumber'] && !isset($data['response_teamId'])) {
                            $isUpdatedDispute = true;
                            $realWinners[$i] = $data['dispute_teamNumber'];
                            $disputeResolved[$i] = true;
                            $updateDisputeValues[$i] = [ 
                                'resolution_winner' => $data['dispute_teamNumber'],
                                'resolution_resolved_by' => 'time',
                            ];
                            $newRef = $disputeRef;
                        } else {
                            // Case 2: Both teams filed conflicting claims and we're set to break conflicts
                            if ($willBreakConflicts && isset($data['response_teamNumber']) ) {
                                $isUpdatedDispute = true;
                                $realWinners[$i] = rand(0, 1) ? $data['dispute_teamNumber'] : $data['response_teamNumber'];
                                $disputeResolved[$i] = true;
                                $updateDisputeValues[$i] = [ 
                                    'resolution_winner' => $realWinners[$i],
                                    'resolution_resolved_by' => null,
                                ];
                                $newRef = $disputeRef;
                            }
                        }   
                    }
                }
            }

            $disputeRefList[] = $newRef;
        }

        $scores = $this->calcScores($scores);

        if ($isUpdatedDispute) {

            $updateReportValues = [ 
                'realWinners' => $realWinners,
                'scores' => $scores,
                'disputeResolved' => $disputeResolved,
            ];
        }

        return [
            $updateReportValues,
            $disputeRefList,
            $updateDisputeValues,
            $isUpdatedDispute
        ];
    }

    public function handleReports($matchStatusData, $willBreakTiesAndConflicts = false) {
        /**
         * Resolves winners for matchups with incomplete/conflicted/tied submissions.
         * 
         * @param array $matchStatusData Current match status and result data
         * @param array $bracket Current bracket context
         * @param string $eventId Event identifier for dispute references
         * @param bool $willBreakTiesAndConflicts Whether to auto-resolve tied/disputed matches
         * @return array Updated match data or empty array if no updates
         */
        
        $team1Winners = $matchStatusData['team1Winners'] ?? [null, null, null];
        $team2Winners = $matchStatusData['team2Winners'] ?? [null, null, null];
        $realWinners = $matchStatusData['realWinners'] ?? [null, null, null];
        $defaultWinners = $matchStatusData['defaultWinners'] ?? [null, null, null];
        $randomWinners = $matchStatusData['randomWinners'] ?? [null, null, null];
        $scores = [0, 0];
        $noScores = 0;
        $updated = false;
        $newUpdate = [];
        
        for ($i = 0; $i < 3; $i++) {
            if (!isset($realWinners[$i])) {
                // Complete but conflict
                if (isset($team2Winners[$i]) && isset($team1Winners[$i])) { 
                    if ($willBreakTiesAndConflicts) {
                        $disputeResolved = $matchStatusData['disputeResolved'] ?? [null, null, null];
                        if (!isset($disputeResolved[$i]) || $disputeResolved[$i]) {
                            $updated = true;
                            $winner_chosen = rand(0, 1);
                            $realWinners[$i] = $winner_chosen;
                            $randomWinners[$i] = true;
                        }
                    }
                } elseif (isset($team2Winners[$i]) && !isset($team1Winners[$i])) {
                    // Only team 2 submitted a winner
                    $updated = true;
                    $defaultWinners[$i] = true;
                    $realWinners[$i] = 1; // Team 2 wins
                } elseif (isset($team1Winners[$i]) && !isset($team2Winners[$i])) {
                    // Only team 1 submitted a winner
                    $updated = true;
                    $defaultWinners[$i] = true;
                    $realWinners[$i] = 0; // Team 1 wins
                } else {
                    $noScores++;
                }
            }
        }

        $scores = $this->calcScores($scores);

        if ($noScores == 3) {
            $updated = true;
            $newUpdate['disqualified'] = true; 
        } elseif ($willBreakTiesAndConflicts) {
            // Break Tie
            if ($scores[0] == $scores[1]) {
                for ($i = 0; $i < 3; $i++) {
                    if (!isset($team2Winners[$i]) && !isset($team1Winners[$i])) { 
                        if ($willBreakTiesAndConflicts) {
                            $disputeResolved = $matchStatusData['disputeResolved'] ?? [null, null, null];
                            if (!isset($disputeResolved[$i]) || $disputeResolved[$i]) {
                                $updated = true;
                                $realWinners[$i] = rand(0, 1);
                                $randomWinners[$i] = true;
                            }
                        }
                    }
                }
            }    
        }

        if ($updated) {
            $newUpdate = [ 
                'realWinners' => $realWinners,
                'scores' => $scores,
                'defaultWinners' => $defaultWinners,
                'randomWinners' => $randomWinners
            ];
        }
        
        return [
            $newUpdate,
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
            $docRef = $this->firestore->database()->collection('event')->document($deadline->event_details_id)->collection('brackets')->document($matchStatusPath);
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
            $docRef = $this->firestore->database()->collection('event')->document($deadline->event_details_id)->collection('brackets')->document($matchStatusPath);
            $snapshot = $docRef->snapshot();

            if ($snapshot->exists()) {
                $matchStatusData = $snapshot->data();
                [   $disputeRefList,
                    $updateDisputeValues, 
                    $updateValues 
                ] = $this->interpretDeadlines( $matchStatusData, $updateValues, $bracket, $deadline );
                $docRef->update($updateValues);
                foreach ($disputeRefList as $index =>  $disputeRef) {
                    $disputeRef->update($updateDisputeValues[$index]);
                }
            }
        }

    }
    
    public function handleOrgTasks(Collection $orgBracketDeadlines) {
        foreach ($orgBracketDeadlines as $deadline) {
            $updateValues = [];
            $bracket = $bracketInfoMap[$deadline->event_details_id][$deadline->stage][$deadline->inner_stage_name] ?? null;
            if (!$bracket) {
                continue;
            }

            $matchStatusPath = $bracket['team1_position'] . '.' . $bracket['team2_position'];
            $docRef = $this->firestore->database()->collection('event')->document($deadline->event_details_id)->collection('brackets')->document($matchStatusPath);
            $snapshot = $docRef->snapshot();

            if ($snapshot->exists()) {
                $matchStatusData = $snapshot->data();
                [   $disputeRefList,
                    $updateDisputeValues, 
                    $updateValues 
                ] = $this->interpretDeadlines( $matchStatusData, $updateValues, $bracket, $deadline, true );
                $docRef->update($updateValues);
                foreach ($disputeRefList as $index =>  $disputeRef) {
                    $disputeRef->update($updateDisputeValues[$index]);
                }
            }
        }
    }

    protected function interpretDeadlines($matchStatusData, $updateValues, $bracket, $deadline, $afterOrganizerDeadline = false) {
        [
            $updateReportValues,
            $disputeRefList,
            $updateDisputeValues,
            $isUpdatedDispute
        ] = $this->handleDisputes($matchStatusData, $bracket, $deadline->event_details_id, $afterOrganizerDeadline);

        if ($isUpdatedDispute) {
            $updateValues = $this->updateValueByPath($updateValues, $updateReportValues);
            $matchStatusData = $this->updateValueByPath($matchStatusData, $updateReportValues, true);
        }
        
        [   
            $newUpdate,
            $updated
        ] = $this->handleReports($matchStatusData);

        if ($updated) {
            $updateValues = $this->updateValueByPath($updateValues, $newUpdate);
            $matchStatusData = $this->updateValueByPath($matchStatusData, $newUpdate, true);
        }

        $this->resolveNextStage($bracket, $matchStatusData['scores'], $deadline);
        
        return [ 
            $disputeRefList,
            $updateDisputeValues, 
            $updateValues 
        ];
    }

    function updateValueByPath(array $updateValues, array $newKeyValues, bool $useDirectKeys = false): array
    {
        foreach ($newKeyValues as $pathToFind => $newValue) {
            $found = false;
            
            foreach ($updateValues as $key => $item) {
                if ($useDirectKeys) {
                    $updateValues[$pathToFind] = $newValue;
                    continue;
                }

                if (isset($item['path']) && $item['path'] === $pathToFind) {
                    $updateValues[$key]['value'] = $newValue;
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $updateValues[] = [
                    'path' => $pathToFind,
                    'value' => $newValue
                ];
            }
        }
        
        return $updateValues;
    }

    public function calcScores($realWinners) {
        $score1 = 0;
        $score2 = 0;
        
        foreach ($realWinners as $value) {
            if (!isset($value)) continue;
            if ($value == "1") {
                $score1++;
            } else {
                $score2++;
            }
        }
        
        return [$score1, $score2];
    }

}
