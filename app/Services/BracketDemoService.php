<?php

namespace App\Services;

class BracketDemoService implements DataServiceInterface
{
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
        return self::PREV_VALUES;
    }

    public function getPagination(): ?array
    {
        return [
            "current_page" => 1,
            "total_pages" => 1, 
            "total_rounds" => 3,
            "rounds_per_page" => 3,
            "has_next_page" => false,
            "has_prev_page" => false,
            "showing_rounds" =>  [
                "from" => 1,
                "to" => 3
            ]
        ];
    }

    public function getRoundNames(): ?array 
    {
        return ['U', 'L', 'F'];
    }

    const PREV_VALUES = [
        32 => [
            'F' => ['G1', 'G2'],
            'G1' => ['U29', 'U30'],
            'G2' => ['L59', 'L60'],
            //
            //
            'U1' => ['W1', 'W2'],
            'U2' => ['W3', 'W4'],
            'U3' => ['W5', 'W6'],
            'U4' => ['W7', 'W8'],
            'U5' => ['W9', 'W10'],
            'U6' => ['W11', 'W12'],
            'U7' => ['W13', 'W14'],
            'U8' => ['W15', 'W16'],
            'U9' => ['W17', 'W18'],
            'U10' => ['W19', 'W20'],
            'U11' => ['W21', 'W22'],
            'U12' => ['W23', 'W24'],
            'U13' => ['W25', 'W26'],
            'U14' => ['W27', 'W28'],
            'U15' => ['W29', 'W30'],
            'U16' => ['W31', 'W32'],
            //
            'U17' => ['U1', 'U2'],
            'U18' => ['U3', 'U4'],
            'U19' => ['U5', 'U6'],
            'U20' => ['U7', 'U8'],
            'U21' => ['U9', 'U10'],
            'U22' => ['U11', 'U12'],
            'U23' => ['U13', 'U14'],
            'U24' => ['U15', 'U16'],
            //
            'U25' => ['U17', 'U18'],
            'U26' => ['U19', 'U20'],
            'U27' => ['U21', 'U22'],
            'U28' => ['U23', 'U24'],
            //
            'U29' => ['U25', 'U26'],
            'U30' => ['U27', 'U28'],
            //
            //
            'L1' => ['W1', 'W2'],
            'L2' => ['W3', 'W4'],
            'L3' => ['W5', 'W6'],
            'L4' => ['W7', 'W8'],
            'L5' => ['W9', 'W10'],
            'L6' => ['W11', 'W12'],
            'L7' => ['W13', 'W14'],
            'L8' => ['W15', 'W16'],
            'L9' => ['W17', 'W18'],
            'L10' => ['W19', 'W20'],
            'L11' => ['W21', 'W22'],
            'L12' => ['W23', 'W24'],
            'L13' => ['W25', 'W26'],
            'L14' => ['W27', 'W28'],
            'L15' => ['W29', 'W30'],
            'L16' => ['W31', 'W32'],
            //
            'L17' => ['L1', 'L2'],
            'L18' => ['U1', 'U2'],
            'L19' => ['L3', 'L4'],
            'L20' => ['U3', 'U4'],
            'L21' => ['L5', 'L6'],
            'L22' => ['U5', 'U6'],
            'L23' => ['L7', 'L8'],
            'L24' => ['U7', 'U8'],
            'L25' => ['L9', 'L10'],
            'L26' => ['U9', 'U10'],
            'L27' => ['L11', 'L12'],
            'L28' => ['U11', 'U12'],
            'L29' => ['L13', 'L14'],
            'L30' => ['U13', 'U14'],
            'L31' => ['L15', 'L16'],
            'L32' => ['U15', 'U16'],
            //
            //
            'L33' => ['L17', 'L18'],
            'L34' => ['L19', 'L20'],
            'L35' => ['L21', 'L22'],
            'L36' => ['L23', 'L24'],
            'L37' => ['L25', 'L26'],
            'L38' => ['L27', 'L28'],
            'L39' => ['L29', 'L30'],
            'L40' => ['L31', 'L32'],
            //
            //
            'L41' => ['L33', 'L34'],
            'L42' => ['U17', 'U18'],
            'L43' => ['L35', 'L36'],
            'L44' => ['U19', 'U20'],
            'L45' => ['L37', 'L38'],
            'L46' => ['U21', 'U22'],
            'L47' => ['L39', 'L40'],
            'L48' => ['U23', 'U24'],
            //
            //
            'L49' => ['L41', 'L42'],
            'L50' => ['L43', 'L44'],
            'L51' => ['L45', 'L45'],
            'L52' => ['L47', 'L48'],
            //
            //
            'L53' => ['L49', 'L50'],
            'L54' => ['U25', 'U26'],
            'L55' => ['L51', 'L52'],
            'L56' => ['U27', 'U28'],
            //
            //
            'L57' => ['L53', 'L54'],
            'L58' => ['L55', 'L56'],
            //
            //
            'L59' => ['L57', 'L58'],
            'L60' => ['U29', 'U30'],
        ],
        16 => [
            'F' => ['G1', 'G2'],
            'G1' => ['U13', 'U14'],
            'G2' => ['L27', 'L28'],
            //
            // Upper Bracket
            'U1' => ['W1', 'W2'],
            'U2' => ['W3', 'W4'],
            'U3' => ['W5', 'W6'],
            'U4' => ['W7', 'W8'],
            'U5' => ['W9', 'W10'],
            'U6' => ['W11', 'W12'],
            'U7' => ['W13', 'W14'],
            'U8' => ['W15', 'W16'],
            //
            'U9' => ['U1', 'U2'],
            'U10' => ['U3', 'U4'],
            'U11' => ['U5', 'U6'],
            'U12' => ['U7', 'U8'],
            //
            'U13' => ['U9', 'U10'],
            'U14' => ['U11', 'U12'],
            //
            'U15' => ['U13', 'U14'],
            //
            // Lower Bracket
            'L1' => ['W1', 'W2'],
            'L2' => ['W3', 'W4'],
            'L3' => ['W5', 'W6'],
            'L4' => ['W7', 'W8'],
            'L5' => ['W9', 'W10'],
            'L6' => ['W11', 'W12'],
            'L7' => ['W13', 'W14'],
            'L8' => ['W15', 'W16'],
            //
            'L9' => ['L1', 'L2'],
            'L10' => ['U1', 'U2'],
            'L11' => ['L3', 'L4'],
            'L12' => ['U3', 'U4'],
            'L13' => ['L5', 'L6'],
            'L14' => ['U5', 'U6'],
            'L15' => ['L7', 'L8'],
            'L16' => ['U7', 'U8'],
            //
            'L17' => ['L9', 'L10'],
            'L18' => ['L11', 'L12'],
            'L19' => ['L13', 'L14'],
            'L20' => ['L15', 'L16'],
            //
            'L21' => ['L17', 'L18'],
            'L22' => ['U9', 'U10'],
            'L23' => ['L19', 'L20'],
            'L24' => ['U11', 'U12'],
            //
            'L25' => ['L21', 'L22'],
            'L26' => ['L23', 'L24'],
            //
            'L27' => ['L25', 'L26'],
            //
            'L28' => ['U13', 'U14'],
        ],
        8 => [
            'F' => ['G1', 'G2'],
            'G1' => ['U5', 'U6'],
            'G2' => ['L11', 'L12'],
            //
            // Upper Bracket
            'U1' => ['W1', 'W2'],
            'U2' => ['W3', 'W4'],
            'U3' => ['W5', 'W6'],
            'U4' => ['W7', 'W8'],
            //
            'U5' => ['U1', 'U2'],
            'U6' => ['U3', 'U4'],
            //
            //
            'L1' => ['W1', 'W2'],
            'L2' => ['W3', 'W4'],
            'L3' => ['W5', 'W6'],
            'L4' => ['W7', 'W8'],
            //
            'L5' => ['L1', 'L2'],
            'L6' => ['U1', 'U2'],
            'L7' => ['L3', 'L4'],
            'L8' => ['U3', 'U4'],
            //
            'L9' => ['L5', 'L6'],
            'L10' => ['L7', 'L8'],
            //
            'L11' => ['L9', 'L10'],
            'L12' => ['U5', 'U6'],
        ],
    ];

    public function produceBrackets(
        int $teamNumber,
        bool $isOrganizer,
        ?array $USER_ENUMS,
        $deadlines,
        $page
    ) {
        $defaultValues = $USER_ENUMS ?
            $this->generateDefaultValues($isOrganizer, $USER_ENUMS)
            : [];

        // Demo data with winners assigned
        $demoResults = $this->getDemoResults($teamNumber);

        if ($teamNumber == 32) {
            return $this->generateBrackets32($defaultValues, $deadlines, $demoResults);
        } elseif ($teamNumber == 16) {
            return $this->generateBrackets16($defaultValues, $deadlines, $demoResults);
        } else {
            return $this->generateBrackets8($defaultValues, $deadlines, $demoResults);
        }
    }

    private function getDemoResults(int $teamNumber): array
    {
        // Predefined winners for demo purposes
        if ($teamNumber == 32) {
            return [
                // Upper bracket winners (odd team numbers win in first round)
                'U1' => 'W1', 'U2' => 'W3', 'U3' => 'W5', 'U4' => 'W7',
                'U5' => 'W9', 'U6' => 'W11', 'U7' => 'W13', 'U8' => 'W15',
                'U9' => 'W17', 'U10' => 'W19', 'U11' => 'W21', 'U12' => 'W23',
                'U13' => 'W25', 'U14' => 'W27', 'U15' => 'W29', 'U16' => 'W31',
                // Continue pattern...
                'U17' => 'U1', 'U18' => 'U3', 'U19' => 'U5', 'U20' => 'U7',
                'U21' => 'U9', 'U22' => 'U11', 'U23' => 'U13', 'U24' => 'U15',
                'U25' => 'U17', 'U26' => 'U19', 'U27' => 'U21', 'U28' => 'U23',
                'U29' => 'U25', 'U30' => 'U27',
                'G1' => 'U29',
                'F' => 'G1',
            ];
        } elseif ($teamNumber == 16) {
            return [
                'U1' => 'W1', 'U2' => 'W3', 'U3' => 'W5', 'U4' => 'W7',
                'U5' => 'W9', 'U6' => 'W11', 'U7' => 'W13', 'U8' => 'W15',
                'U9' => 'U1', 'U10' => 'U3', 'U11' => 'U5', 'U12' => 'U7',
                'U13' => 'U9', 'U14' => 'U11',
                'G1' => 'U13',
                'F' => 'G1',
            ];
        } else {
            return [
                'U1' => 'W1', 'U2' => 'W3', 'U3' => 'W5', 'U4' => 'W7',
                'U5' => 'U1', 'U6' => 'U3',
                'G1' => 'U5',
                'F' => 'G1',
            ];
        }
    }

    private function generateBrackets32(array $defaultValues, $deadlines, array $demoResults): array
    {
        return [
            'F' => [
                'F' => [[
                    'team1_position' => 'G1',
                    'team2_position' => 'G2',
                    'winner_next_position' => 'F',
                    'loser_next_position' => null,
                    'winner_position' => $demoResults['F'] ?? null,
                    ...$defaultValues,
                    'deadline' => $deadlines['F']['F'] ?? null,
                ]],
                'W' => [[
                    'team1_position' => 'F',
                    'team2_position' => null,
                    'winner_next_position' => null,
                    'loser_next_position' => null,
                    ...$defaultValues,
                    'deadline' => $deadlines['F']['W'] ?? null,
                ]],
            ],
            'U' => [
                'e1' => [
                    [
                        'team1_position' => 'W1',
                        'team2_position' => 'W2',
                        'winner_next_position' => 'U1',
                        'loser_next_position' => 'L1',
                        'winner_position' => $demoResults['U1'] ?? null,
                        ...$defaultValues,
                        'deadline' => $deadlines['U']['e1'] ?? null,
                    ],
                    [
                        'team1_position' => 'W3',
                        'team2_position' => 'W4',
                        'winner_next_position' => 'U2',
                        'loser_next_position' => 'L2',
                        'winner_position' => $demoResults['U2'] ?? null,
                        ...$defaultValues,
                        'deadline' => $deadlines['U']['e1'] ?? null,
                    ],
                    [
                        'team1_position' => 'W5',
                        'team2_position' => 'W6',
                        'winner_next_position' => 'U3',
                        'loser_next_position' => 'L3',
                        'winner_position' => $demoResults['U3'] ?? null,
                        ...$defaultValues,
                        'deadline' => $deadlines['U']['e1'] ?? null,
                    ],
                    [
                        'team1_position' => 'W7',
                        'team2_position' => 'W8',
                        'winner_next_position' => 'U4',
                        'loser_next_position' => 'L4',
                        'winner_position' => $demoResults['U4'] ?? null,
                        ...$defaultValues,
                        'deadline' => $deadlines['U']['e1'] ?? null,
                    ],
                    [
                        'team1_position' => 'W9',
                        'team2_position' => 'W10',
                        'winner_next_position' => 'U5',
                        'loser_next_position' => 'L5',
                        'winner_position' => $demoResults['U5'] ?? null,
                        ...$defaultValues,
                        'deadline' => $deadlines['U']['e1'] ?? null,
                    ],
                    [
                        'team1_position' => 'W11',
                        'team2_position' => 'W12',
                        'winner_next_position' => 'U6',
                        'loser_next_position' => 'L6',
                        'winner_position' => $demoResults['U6'] ?? null,
                        ...$defaultValues,
                        'deadline' => $deadlines['U']['e1'] ?? null,
                    ],
                    [
                        'team1_position' => 'W13',
                        'team2_position' => 'W14',
                        'winner_next_position' => 'U7',
                        'loser_next_position' => 'L7',
                        'winner_position' => $demoResults['U7'] ?? null,
                        ...$defaultValues,
                        'deadline' => $deadlines['U']['e1'] ?? null,
                    ],
                    [
                        'team1_position' => 'W15',
                        'team2_position' => 'W16',
                        'winner_next_position' => 'U8',
                        'loser_next_position' => 'L8',
                        'winner_position' => $demoResults['U8'] ?? null,
                        ...$defaultValues,
                        'deadline' => $deadlines['U']['e1'] ?? null,
                    ],
                    [
                        'team1_position' => 'W17',
                        'team2_position' => 'W18',
                        'winner_next_position' => 'U9',
                        'loser_next_position' => 'L9',
                        'winner_position' => $demoResults['U9'] ?? null,
                        ...$defaultValues,
                        'deadline' => $deadlines['U']['e1'] ?? null,
                    ],
                    [
                        'team1_position' => 'W19',
                        'team2_position' => 'W20',
                        'winner_next_position' => 'U10',
                        'loser_next_position' => 'L10',
                        'winner_position' => $demoResults['U10'] ?? null,
                        ...$defaultValues,
                        'deadline' => $deadlines['U']['e1'] ?? null,
                    ],
                    [
                        'team1_position' => 'W21',
                        'team2_position' => 'W22',
                        'winner_next_position' => 'U11',
                        'loser_next_position' => 'L11',
                        'winner_position' => $demoResults['U11'] ?? null,
                        ...$defaultValues,
                        'deadline' => $deadlines['U']['e1'] ?? null,
                    ],
                    [
                        'team1_position' => 'W23',
                        'team2_position' => 'W24',
                        'winner_next_position' => 'U12',
                        'loser_next_position' => 'L12',
                        'winner_position' => $demoResults['U12'] ?? null,
                        ...$defaultValues,
                        'deadline' => $deadlines['U']['e1'] ?? null,
                    ],
                    [
                        'team1_position' => 'W25',
                        'team2_position' => 'W26',
                        'winner_next_position' => 'U13',
                        'loser_next_position' => 'L13',
                        'winner_position' => $demoResults['U13'] ?? null,
                        ...$defaultValues,
                        'deadline' => $deadlines['U']['e1'] ?? null,
                    ],
                    [
                        'team1_position' => 'W27',
                        'team2_position' => 'W28',
                        'winner_next_position' => 'U14',
                        'loser_next_position' => 'L14',
                        'winner_position' => $demoResults['U14'] ?? null,
                        ...$defaultValues,
                        'deadline' => $deadlines['U']['e1'] ?? null,
                    ],
                    [
                        'team1_position' => 'W29',
                        'team2_position' => 'W30',
                        'winner_next_position' => 'U15',
                        'loser_next_position' => 'L15',
                        'winner_position' => $demoResults['U15'] ?? null,
                        ...$defaultValues,
                        'deadline' => $deadlines['U']['e1'] ?? null,
                    ],
                    [
                        'team1_position' => 'W31',
                        'team2_position' => 'W32',
                        'winner_next_position' => 'U16',
                        'loser_next_position' => 'L16',
                        'winner_position' => $demoResults['U16'] ?? null,
                        ...$defaultValues,
                        'deadline' => $deadlines['U']['e1'] ?? null,
                    ],
                ],
                'p0' => [
                    [
                        'team1_position' => 'U29',
                        'team2_position' => 'U30',
                        'winner_next_position' => 'G1',
                        'loser_next_position' => 'L60',
                        'winner_position' => $demoResults['G1'] ?? null,
                        ...$defaultValues,
                        'deadline' => $deadlines['U']['p0'] ?? null,
                    ],
                ],
            ],
            'L' => [
                'p2' => [
                    [
                        'team1_position' => 'L59',
                        'team2_position' => 'L60',
                        'winner_next_position' => 'G2',
                        'loser_next_position' => null,
                        ...$defaultValues,
                        'deadline' => $deadlines['L']['p2'] ?? null,
                    ],
                ],
            ],
        ];
    }

    private function generateBrackets16(array $defaultValues, $deadlines, array $demoResults): array
    {
        return [
            'F' => [
                'F' => [[
                    'team1_position' => 'G1',
                    'team2_position' => 'G2',
                    'winner_next_position' => 'F',
                    'loser_next_position' => null,
                    'winner_position' => $demoResults['F'] ?? null,
                    ...$defaultValues,
                    'deadline' => $deadlines['F']['F'] ?? null,
                ]],
                'W' => [[
                    'team1_position' => 'F',
                    'team2_position' => null,
                    'winner_next_position' => null,
                    'loser_next_position' => null,
                    ...$defaultValues,
                    'deadline' => $deadlines['F']['W'] ?? null,
                ]],
            ],
            'U' => [
                'e1' => [
                    [
                        'team1_position' => 'W1',
                        'team2_position' => 'W2',
                        'winner_next_position' => 'U1',
                        'loser_next_position' => 'L1',
                        'winner_position' => $demoResults['U1'] ?? null,
                        ...$defaultValues,
                        'deadline' => $deadlines['U']['e1'] ?? null,
                    ],
                    [
                        'team1_position' => 'W3',
                        'team2_position' => 'W4',
                        'winner_next_position' => 'U2',
                        'loser_next_position' => 'L2',
                        'winner_position' => $demoResults['U2'] ?? null,
                        ...$defaultValues,
                        'deadline' => $deadlines['U']['e1'] ?? null,
                    ],
                    [
                        'team1_position' => 'W5',
                        'team2_position' => 'W6',
                        'winner_next_position' => 'U3',
                        'loser_next_position' => 'L3',
                        'winner_position' => $demoResults['U3'] ?? null,
                        ...$defaultValues,
                        'deadline' => $deadlines['U']['e1'] ?? null,
                    ],
                    [
                        'team1_position' => 'W7',
                        'team2_position' => 'W8',
                        'winner_next_position' => 'U4',
                        'loser_next_position' => 'L4',
                        'winner_position' => $demoResults['U4'] ?? null,
                        ...$defaultValues,
                        'deadline' => $deadlines['U']['e1'] ?? null,
                    ],
                    [
                        'team1_position' => 'W9',
                        'team2_position' => 'W10',
                        'winner_next_position' => 'U5',
                        'loser_next_position' => 'L5',
                        'winner_position' => $demoResults['U5'] ?? null,
                        ...$defaultValues,
                        'deadline' => $deadlines['U']['e1'] ?? null,
                    ],
                    [
                        'team1_position' => 'W11',
                        'team2_position' => 'W12',
                        'winner_next_position' => 'U6',
                        'loser_next_position' => 'L6',
                        'winner_position' => $demoResults['U6'] ?? null,
                        ...$defaultValues,
                        'deadline' => $deadlines['U']['e1'] ?? null,
                    ],
                    [
                        'team1_position' => 'W13',
                        'team2_position' => 'W14',
                        'winner_next_position' => 'U7',
                        'loser_next_position' => 'L7',
                        'winner_position' => $demoResults['U7'] ?? null,
                        ...$defaultValues,
                        'deadline' => $deadlines['U']['e1'] ?? null,
                    ],
                    [
                        'team1_position' => 'W15',
                        'team2_position' => 'W16',
                        'winner_next_position' => 'U8',
                        'loser_next_position' => 'L8',
                        'winner_position' => $demoResults['U8'] ?? null,
                        ...$defaultValues,
                        'deadline' => $deadlines['U']['e1'] ?? null,
                    ],
                ],
                'p0' => [
                    [
                        'team1_position' => 'U13',
                        'team2_position' => 'U14',
                        'winner_next_position' => 'G1',
                        'loser_next_position' => 'L28',
                        'winner_position' => $demoResults['G1'] ?? null,
                        ...$defaultValues,
                        'deadline' => $deadlines['U']['p0'] ?? null,
                    ],
                ],
            ],
            'L' => [
                'p2' => [
                    [
                        'team1_position' => 'L27',
                        'team2_position' => 'L28',
                        'winner_next_position' => 'G2',
                        'loser_next_position' => null,
                        ...$defaultValues,
                        'deadline' => $deadlines['L']['p2'] ?? null,
                    ],
                ],
            ],
        ];
    }

    private function generateBrackets8(array $defaultValues, $deadlines, array $demoResults): array
    {
        return [
            'F' => [
                'F' => [[
                    'team1_position' => 'G1',
                    'team2_position' => 'G2',
                    'winner_next_position' => 'F',
                    'loser_next_position' => null,
                    'winner_position' => $demoResults['F'] ?? null,
                    ...$defaultValues,
                    'deadline' => $deadlines['F']['F'] ?? null,
                ]],
                'W' => [[
                    'team1_position' => 'F',
                    'team2_position' => null,
                    'winner_next_position' => null,
                    'loser_next_position' => null,
                    ...$defaultValues,
                    'deadline' => $deadlines['F']['W'] ?? null,
                ]],
            ],
            'U' => [
                'e1' => [
                    [
                        'team1_position' => 'W1',
                        'team2_position' => 'W2',
                        'winner_next_position' => 'U1',
                        'loser_next_position' => 'L1',
                        'winner_position' => $demoResults['U1'] ?? null,
                        ...$defaultValues,
                        'deadline' => $deadlines['U']['e1'] ?? null,
                    ],
                    [
                        'team1_position' => 'W3',
                        'team2_position' => 'W4',
                        'winner_next_position' => 'U2',
                        'loser_next_position' => 'L2',
                        'winner_position' => $demoResults['U2'] ?? null,
                        ...$defaultValues,
                        'deadline' => $deadlines['U']['e1'] ?? null,
                    ],
                    [
                        'team1_position' => 'W5',
                        'team2_position' => 'W6',
                        'winner_next_position' => 'U3',
                        'loser_next_position' => 'L3',
                        'winner_position' => $demoResults['U3'] ?? null,
                        ...$defaultValues,
                        'deadline' => $deadlines['U']['e1'] ?? null,
                    ],
                    [
                        'team1_position' => 'W7',
                        'team2_position' => 'W8',
                        'winner_next_position' => 'U4',
                        'loser_next_position' => 'L4',
                        'winner_position' => $demoResults['U4'] ?? null,
                        ...$defaultValues,
                        'deadline' => $deadlines['U']['e1'] ?? null,
                    ],
                ],
                'p0' => [
                    [
                        'team1_position' => 'U5',
                        'team2_position' => 'U6',
                        'winner_next_position' => 'G1',
                        'loser_next_position' => 'L12',
                        'winner_position' => $demoResults['G1'] ?? null,
                        ...$defaultValues,
                        'deadline' => $deadlines['U']['p0'] ?? null,
                    ],
                ],
            ],
            'L' => [
                'p2' => [
                    [
                        'team1_position' => 'L11',
                        'team2_position' => 'L12',
                        'winner_next_position' => 'G2',
                        'loser_next_position' => null,
                        ...$defaultValues,
                        'deadline' => $deadlines['L']['p2'] ?? null,
                    ],
                ],
            ],
        ];
    }
}