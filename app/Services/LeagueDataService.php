<?php

namespace App\Services;

class LeagueDataService implements DataServiceInterface
{
    private array $pagination; 
    private array $roundNames;
    
    public function generateDefaultValues(
        bool $isOrganizer,
        array $USER_ENUMS
    ): array {
        return [
            'id' => null,
            'team1_id' => null,
            'team1_teamName' => 'TBD',
            'team1_teamBanner' => null,
            'team1_roster' => null,
            'team2_id' => null,
            'team2_teamName' => 'TBD',
            'team2_teamBanner' => null,
            'team2_roster' => null,
            'user_level' => $isOrganizer ? $USER_ENUMS['IS_ORGANIZER'] : $USER_ENUMS['IS_PUBLIC'],
        ];
    }

    public function getPrevValues(): array
    {
        return [
            8 => [],
            16 => [],
            32 => []
        ];
    }

    public function getPagination(): ?array
    {
        return $this->pagination;
    }

    public function getRoundNames(): ?array 
    {
        return $this->roundNames;
    }

    public function produceBrackets(
        int $teamNumber,
        bool $isOrganizer,
        ?array $USER_ENUMS,
        $deadline,
        $page,
    ) {
        // dd($page);
        if (!$page) {
            $page = 1;
        }

        $roundsPerPage = 5;
        $defaultValues = $USER_ENUMS ?
            $this->generateDefaultValues($isOrganizer, $USER_ENUMS)
            : [];
    
        $totalRounds = $teamNumber - 1;
        if ($page == 'all') {
            $roundsPerPage = $totalRounds;
            $page = 1;
        }

        $matchesPerRound = intval($teamNumber / 2);
        
        $startRound = ($page - 1) * $roundsPerPage + 1;
        $endRound = min($startRound + $roundsPerPage - 1, $totalRounds);
        
        $startRound = max(1, $startRound);
        $endRound = max($startRound, min($endRound, $totalRounds));
        $rounds = [];
        $roundNames = [];

        
        for ($round = $startRound; $round <= $endRound; $round++) {

            $roundName = "R{$round}";
            $roundNames[] = $roundName;
            

            $matches = [];
            
            for ($match = 1; $match <= $matchesPerRound; $match++) {
                // $matchKey = 'M' . (($round - 1) * $matchesPerRound + $match);
                
                $basePosition = ($round - 1) * $matchesPerRound * 2;
                $position1 = 'P' . ($basePosition + ($match - 1) * 2 + 1);
                $position2 = 'P' . ($basePosition + ($match - 1) * 2 + 2);
                
                $matches[] = [
                    ...$defaultValues,
                    'team1_position' => $position1,
                    'team2_position' => $position2,
                    'winner_next_position' => null,
                    'loser_next_position' => null,
                    'order' => $match-1,
                    'deadline' => $deadline ? (isset($deadline[$round][$round]) ? $deadline[$round][$round]: null) : null,
                ];
            }
            $rounds[$roundName][$roundName] = $matches;


        }

        // dd($rounds, $roundNames, $page);
    
        $totalPages = ceil($totalRounds / $roundsPerPage);
        $hasNextPage = $page < $totalPages;
        $hasPrevPage = $page > 1;

        $this->roundNames = $roundNames;
        
        $this->pagination = [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_rounds' => $totalRounds,
            'rounds_per_page' => $roundsPerPage,
            'has_next_page' => $hasNextPage,
            'has_prev_page' => $hasPrevPage,
            'showing_rounds' => [
                'from' => $startRound,
                'to' => $endRound
            ]
        ];
    
        return $rounds;
    }
}