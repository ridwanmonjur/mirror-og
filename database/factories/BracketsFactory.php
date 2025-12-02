<?php

namespace Database\Factories;

use App\Models\EventDetail;
use App\Models\Brackets;
use App\Models\Team;
use App\Models\EventCategory;
use App\Services\EventMatchService;
use App\Services\CloudFunctionAuthService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class BracketsFactory extends Factory
{
    protected $model = Brackets::class;

    public function definition(): array
    {
        return [];
    }

  

    /**
     * The event match service instance.
     *
     * @var EventMatchService
     */
    protected $eventMatchService;

    /**
     * The cloud function auth service instance.
     *
     * @var CloudFunctionAuthService
     */
    protected $authService;

    public function __construct()
    {
        parent::__construct();

        // Resolve the services from the container
        $this->eventMatchService = App::make(EventMatchService::class);
        $this->authService = App::make(CloudFunctionAuthService::class);
    }


    public function seed($options = []) {
        $defaults = [
            'event' => [
                'eventTier' => 'Dolphin',
                'eventType' => 'Tournament',
                'eventName' => 'Test Brackets',
                'eventGame' => 'Dota 2',
            ],
            'joinEvent' => [
                'join_status' => 'confirmed',
                'payment_status' => 'confirmed',
                'participantPayment' => [
                    'register_time' => config('constants.SIGNUP_STATUS.EARLY'),
                    'type' => 'wallet',
                ],
            ],
            'noOfConTeams' => 2,
        ];

        $options = array_merge($defaults, $options);

        $joinEventFactory = new JoinEventFactory;
        // dd($options);
        
        $result = $joinEventFactory->seed($options);

        $events = collect($result['events']);
        $participants = collect($result['participants']);
        $organizer = collect($result['organizer']);
        $organizerEmail = $organizer->pluck('email')->toArray();
        $partcipantEmails = $participants->pluck('email')->toArray();
        $eventIds = $events->pluck('id')->toArray();
        $teams = $result['teams'];

        foreach ($events as $detail) {
            $this->eventMatchService->createBrackets($detail);
            
            if ($detail->type->eventType == 'Tournament') {
                $this->updateTournamentBrackets($detail->id, $teams);
            } else {
                $this->updateLeagueBrackets($detail->id, $teams);
            }
        }

        return [
            'eventIds' => $eventIds,
            'result' => $result,
            'organizers' => $organizerEmail,
            'participants' => $partcipantEmails,
        ];
    }

    public function seedDemo($options = []) {
        $defaults = [
            'event' => [
                'eventTier' => 'Dolphin',
                'eventType' => 'Tournament',
                'eventName' => 'Test Brackets',
                'eventGame' => 'Dota 2',
            ],
            'joinEvent' => [
                'join_status' => 'confirmed',
                'payment_status' => 'confirmed',
                'participantPayment' => [
                    'register_time' => config('constants.SIGNUP_STATUS.EARLY'),
                    'type' => 'wallet',
                ],
            ],
            'noOfConTeams' => 2,
        ];

        $options = array_merge($defaults, $options);

        $joinEventFactory = new JoinEventFactory;
        // dd($options);

        $result = $joinEventFactory->seed($options);

        $events = collect($result['events']);
        $participants = collect($result['participants']);
        $organizer = collect($result['organizer']);
        $organizerEmail = $organizer->pluck('email')->toArray();
        $partcipantEmails = $participants->pluck('email')->toArray();
        $eventIds = $events->pluck('id')->toArray();
        $teams = $result['teams'];

        foreach ($events as $detail) {
            // Clear cache before processing each event
            \Illuminate\Support\Facades\Cache::flush();

            $this->eventMatchService->createBrackets($detail);

            if ($detail->type->eventType == 'Tournament') {
                $this->updateTournamentDemo($detail->id, $teams);
            } else {
                // updateLeagueDemo will automatically fix bracket count mismatch
                $this->updateLeagueDemo($detail->id, $teams);
                // Calculate and populate league standings
                $this->calculateLeagueStandings($detail->id);
            }
        }

        return [
            'eventIds' => $eventIds,
            'result' => $result,
            'organizers' => $organizerEmail,
            'participants' => $partcipantEmails,
        ];
    }

    /**
     * Update bracket team IDs for specific criteria
     *
     * @param  int  $eventId  Event ID
     * @param  string  $stageName  Stage name (U, L, F, W)
     * @param  array  $innerStageNames  Array of inner stage names to update
     * @param  array  $teams  Array of team models to use for assignment
     */
    private function updateBracketTeams(
        int $eventId,
        string $stageName,
        array $innerStageNames,
        array $teams
    ): void {
        $brackets = Brackets::where('event_details_id', $eventId)
            ->where('stage_name', $stageName)
            ->whereIn('inner_stage_name', $innerStageNames)
            ->get();

        $teamCount = count($teams);
        
        if ($teamCount === 0) {
            return;
        }
        
        $teamIndex = 0;

        foreach ($brackets as $bracket) {
            $bracket->team1_id = $teams[$teamIndex % $teamCount]->id;
            $teamIndex++;

            $bracket->team2_id = $teams[$teamIndex % $teamCount]->id;
            $teamIndex++;

            $bracket->save();
        }
    }

    /**
     * Update bracket teams for Tournament events
     *
     * @param  int  $eventId  Event ID
     * @param  array  $teams  Array of team models to use for assignment
     */
    private function updateTournamentBrackets(int $eventId, array $teams): void
    {
        $this->updateBracketTeams(
            $eventId,
            'U',
            ['e1', 'e3', 'e5', 'p2'],
            $teams
        );

        // 2. Update L, e2, e4 brackets
        $this->updateBracketTeams(
            $eventId,
            'L',
            ['e2', 'e4', 'p2'],
            $teams
        );
    }

    /**
     * Update bracket teams for League events
     *
     * @param  int  $eventId  Event ID
     * @param  array  $teams  Array of team models to use for assignment
     */
    private function updateLeagueBrackets(int $eventId, array $teams): void
    {
        // For leagues, stage_name and inner_stage_name are the same
        // Using R1, R2, R3, R4, R5 for both stage and inner stage
        $rounds = ['R1', 'R2'];
        // 7, 15, 31
        
        foreach ($rounds as $round) {
            $this->updateBracketTeams(
                $eventId,
                $round,
                [$round],
                $teams
            );
        }
    }

    private function updateTournamentDemo(int $eventId, array $teams): void
    {
        $teamCount = count($teams);
        
        if ($teamCount < 2) {
            return;
        }
        
        $eventDetail = EventDetail::with(['game', 'tier'])->find($eventId);
        $eventGame = $eventDetail->game->gameTitle ?? 'Dota 2';
        $eventCategory = EventCategory::where('gameTitle', $eventGame)->first();
        $gamesPerMatch = $eventCategory->games_per_match ?? 3;
       
        $this->assignTeamsToFirstBrackets($eventId, $teams);
        [$semiMatchResults, $brackets] = $this->seedTournamentDemoResults($eventId, $gamesPerMatch);
        $this->progressTeamsThroughBrackets($eventDetail, $brackets, $semiMatchResults);
    }
    
    private function getTeamByPosition(string $position, array $teams)
    {
        if (preg_match('/W(\d+)/', $position, $matches)) {
            $teamIndex = intval($matches[1]) - 1;
            return $teams[$teamIndex] ?? null;
        }
        
        return $teams[0] ?? null;
    }
    
    private function assignTeamsToFirstBrackets(int $eventId, array $teams): void
    {
        $this->updateBracketTeams($eventId, 'U', ['e1'], $teams);
    }
    
    
    
    private function progressTeamsThroughBrackets($eventDetail, $brackets, $semiMatchResults): void
    {
        $allResults = $semiMatchResults;
        $specificIds = array_keys($semiMatchResults);
        
        $stageSequence = [
            ['stage_name' => 'U', 'inner_stage_name' => 'e1'],
            ['stage_name' => 'L', 'inner_stage_name' => 'e1'], 
            ['stage_name' => 'U', 'inner_stage_name' => 'e2'],
            ['stage_name' => 'L', 'inner_stage_name' => 'e2'],
            ['stage_name' => 'U', 'inner_stage_name' => 'e3'],
            ['stage_name' => 'L', 'inner_stage_name' => 'e3'],
            ['stage_name' => 'U', 'inner_stage_name' => 'e4'],
            ['stage_name' => 'L', 'inner_stage_name' => 'e4'],
            ['stage_name' => 'U', 'inner_stage_name' => 'p0'],
            ['stage_name' => 'L', 'inner_stage_name' => 'e5'],
            ['stage_name' => 'L', 'inner_stage_name' => 'e6'],
            ['stage_name' => 'L', 'inner_stage_name' => 'p1'],
            ['stage_name' => 'L', 'inner_stage_name' => 'p2'],
            ['stage_name' => 'F', 'inner_stage_name' => 'F'],
        ];
        
        foreach ($stageSequence as $stage) {
            $stageBrackets = Brackets::where('event_details_id', $eventDetail->id)
                ->where('stage_name', $stage['stage_name'])
                ->where('inner_stage_name', $stage['inner_stage_name'])
                ->get();
            
            if ($stageBrackets->isEmpty()) {
                continue;
            }
            
            $stageBracketsKeyed = $stageBrackets->keyBy(function ($item) {
                return $item->team1_position . '.' . $item->team2_position;
            });
            
            $stageSetup = DB::table('brackets_setup')
                ->where('event_tier_id', $eventDetail->tier->id)
                ->where('stage_name', $stage['stage_name'])
                ->where('inner_stage_name', $stage['inner_stage_name'])
                ->get()
                ->keyBy(function ($item) {
                    return $item->team1_position . '.' . $item->team2_position;
                });
            
            foreach ($stageBracketsKeyed as $matchId => $bracket) {
                if (!isset($allResults[$matchId])) {
                    $isTeam1Winner = rand(1, 2) == 1;
                    $pattern = rand(0, 3);
                    $baseResults = [];
                    $gamesPerMatch = $eventDetail->game->games_per_match ?? 3;
                    $nullArray = array_fill(0, $gamesPerMatch, null);

                    if ($isTeam1Winner) {
                        switch ($pattern) {
                            case 0: $baseResults = ['0', '0', '0']; break; // 3-0 team1
                            case 1: $baseResults = ['0', '0', '1']; break; // 2-1 team1
                            case 2: $baseResults = ['0', '1', '0']; break; // 2-1 team1
                            case 3: $baseResults = ['0', '0', null]; break; // 2-0 team1
                        }
                    } else {
                        switch ($pattern) {
                            case 0: $baseResults = ['1', '1', '1']; break; // 3-0 team2
                            case 1: $baseResults = ['1', '1', '0']; break; // 2-1 team2
                            case 2: $baseResults = ['1', '0', '1']; break; // 2-1 team2
                            case 3: $baseResults = ['1', '1', null]; break; // 2-0 team2
                        }
                    }

                    $allResults[$matchId] = [
                        'team1Id' => $bracket->team1_id,
                        'team2Id' => $bracket->team2_id,
                        'team1Winners' => $baseResults,
                        'team2Winners' => $baseResults,
                        'realWinners' => $baseResults,
                        'randomWinners' => $nullArray,
                        'organizerWinners' => $nullArray,
                        'stageName' => $bracket->stage_name,
                    ];
                    $specificIds[] = $matchId;
                }
                
                $setup = $stageSetup->get($matchId);
                if ($setup && isset($allResults[$matchId])) {
                    if (!isset($allResults[$matchId]['score'])) {
                        $allResults[$matchId]['score'] = $this->calculateScores($allResults[$matchId]['realWinners'] ?? ['0', '0']);
                    }
                    
                    $scores = $allResults[$matchId]['score'];
                    $winnerId = $scores[0] > $scores[1] ? $allResults[$matchId]['team1Id'] : $allResults[$matchId]['team2Id'];
                    $loserId = $scores[0] > $scores[1] ? $allResults[$matchId]['team2Id'] : $allResults[$matchId]['team1Id'];
                    
                    if ($setup->winner_next_position && $winnerId) {
                        $this->advanceTeamToNextBracket($eventDetail->id, $winnerId, $setup->winner_next_position);
                    }
                    
                    if ($setup->loser_next_position && $loserId) {
                        $this->advanceTeamToNextBracket($eventDetail->id, $loserId, $setup->loser_next_position);
                    }
                }
            }
        }
        
        if (!empty($allResults)) {
            $gamesPerMatch = $eventDetail->game->games_per_match ?? 3;
            $this->callCloudFunctionBatchReports($eventDetail->id, count($specificIds), array_values($allResults), $specificIds, $gamesPerMatch);
        }
    }
    
    private function advanceTeamToNextBracket(int $eventId, int $teamId, string $nextPosition): void
    {
        $nextBracket = Brackets::where('event_details_id', $eventId)
            ->where(function($query) use ($nextPosition) {
                $query->where('team1_position', $nextPosition)
                      ->orWhere('team2_position', $nextPosition);
            })
            ->first();
        
        if ($nextBracket) {
            if ($nextBracket->team1_position == $nextPosition && !$nextBracket->team1_id) {
                $nextBracket->team1_id = $teamId;
            } elseif ($nextBracket->team2_position == $nextPosition && !$nextBracket->team2_id) {
                $nextBracket->team2_id = $teamId;
            }
            
            $nextBracket->save();
        }
    }

    private function updateLeagueDemo(int $eventId, array $teams): void
    {
        $teamCount = count($teams);

        if ($teamCount < 2) {
            return;
        }

        // Get games_per_match from EventCategory
        $eventDetail = EventDetail::find($eventId);
        $eventGame = $eventDetail->game->gameTitle ?? 'Dota 2';
        $eventCategory = EventCategory::where('gameTitle', $eventGame)->first();
        $gamesPerMatch = $eventCategory->games_per_match ?? 3;

        // Check if brackets match the team count
        // Note: We don't update event_tier.tierTeamSlot because it's shared across events
        $existingBrackets = Brackets::where('event_details_id', $eventId)->count();
        $expectedRounds = $teamCount - 1;
        $expectedMatchesPerRound = floor($teamCount / 2);
        $expectedBrackets = $expectedRounds * $expectedMatchesPerRound;

        // If bracket count doesn't match, manually create brackets with correct dimensions
        if ($existingBrackets != $expectedBrackets) {
            \Illuminate\Support\Facades\Log::info("League bracket count mismatch. Existing: {$existingBrackets}, Expected: {$expectedBrackets}. Creating correct brackets.");

            // Delete old brackets
            Brackets::where('event_details_id', $eventId)->delete();

            // Manually create brackets for league (round-robin format)
            $this->createLeagueBrackets($eventId, $teamCount);
        }

        $schedule = $this->generateLeagueSchedule($teams);

        foreach ($schedule as $roundIndex => $matches) {
            $roundName = 'R' . ($roundIndex + 1);

            $brackets = Brackets::where('event_details_id', $eventId)
                ->where('stage_name', $roundName)
                ->where('inner_stage_name', $roundName)
                ->get();

            foreach ($matches as $matchIndex => $match) {
                if (isset($brackets[$matchIndex])) {
                    $bracket = $brackets[$matchIndex];
                    $bracket->team1_id = $match['team1']->id;
                    $bracket->team2_id = $match['team2']->id;
                    $bracket->save();
                }
            }
        }

        // Generate demo score data for Firestore
        $this->seedLeagueDemoResults($eventId, $gamesPerMatch);
    }
    
    /**
     * Manually create league brackets for round-robin format
     *
     * @param int $eventId Event ID
     * @param int $teamCount Number of teams
     */
    private function createLeagueBrackets(int $eventId, int $teamCount): void
    {
        $numRounds = $teamCount - 1;
        $matchesPerRound = floor($teamCount / 2);

        $now = now();
        $matches = [];
        $matchCounter = 1;

        for ($round = 1; $round <= $numRounds; $round++) {
            $roundName = 'R' . $round;

            for ($match = 1; $match <= $matchesPerRound; $match++) {
                $matches[] = [
                    'order' => $matchCounter,
                    'event_details_id' => $eventId,
                    'stage_name' => $roundName,
                    'inner_stage_name' => $roundName,
                    'team1_position' => 'P' . (($matchCounter * 2) - 1),
                    'team2_position' => 'P' . ($matchCounter * 2),
                    'team1_id' => null,
                    'team2_id' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $matchCounter++;
            }
        }

        Brackets::insert($matches);
        \Illuminate\Support\Facades\Log::info("Created {$numRounds} rounds with {$matchesPerRound} matches each for event {$eventId}");
    }

    private function generateLeagueSchedule(array $teams): array
    {
        $teamCount = count($teams);
        $schedule = [];
        
        if ($teamCount % 2 == 1) {
            $teams[] = null; // Add bye for odd number of teams
            $teamCount++;
        }
        
        $numRounds = $teamCount - 1;
        
        for ($round = 0; $round < $numRounds; $round++) {
            $roundMatches = [];
            
            for ($i = 0; $i < $teamCount / 2; $i++) {
                $home = ($round + $i) % ($teamCount - 1);
                $away = ($teamCount - 1 - $i + $round) % ($teamCount - 1);
                
                if ($i == 0) {
                    $away = $teamCount - 1;
                }
                
                $team1 = $teams[$home];
                $team2 = $teams[$away];
                
                if ($team1 !== null && $team2 !== null) {
                    $roundMatches[] = [
                        'team1' => $team1,
                        'team2' => $team2
                    ];
                }
            }
            
            $schedule[] = $roundMatches;
        }
        
        return $schedule;
    }
    
    private function seedLeagueDemoResults(int $eventId, int $gamesPerMatch): void
    {
        $brackets = Brackets::where('event_details_id', $eventId)->get();
        $bracketsKeyed = $brackets->keyBy(function ($item) {
            return $item->team1_position . '.' . $item->team2_position;
        });
        
        $documentSpecs = $this->generateUnanimousMatches($bracketsKeyed, $gamesPerMatch);
        
        $customValuesArray = [];
        $specificIds = [];

        foreach ($documentSpecs as $documentId => $customValues) {
            $slicedValues = [];
            
            foreach ($customValues as $key => $array) {
                if (is_array($array)) {
                    $slicedValues[$key] = array_slice($array, 0, $gamesPerMatch);
                } else {
                    $slicedValues[$key] = $array;
                }
            }
            
            $slicedValues['score'] = $this->calculateScores($slicedValues['realWinners']);
            
            $specificIds[] = $documentId;
            $customValuesArray[] = $slicedValues;
        }

        $this->callCloudFunctionBatchReports($eventId, count($specificIds), $customValuesArray, $specificIds, $gamesPerMatch);
    }
    
    private function seedTournamentDemoResults(int $eventId, int $gamesPerMatch): array
    {
        $brackets = Brackets::where('event_details_id', $eventId)->get();
        
        $bracketsWithTeams = $brackets->filter(function($bracket) {
            return $bracket->team1_id && $bracket->team2_id;
        });
        
        $bracketsKeyed = $bracketsWithTeams->keyBy(function ($item) {
            return $item->team1_position . '.' . $item->team2_position;
        });
        
        $documentSpecs = $this->generateTournamentMatches($bracketsKeyed, $gamesPerMatch);
        
        $customValuesArray = [];

        foreach ($documentSpecs as $documentId => $customValues) {
            $slicedValues = [];

            foreach ($customValues as $key => $array) {
                if (is_array($array)) {
                    $slicedValues[$key] = array_slice($array, 0, $gamesPerMatch);
                } else {
                    $slicedValues[$key] = $array;
                }
            }

            $slicedValues['score'] = $this->calculateScores($slicedValues['realWinners']);

            $customValuesArray[$documentId] = $slicedValues;
        }

        return [$customValuesArray, $brackets];
    }
    
    private function generateTournamentMatches($brackets, int $gamesPerMatch): array
    {
        $documentSpecs = [];
        $nullArray = array_fill(0, $gamesPerMatch, null);
        
        $index = 0;
        foreach ($brackets as $matchId => $bracket) {
            
            $isTeam1Winner = rand(1, 2) == 1;
            
            $pattern = rand(0, 3);
            $baseResults = [];
            
            if ($isTeam1Winner) {
                switch ($pattern) {
                    case 0: $baseResults = ['0', '0', '0']; break; // 3-0 team1
                    case 1: $baseResults = ['0', '0', '1']; break; // 2-1 team1
                    case 2: $baseResults = ['0', '1', '0']; break; // 2-1 team1
                    case 3: $baseResults = ['0', '0', null]; break; // 2-0 team1
                    default: $baseResults = ['0', '0', '0']; break; // 3-0 team1
                }
            } else {
                switch ($pattern) {
                    case 0: $baseResults = ['1', '1', '1']; break; // 3-0 team2
                    case 1: $baseResults = ['1', '1', '0']; break; // 2-1 team2
                    case 2: $baseResults = ['1', '0', '1']; break; // 2-1 team2
                    case 3: $baseResults = ['1', '1', null]; break; // 2-0 team2
                    default: $baseResults = ['1', '1', '1']; break; // 3-0 team2
                }
            }
            
            $documentSpecs[$matchId] = [
                'team1Winners' => $baseResults,
                'team2Winners' => $baseResults, // Tournament matches are less controversial
                'realWinners' => $baseResults,
                'randomWinners' => $nullArray,
                'organizerWinners' => $nullArray,
                'stageName' => $bracket->stage_name,
                'team1Id' => $bracket->team1_id,
                'team2Id' => $bracket->team2_id,
            ];
            $index++;
        }
        
        $conflictMatches = array_filter(array_keys($brackets->toArray()), function($matchId) use ($brackets) {
            $bracket = $brackets->get($matchId);
            return $bracket && $bracket->stage_name == 'U' && 
                   in_array($bracket->inner_stage_name, ['e1', 'e2']);
        });
        
        $conflictMatches = array_slice($conflictMatches, 0, 2);
        
        foreach ($conflictMatches as $matchId) {
            $flippedResults = [];
            foreach ($documentSpecs[$matchId]['team1Winners'] as $result) {
                $flippedResults[] = $result == '1' ? '0' : ($result == '0' ? '1' : null);
            }
            $documentSpecs[$matchId]['team2Winners'] = $flippedResults;
            $documentSpecs[$matchId]['organizerWinners'] = $documentSpecs[$matchId]['team1Winners'];
            $documentSpecs[$matchId]['realWinners'] = $documentSpecs[$matchId]['team1Winners'];
        }
        
        return $documentSpecs;
    }
    
    private function generateUnanimousMatches($brackets, int $gamesPerMatch): array
    {
        $documentSpecs = [];
        $nullArray = array_fill(0, 3, null);

        // Get all unique team IDs from brackets to create dynamic win probabilities
        $teamIds = [];
        foreach ($brackets as $bracket) {
            if ($bracket->team1_id) $teamIds[$bracket->team1_id] = true;
            if ($bracket->team2_id) $teamIds[$bracket->team2_id] = true;
        }
        $teamIds = array_keys($teamIds);
        $teamCount = count($teamIds);

        // Assign each team a skill level (0-100) that determines win probability
        // This creates a realistic distribution where some teams are stronger
        $teamSkills = [];
        foreach ($teamIds as $index => $teamId) {
            // Create a distribution where teams have varying skill levels
            // Top teams: 80-95, Mid teams: 50-80, Bottom teams: 20-50
            $position = $index / max(1, $teamCount - 1); // 0 to 1

            // Use a sigmoid-like curve to create more variance at top and bottom
            $skill = 20 + (75 * (1 - pow(abs($position - 0.5) * 2, 1.5)));

            // Add some randomness
            $skill += mt_rand(-10, 10);
            $skill = max(15, min(95, $skill)); // Clamp between 15-95

            $teamSkills[$teamId] = $skill;
        }

        $index = 0;
        foreach ($brackets as $matchId => $bracket) {
            $team1Id = $bracket->team1_id;
            $team2Id = $bracket->team2_id;

            // Get skill levels
            $team1Skill = $teamSkills[$team1Id] ?? 50;
            $team2Skill = $teamSkills[$team2Id] ?? 50;

            // Calculate win probability based on skill difference
            // Higher skill difference = more likely to win
            $skillDiff = $team1Skill - $team2Skill;
            $team1WinProb = 1 / (1 + exp(-$skillDiff / 15)); // Sigmoid function

            // Determine match winner based on probability
            $baseResults = [];
            for ($i = 0; $i < $gamesPerMatch; $i++) {
                // Add some randomness per game
                $randomFactor = mt_rand(-20, 20) / 100;
                $gameWinProb = $team1WinProb + $randomFactor;

                $team1Wins = (mt_rand(1, 100) / 100) < $gameWinProb;
                $baseResults[] = $team1Wins ? '0' : '1';
            }

            $documentSpecs[$matchId] = [
                'team1Winners' => $baseResults,
                'team2Winners' => $baseResults, // Same as team1 - unanimous
                'realWinners' => $baseResults,
                'randomWinners' => $nullArray,
                'organizerWinners' => $nullArray,
                'stageName' => $bracket->stage_name,
                'team1Id' => $bracket->team1_id,
                'team2Id' => $bracket->team2_id,
            ];
            $index++;
        }

        // Add some conflicts (5% of matches) for realism
        $conflictCount = max(2, min(10, (int)(count($brackets) * 0.05)));
        $conflictMatches = array_slice(array_keys($brackets->toArray()), 0, $conflictCount);

        foreach ($conflictMatches as $matchId) {
            // Flip team2's opinion to create conflict
            $flippedResults = [];
            foreach ($documentSpecs[$matchId]['team1Winners'] as $result) {
                if ($result !== null) {
                    $flippedResults[] = $result == '1' ? '0' : '1';
                } else {
                    $flippedResults[] = null;
                }
            }
            $documentSpecs[$matchId]['team2Winners'] = $flippedResults;
        }

        // Step 3: Resolve some conflicts with organizer decisions (40% of conflicts)
        $organizerCount = max(1, (int)($conflictCount * 0.4));
        $organizerMatches = array_slice($conflictMatches, 0, $organizerCount);

        foreach ($organizerMatches as $matchId) {
            $organizerDecision = $documentSpecs[$matchId]['team1Winners']; // Organizer sides with team1
            $documentSpecs[$matchId]['organizerWinners'] = $organizerDecision;
            $documentSpecs[$matchId]['realWinners'] = $organizerDecision;
        }

        // Step 4: Resolve remaining conflicts with random decisions
        $randomMatches = array_slice($conflictMatches, $organizerCount);

        foreach ($randomMatches as $matchId) {
            $randomDecision = [];
            foreach ($documentSpecs[$matchId]['team1Winners'] as $i => $result) {
                // Randomly pick between team1 and team2's choice
                $randomDecision[] = mt_rand(0, 1) == 0
                    ? $documentSpecs[$matchId]['team1Winners'][$i]
                    : $documentSpecs[$matchId]['team2Winners'][$i];
            }
            $documentSpecs[$matchId]['randomWinners'] = $randomDecision;
            $documentSpecs[$matchId]['realWinners'] = $randomDecision;
        }

        return $documentSpecs;
    }
    
    private function calculateScores(array $realWinners): array
    {
        $team1Score = 0;
        $team2Score = 0;
        
        foreach ($realWinners as $winner) {
            if ($winner == '0') {
                $team1Score++;
            } elseif ($winner == '1') {
                $team2Score++;
            }
        }
        
        return [$team1Score, $team2Score];
    }
    
    private function callCloudFunctionBatchReports($eventId, $count, $customValuesArray, $specificIds, $gamesPerMatch)
    {
        try {
            $cloudFunctionUrl = config('services.cloud_server_functions.url');

            // Log the data being sent for debugging
            \Illuminate\Support\Facades\Log::info('Sending batch reports to cloud function', [
                'event_id' => $eventId,
                'count' => $count,
                'specific_ids' => $specificIds,
                'specific_ids_types' => array_map('gettype', $specificIds),
                'games_per_match' => $gamesPerMatch
            ]);

            // Get cached identity token for authentication
            $identityToken = $this->authService->getCachedIdentityToken($cloudFunctionUrl);

            $response = Http::timeout(30)
                ->contentType('application/json')
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $identityToken,
                    'User-Agent' => 'Laravel-App/1.0'
                ])
                ->post($cloudFunctionUrl . '/batch/reports', [
                    'event_id' => $eventId,
                    'count' => $count,
                    'custom_values_array' => $customValuesArray,
                    'specific_ids' => $specificIds,
                    'games_per_match' => $gamesPerMatch
                ]);

            if (!$response->successful()) {
                // Clear cache on authentication errors and retry once
                if ($response->status() === 401 || $response->status() === 403) {
                    \Illuminate\Support\Facades\Log::warning("Authentication failed during factory seeding, clearing token cache and retrying");
                    $this->authService->clearIdentityTokenCache($cloudFunctionUrl);

                    // Retry once with fresh token
                    $identityToken = $this->authService->getCachedIdentityToken($cloudFunctionUrl);
                    $response = Http::timeout(30)
                        ->contentType('application/json')
                        ->withHeaders([
                            'Authorization' => 'Bearer ' . $identityToken,
                            'User-Agent' => 'Laravel-App/1.0'
                        ])
                        ->post($cloudFunctionUrl . '/batch/reports', [
                            'event_id' => $eventId,
                            'count' => $count,
                            'custom_values_array' => $customValuesArray,
                            'specific_ids' => $specificIds,
                            'games_per_match' => $gamesPerMatch
                        ]);
                }

                if (!$response->successful()) {
                    // Log detailed error information
                    \Illuminate\Support\Facades\Log::error('Cloud function call failed during factory seeding', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                        'event_id' => $eventId,
                        'count' => $count
                    ]);
                    return null;
                }
            }

            $responseData = $response->json();
            if (!isset($responseData['statusReport']) || $responseData['statusReport'] !== 'success') {
                \Illuminate\Support\Facades\Log::error('Cloud function returned error during factory seeding', [
                    'message' => $responseData['messageReport'] ?? 'Unknown error',
                    'response' => $responseData
                ]);
                return null;
            }

            return $responseData;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to create batch reports during factory seeding: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Calculate league standings from Firestore match results and populate event_join_results table
     *
     * @param int $eventId Event ID
     */
    private function calculateLeagueStandings(int $eventId): void
    {
        try {
            // Get all brackets for the event from the database
            $brackets = Brackets::where('event_details_id', $eventId)->get();

            if ($brackets->isEmpty()) {
                \Illuminate\Support\Facades\Log::warning("No brackets found for event {$eventId}");
                return;
            }

            // Get all join events (teams participating in the event)
            $joinEvents = DB::table('join_events')
                ->where('event_details_id', $eventId)
                ->where('join_status', 'confirmed')
                ->select('id', 'team_id', 'event_details_id')
                ->get();

            if ($joinEvents->isEmpty()) {
                \Illuminate\Support\Facades\Log::warning("No confirmed teams found for event {$eventId}");
                return;
            }

            // Initialize stats for each team
            $teamStats = [];
            foreach ($joinEvents as $joinEvent) {
                $teamStats[$joinEvent->team_id] = [
                    'join_events_id' => $joinEvent->id,
                    'played' => 0,
                    'won' => 0,
                    'draw' => 0,
                    'lost' => 0,
                    'points' => 0,
                ];
            }

            // Fetch all match results from Firestore using FirestoreService
            $firestoreService = app(\App\Services\FirestoreService::class);
            $allMatchResults = $firestoreService->getAllMatchResults($eventId);

            $processedMatches = 0;
            $skippedMatches = 0;

            // Process each bracket's results
            foreach ($brackets as $bracket) {
                $matchId = $bracket->team1_position . '.' . $bracket->team2_position;
                $team1Id = $bracket->team1_id;
                $team2Id = $bracket->team2_id;

                // Skip if teams are not assigned
                if (!$team1Id || !$team2Id) {
                    $skippedMatches++;
                    continue;
                }

                // Skip if teams don't have stats initialized
                if (!isset($teamStats[$team1Id]) || !isset($teamStats[$team2Id])) {
                    $skippedMatches++;
                    continue;
                }

                // Check if match result exists in Firestore
                if (!isset($allMatchResults[$matchId])) {
                    $skippedMatches++;
                    continue;
                }

                $matchData = $allMatchResults[$matchId];

                // Get the score from Firestore
                $score = $matchData['score'] ?? [0, 0];
                $team1Score = $score[0] ?? 0;
                $team2Score = $score[1] ?? 0;

                // Only process if the match has been played (score is not 0-0)
                if ($team1Score > 0 || $team2Score > 0) {
                    // Update played count
                    $teamStats[$team1Id]['played']++;
                    $teamStats[$team2Id]['played']++;

                    // Determine winner and update stats
                    if ($team1Score > $team2Score) {
                        // Team 1 wins
                        $teamStats[$team1Id]['won']++;
                        $teamStats[$team1Id]['points'] += 3; // 3 points for a win
                        $teamStats[$team2Id]['lost']++;
                    } elseif ($team2Score > $team1Score) {
                        // Team 2 wins
                        $teamStats[$team2Id]['won']++;
                        $teamStats[$team2Id]['points'] += 3; // 3 points for a win
                        $teamStats[$team1Id]['lost']++;
                    } else {
                        // Draw
                        $teamStats[$team1Id]['draw']++;
                        $teamStats[$team1Id]['points'] += 1; // 1 point for a draw
                        $teamStats[$team2Id]['draw']++;
                        $teamStats[$team2Id]['points'] += 1; // 1 point for a draw
                    }
                    $processedMatches++;
                } else {
                    $skippedMatches++;
                }
            }

            // Calculate positions based on points (with tiebreakers: points, wins, then team_id for uniqueness)
            $standingsArray = [];
            foreach ($teamStats as $teamId => $stats) {
                $standingsArray[] = [
                    'team_id' => $teamId,
                    'join_events_id' => $stats['join_events_id'],
                    'played' => $stats['played'],
                    'won' => $stats['won'],
                    'draw' => $stats['draw'],
                    'lost' => $stats['lost'],
                    'points' => $stats['points'],
                    // Create sort key: points (highest), wins (highest), team_id (lowest for consistency)
                    'sort_key' => ($stats['points'] * 1000000) + ($stats['won'] * 1000) + (10000 - $teamId)
                ];
            }

            // Sort by sort_key descending
            usort($standingsArray, function($a, $b) {
                return $b['sort_key'] <=> $a['sort_key'];
            });

            $position = 1;
            foreach ($standingsArray as $stats) {
                // Insert or update event_join_results
                DB::table('event_join_results')->updateOrInsert(
                    ['join_events_id' => $stats['join_events_id']],
                    [
                        'position' => $position,
                        'played' => $stats['played'],
                        'won' => $stats['won'],
                        'draw' => $stats['draw'],
                        'lost' => $stats['lost'],
                        'points' => $stats['points'],
                    ]
                );
                $position++;
            }

            \Illuminate\Support\Facades\Log::info("Successfully calculated league standings for event {$eventId}. Processed: {$processedMatches}, Skipped: {$skippedMatches}");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to calculate league standings for event {$eventId}: " . $e->getMessage());
            \Illuminate\Support\Facades\Log::error("Stack trace: " . $e->getTraceAsString());
        }
    }
}
