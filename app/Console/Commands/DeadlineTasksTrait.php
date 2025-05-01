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
use App\Models\Brackets;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait DeadlineTasksTrait
{
    protected $bracketDataService;
    protected $firestore;


    
    protected function initializeDeadlineTasksTrait(BracketDataService $bracketDataService)
    {
        $this->bracketDataService = $bracketDataService;
        
        $factory = new \Kreait\Firebase\Factory();
        $this->firestore = $factory->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS' )))->createFirestore();
    }

    public function resolveNextStage( $bracket, array $extraBracket, array $scores, $tierId) {
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
        

        $next_position = $extraBracket['winner_next_position'];
        $bracketSetup = DB::table('brackets_setup')
            ->where('event_tier_id', $tierId) 
            ->where(function($query) use ($next_position) {
                $query->where('team1_position', $next_position)
                    ->orWhere('team2_position', $next_position);
            })
            ->first();

        $winnerBrackets = Brackets::where('event_details_id', $bracket['event_details_id'])
            ->where(function($query) use ($next_position) {
                $query->where('team1_position', $next_position)
                    ->orWhere('team2_position', $next_position);
            })
            ->first();

        if (!$winnerBrackets) {
            $winnerBrackets = new Brackets([
                'event_details_id' => $bracket['event_details_id'],
                'team1_position' => $bracketSetup['team1_position'],
                'team2_position' => $bracketSetup['team2_position'],
                'order' => $bracketSetup['order'],
                'stage_name' => $bracketSetup['stage_name'],
                'inner_stage_name' => $bracketSetup['inner_stage_name']
            ]);
        }

        $updated = false;
        
        if ($winnerBrackets->team1_position == $next_position && $winnerBrackets->team1_id != $winner_id) {
            $winnerBrackets->team1_id = $winner_id;
            $updated = true;
        }
        
        if ($winnerBrackets->team2_position == $next_position && $winnerBrackets->team2_id != $winner_id) {
            $winnerBrackets->team2_id = $winner_id;
            $updated = true;
        }
        
        if ($updated) {
            $winnerBrackets->save();
        }
        

        $loserNextPosition = $extraBracket['loser_next_position'];
        if (!$loserNextPosition) return;
        $bracketSetup = DB::table('brackets_setup')
            ->where('event_tier_id', $tierId) 
            ->where(function($query) use ($loserNextPosition) {
                $query->where('team1_position', $loserNextPosition)
                    ->orWhere('team2_position', $loserNextPosition);
            })
            ->first();

        $loserBrackets = Brackets::where('event_details_id', $bracket['event_details_id'])
            ->where(function($query) use ($loserNextPosition) {
                $query->where('team1_position', $loserNextPosition)
                    ->orWhere('team2_position', $loserNextPosition);
            })
            ->first();

        if (!$loserBrackets) {
            $loserBrackets = new Brackets([
                'event_details_id' => $bracket['event_details_id'],
                'team1_position' => $bracketSetup['team1_position'],
                'team2_position' => $bracketSetup['team2_position'],
                'order' => $bracketSetup['order'],
                'stage_name' => $bracketSetup['stage_name'],
                'inner_stage_name' => $bracketSetup['inner_stage_name']
            ]);
        }
        
        $updated = false;
        
        if ($loserBrackets->team1_position == $loserNextPosition && $loserBrackets->team1_id != $loser_id) {
            $loserBrackets->team1_id = $loser_id;
            $updated = true;
        }
        
        if ($loserBrackets->team2_position == $loserNextPosition && $loserBrackets->team2_id != $loser_id) {
            $loserBrackets->team2_id = $loser_id;
            $updated = true;
        }
        
        if ($updated) {
            $loserBrackets->save();
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
            if (!isset($realWinners[$i])) {
                if (!isset($disputeResolved[$i])) {
                    $disputePath =  $bracket['team1_position'] . '.' . $bracket['team2_position'] . '.' . $i;
                    $disputeRef = $this->firestore->database()->collection('event')->document($eventId)->collection('disputes')->document($disputePath);
                    $disputeDoc = $disputeRef?->snapshot();
                    if ($disputeDoc->exists()) {
                        $data = $disputeDoc->data();
                        // Case 1: One team filed a dispute but the other hasn't responded yet
                        if ($data['dispute_teamNumber'] && !isset($data['response_teamId'])) {
                            $isUpdatedDispute = true;
                            $realWinners[$i] = (string) $data['dispute_teamNumber'];
                            $disputeResolved[$i] = true;
                            $updateDisputeValues[$i] = [ 
                                'resolution_winner' => (string) $data['dispute_teamNumber'],
                                'resolution_resolved_by' => 'time',
                            ];
                            $disputeRefList[$i] = $disputeRef;
                        } else {
                            // Case 2: Both teams filed conflicting claims and we're set to break conflicts
                            if ($willBreakConflicts && isset($data['response_teamNumber']) ) {
                                $isUpdatedDispute = true;
                                $realWinners[$i] = rand(0, 1) ? (string) $data['dispute_teamNumber'] : (string) $data['response_teamNumber'];
                                $disputeResolved[$i] = true;
                                $updateDisputeValues[$i] = [ 
                                    'resolution_winner' => (string) $realWinners[$i],
                                    'resolution_resolved_by' => null,
                                ];
                                $disputeRefList[$i] = $disputeRef;
                            }
                        }   
                    }
                }
            }
        }

        $scores = $this->calcScores($realWinners);

        if ($isUpdatedDispute) {

            $updateReportValues = [ 
                'realWinners' => $realWinners,
                'score' => $scores,
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
        $disqualified = false;
        
        for ($i = 0; $i < 3; $i++) {
            if (!isset($realWinners[$i])) {
                // Complete but conflict
                if (isset($team2Winners[$i]) && isset($team1Winners[$i])) { 
                    if ($team2Winners[$i] == $team1Winners[$i]) {
                        $updated = true;
                        $winner_chosen = (string) $team1Winners[$i];
                        $realWinners[$i] = $winner_chosen;
                    }
                    if ($willBreakTiesAndConflicts) {
                        $disputeResolved = $matchStatusData['disputeResolved'] ?? [null, null, null];
                        if (!isset($disputeResolved[$i]) || $disputeResolved[$i]) {
                            $updated = true;
                            $winner_chosen = (string) rand(0, 1);
                            $realWinners[$i] = $winner_chosen;
                            $randomWinners[$i] = true;
                        }
                    }
                } elseif (isset($team2Winners[$i]) && !isset($team1Winners[$i])) {
                    // Only team 2 submitted a winner
                    $updated = true;
                    $defaultWinners[$i] = true;
                    $realWinners[$i] = (string) $team2Winners[$i]; // Team 2 wins
                } elseif (isset($team1Winners[$i]) && !isset($team2Winners[$i])) {
                    // Only team 1 submitted a winner
                    $updated = true;
                    $defaultWinners[$i] = true;
                    $realWinners[$i] = (string) $team1Winners[$i]; // Team 1 wins
                } else {
                    $noScores++;
                }
            }
        }

        $scores = $this->calcScores($realWinners);
        if ($noScores == 3) {
            $updated = true;
            $disqualified = true; 
        } elseif ($willBreakTiesAndConflicts) {
            // Break Tie
            if ($scores[0] == $scores[1]) {
                for ($i = 0; $i < 3; $i++) {
                    if (!isset($team2Winners[$i]) && !isset($team1Winners[$i])) { 
                        if ($willBreakTiesAndConflicts) {
                            $disputeResolved = $matchStatusData['disputeResolved'] ?? [null, null, null];
                            if (!isset($disputeResolved[$i]) || $disputeResolved[$i]) {
                                $updated = true;
                                $realWinners[$i] = (string) rand(0, 1);
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
                'score' => $scores,
                'defaultWinners' => $defaultWinners,
                'randomWinners' => $randomWinners,
                'disqualified' => $disqualified
            ];
        }
        
        return [
            $newUpdate,
            $updated
        ];
    }


    public function handleStartedTasks(Collection $startedBrackets) {
        foreach ($startedBrackets as $bracket) {

            $matchStatusPath = $bracket['team1_position'] . '.' . $bracket['team2_position'];
            $docRef = $this->firestore->database()->collection('event')->document($bracket['event_details_id'])->collection('brackets')->document($matchStatusPath);
            $snapshot = $docRef->snapshot();

            if ($snapshot->exists()) {
                $docRef->update([
                    ['path' => 'matchStatus', 'value' => ['ONGOING', 'UPCOMING', 'UPCOMING']],
                    ['path' => 'completeMatchStatus', 'value' => 'ONGOING']
                ]);
            } 
        }
    }

    public function handleEndedTasks(Collection $endBrackets, $bracketInfo, $tierId) {
        foreach ($endBrackets as $bracket) {
            $extraBracket = $bracketInfo[$bracket['stage_name']][$bracket['inner_stage_name']][$bracket['order']];
            $updateValues = [
                ['path' => 'matchStatus', 'value' => ['ENDED', 'ENDED', 'ENDED']],
                ['path' => 'completeMatchStatus', 'value' => 'ENDED']
            ];

            $matchStatusPath = $bracket['team1_position'] . '.' . $bracket['team2_position'];
            $docRef = $this->firestore->database()
                ->collection('event')
                ->document($bracket['event_details_id'])
                ->collection('brackets')
                ->document($matchStatusPath);
            $snapshot = $docRef->snapshot();

            if ($snapshot->exists()) {
                $matchStatusData = $snapshot->data();
                [   $disputeRefList,
                    $updateDisputeValues, 
                    $updateValues 
                ] = $this->interpretDeadlines( $matchStatusData, $updateValues, $bracket, $extraBracket, $tierId );
                if (!empty($updateValues)) {
                    $docRef->update($updateValues);
                }
                foreach ($disputeRefList as $index =>  $disputeRef) {
                    if (!empty($updateDisputeValues[$index])) {
                        $disputeRef?->update($updateDisputeValues[$index]);
                    }
                }
            }
        }

    }
    
    public function handleOrgTasks(Collection $orgBracketDeadlines, $bracketInfo, $tierId) {
        foreach ($orgBracketDeadlines as $bracket) {
            $updateValues = [];
            $extraBracket = $bracketInfo[$bracket['stage_name']][$bracket['inner_stage_name']][$bracket['order']];
            $matchStatusPath = $bracket['team1_position'] . '.' . $bracket['team2_position'];
            $docRef = $this->firestore->database()->collection('event')->document($bracket['event_details_id'])->collection('brackets')->document($matchStatusPath);
            $snapshot = $docRef->snapshot();

            if ($snapshot->exists()) {
                $matchStatusData = $snapshot->data();
                [   $disputeRefList,
                    $updateDisputeValues, 
                    $updateValues 
                ] = $this->interpretDeadlines( $matchStatusData, $updateValues, $bracket, $extraBracket, $tierId, true );
                Log::info(">>>>UPDATE " );
                Log::info( $updateValues);

                if (!empty($updateValues)) {
                    $docRef->update($updateValues);
                }
                foreach ($disputeRefList as $index =>  $disputeRef) {
                    if (!empty($updateDisputeValues[$index])) {
                        $disputeRef?->update($updateDisputeValues[$index]);
                    }
                }
            }
        }
    }

    protected function interpretDeadlines($matchStatusData, $updateValues, $bracket, $extraBracket, $tierId, $afterOrganizerDeadline = false) {
        [
            $updateReportValues,
            $disputeRefList,
            $updateDisputeValues,
            $isUpdatedDispute
        ] = $this->handleDisputes($matchStatusData, $bracket, $bracket['event_details_id'], $afterOrganizerDeadline);
        
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

        $this->resolveNextStage($bracket, $extraBracket, $matchStatusData['score'], $tierId);
        
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
