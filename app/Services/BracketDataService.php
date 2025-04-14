<?php
namespace App\Services;

class BracketDataService
{
    public function generateDefaultValues (
            bool $isOrganizer = true,
            array $USER_ENUMS
        ): array {
        return [
            'id' => null,
            'team1_id' => null,
            'team1_teamName' => 'No team',
            'team1_teamBanner' => null,
            'team1_roster' => null,
            'team2_id' => null,
            'team2_teamName' => 'No team',
            'team2_teamBanner' => null,
            'team2_roster' => null,
            'user_level' => $isOrganizer ? $USER_ENUMS['IS_ORGANIZER']: null
        ];
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

    public function getReportDeadlines(int $teamNumber = 32) {
        
        return [
            32 => [
                'U' => [
                    'e2' => ['start' => 0, 'end'=> 3], /* 1 */
                    'e3' => ['start' => 7, 'end'=> 8], /* 2 */
                    'e4' => ['start' => 11, 'end'=> 12], /* 3 */
                    'p0' => ['start' => 15, 'end'=> 14], /* 4 */
                ], 
                'L' => [
                    'e1' => ['start' => 0, 'end'=> 3], /* 1 */
                    'e2' => ['start' => 4, 'end'=> 6],  
                    'e3' => ['start' => 7, 'end'=> 8], /* 2 */
                    'e4' => ['start' => 9, 'end'=> 10],
                    'e5' => ['start' => 11, 'end'=> 12], /* 3 */
                    'e6' => ['start' => 13, 'end'=> 14],
                    'p1' => ['start' => 15, 'end'=> 16], /* 4 */
                    'p2' => ['start' => 17, 'end'=> 18],
                ],
                'F' => [
                    'F' => ['start' => 19, 'end'=> 20],
                    'W' => ['start' => 21, 'end'=> 22]
                ]
            ], 
            16 => [
                'U' => [
                    'e2' => ['start' => 0, 'end'=> 3], /* 1 */
                    'e3' => ['start' => 7, 'end'=> 8], /* 2 */
                    'p0' => ['start' => 11, 'end'=> 12], /* 3 */
                ], 
                'L' => [
                    'e1' => ['start' => 0, 'end'=> 3], /* 1 */
                    'e2' => ['start' => 4, 'end'=> 6],  
                    'e3' => ['start' => 7, 'end'=> 8], /* 2 */
                    'e4' => ['start' => 9, 'end'=> 10],
                    'p1' => ['start' => 11, 'end'=> 12], /* 3 */
                    'p2' => ['start' => 13, 'end'=> 14],
                ],
                'F' => [
                    'F' => ['start' => 15, 'end'=> 16],
                    'W' => ['start' => 17, 'end'=> 18]
                ]
            ], 
            8 => [
                'U' => [
                    'e2' => ['start' => 0, 'end'=> 3], /* 1 */
                    'p0' => ['start' => 7, 'end'=> 8], /* 2 */
                ], 
                'L' => [
                    'e1' => ['start' => 0, 'end'=> 3], /* 1 */
                    'e2' => ['start' => 4, 'end'=> 6],
                    'p1' => ['start' => 7, 'end'=> 8], /* 2 */
                    'p2' => ['start' => 9, 'end'=> 10],
                ],
                'F' => [
                    'F' => ['start' => 11, 'end'=> 12],
                    'W' => ['start' => 13, 'end'=> 14]
                ]
            ]
        ][$teamNumber];
    }

    public function produceBrackets(
            int $teamNumber = 32,
            bool $isOrganizer = true,
            array $USER_ENUMS,
            array $deadlines
        ) 
    {
        $defaultValues = $this->generateDefaultValues($isOrganizer, $USER_ENUMS);
        
        if ($teamNumber == 32) {
            return [
                'F' => [
                    'F' => [[
                        'team1_position' => 'G1',
                        'team2_position' => 'G2',
                        'winner_next_position' => 'F',
                        'loser_next_position' => null,
                        ...$defaultValues,
                        'deadline' => $deadlines['F']['F']
                    ]],
                    'W' => [[
                        'team1_position' => 'F',
                        'team2_position' => null,
                        'winner_next_position' => null,
                        'loser_next_position' => null,
                        ...$defaultValues,
                        'deadline' => $deadlines['F']['W']
                    ]]
                ],
                'U' => [
                    'e1' => [
                        [
                            'team1_position' => 'W1',
                            'team2_position' => 'W2',
                            'winner_next_position' => 'U1',
                            'loser_next_position' => 'L1',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e1']
                        ], // 1
                        [
                            'team1_position' => 'W3',
                            'team2_position' => 'W4',
                            'winner_next_position' => 'U2',
                            'loser_next_position' => 'L2',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e1']
                        ], // 2
                        [
                            'team1_position' => 'W5',
                            'team2_position' => 'W6',
                            'winner_next_position' => 'U3',
                            'loser_next_position' => 'L3',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e1']
                        ], // 3
                        [
                            'team1_position' => 'W7',
                            'team2_position' => 'W8',
                            'winner_next_position' => 'U4',
                            'loser_next_position' => 'L4',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e1']
                        ], // 4
                        [
                            'team1_position' => 'W9',
                            'team2_position' => 'W10',
                            'winner_next_position' => 'U5',
                            'loser_next_position' => 'L6',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e1']
                        ], // 5
                        [
                            'team1_position' => 'W11',
                            'team2_position' => 'W12',
                            'winner_next_position' => 'U6',
                            'loser_next_position' => 'L6',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e1']
                        ], // 6
                        [
                            'team1_position' => 'W13',
                            'team2_position' => 'W14',
                            'winner_next_position' => 'U7',
                            'loser_next_position' => 'L7',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e1']
                        ], // 7
                        [
                            'team1_position' => 'W15',
                            'team2_position' => 'W16',
                            'winner_next_position' => 'U8',
                            'loser_next_position' => 'L8',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e1']
                        ], // 8
                        [
                            'team1_position' => 'W17',
                            'team2_position' => 'W18',
                            'winner_next_position' => 'U9',
                            'loser_next_position' => 'L9',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e1']
                        ], // 9
                        [
                            'team1_position' => 'W19',
                            'team2_position' => 'W20',
                            'winner_next_position' => 'U10',
                            'loser_next_position' => 'L10',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e1']
                        ], // 10
                        [
                            'team1_position' => 'W21',
                            'team2_position' => 'W22',
                            'winner_next_position' => 'U11',
                            'loser_next_position' => 'L11',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e1']
                        ], // 11
                        [
                            'team1_position' => 'W23',
                            'team2_position' => 'W24',
                            'winner_next_position' => 'U12',
                            'loser_next_position' => 'L12',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e1']
                        ], // 12
                        [
                            'team1_position' => 'W25',
                            'team2_position' => 'W26',
                            'winner_next_position' => 'U13',
                            'loser_next_position' => 'L13',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e1']
                        ], // 13
                        [
                            'team1_position' => 'W27',
                            'team2_position' => 'W28',
                            'winner_next_position' => 'U14',
                            'loser_next_position' => 'L14',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e1']
                        ], // 14
                        [
                            'team1_position' => 'W29',
                            'team2_position' => 'W30',
                            'winner_next_position' => 'U15',
                            'loser_next_position' => 'L15',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e1']
                        ], // 15
                        [
                            'team1_position' => 'W31',
                            'team2_position' => 'W32',
                            'winner_next_position' => 'U16',
                            'loser_next_position' => 'L16',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e1']
                        ], // 16
                    ],
                    'e2' => [
                        [
                            'team1_position' => 'U1',
                            'team2_position' => 'U2',
                            'winner_next_position' => 'U17',
                            'loser_next_position' => 'L18',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e2']
                        ], // 1
                        [
                            'team1_position' => 'U3',
                            'team2_position' => 'U4',
                            'winner_next_position' => 'U18',
                            'loser_next_position' => 'L20',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e2']
                        ], // 2
                        [
                            'team1_position' => 'U5',
                            'team2_position' => 'U6',
                            'winner_next_position' => 'U19',
                            'loser_next_position' => 'L22',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e2']
                        ], // 3
                        [
                            'team1_position' => 'U7',
                            'team2_position' => 'U8',
                            'winner_next_position' => 'U20',
                            'loser_next_position' => 'L24',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e2']
                        ], // 4
                        [
                            'team1_position' => 'U9',
                            'team2_position' => 'U10',
                            'winner_next_position' => 'U21',
                            'loser_next_position' => 'L26',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e2']
                        ], // 5
                        [
                            'team1_position' => 'U11',
                            'team2_position' => 'U12',
                            'winner_next_position' => 'U22',
                            'loser_next_position' => 'L28',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e2']
                        ], // 6
                        [
                            'team1_position' => 'U13',
                            'team2_position' => 'U14',
                            'winner_next_position' => 'U23',
                            'loser_next_position' => 'L30',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e2']
                        ], // 7
                        [
                            'team1_position' => 'U15',
                            'team2_position' => 'U16',
                            'winner_next_position' => 'U24',
                            'loser_next_position' => 'L32',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e2']
                        ], // 8
                    ],
                    'e3' => [
                        [
                            'team1_position' => 'U17',
                            'team2_position' => 'U18',
                            'winner_next_position' => 'U25',
                            'loser_next_position' => 'L42',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e3']
                        ], // 1
                        [
                            'team1_position' => 'U19',
                            'team2_position' => 'U20',
                            'winner_next_position' => 'U26',
                            'loser_next_position' => 'L44',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e3']
                        ], // 2
                        [
                            'team1_position' => 'U21',
                            'team2_position' => 'U22',
                            'winner_next_position' => 'U27',
                            'loser_next_position' => 'L46',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e3']
                        ], // 3
                        [
                            'team1_position' => 'U23',
                            'team2_position' => 'U24',
                            'winner_next_position' => 'U28',
                            'loser_next_position' => 'L48',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e3']
                        ], // 4
                    ],
                    'e4' => [
                        [
                            'team1_position' => 'U25',
                            'team2_position' => 'U26',
                            'winner_next_position' => 'U29',
                            'loser_next_position' => 'L54',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e4']
                        ], // 1
                        [
                            'team1_position' => 'U27',
                            'team2_position' => 'U28',
                            'winner_next_position' => 'U30',
                            'loser_next_position' => 'L56',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e4']
                        ], // 2
                    ],
                    'p0' => [
                        [
                            'team1_position' => 'U29',
                            'team2_position' => 'U30',
                            'winner_next_position' => 'G1',
                            'loser_next_position' => 'L60',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['p0']
                        ], // 1
                    ],
                ],
                'L' => [
                    'e1' => [
                        [
                            'team1_position' => 'L1',
                            'team2_position' => 'L2',
                            'winner_next_position' => 'L17',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['e1']
                        ], // 1
                        [
                            'team1_position' => 'L3',
                            'team2_position' => 'L4',
                            'winner_next_position' => 'L19',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['e1']
                        ], // 2
                        [
                            'team1_position' => 'L5',
                            'team2_position' => 'L6',
                            'winner_next_position' => 'L21',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['e1']
                        ], // 3
                        [
                            'team1_position' => 'L7',
                            'team2_position' => 'L8',
                            'winner_next_position' => 'L23',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['e1']
                        ], // 4
                        [
                            'team1_position' => 'L9',
                            'team2_position' => 'L10',
                            'winner_next_position' => 'L25',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['e1']
                        ], // 5
                        [
                            'team1_position' => 'L11',
                            'team2_position' => 'L12',
                            'winner_next_position' => 'L27',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['e1']
                        ], // 6
                        [
                            'team1_position' => 'L13',
                            'team2_position' => 'L14',
                            'winner_next_position' => 'L29',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['e1']
                        ], // 7
                        [
                            'team1_position' => 'L15',
                            'team2_position' => 'L16',
                            'winner_next_position' => 'L31',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['e1']
                        ], // 8
                    ],
                    'e2' => [
                        [
                            'team1_position' => 'L17',
                            'team2_position' => 'L18',
                            'winner_next_position' => 'L33',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['e2']
                        ], // 1
                        [
                            'team1_position' => 'L19',
                            'team2_position' => 'L20',
                            'winner_next_position' => 'L34',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['e2']
                        ], // 2
                        [
                            'team1_position' => 'L21',
                            'team2_position' => 'L22',
                            'winner_next_position' => 'L35',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['e2']
                        ], // 3
                        [
                            'team1_position' => 'L23',
                            'team2_position' => 'L24',
                            'winner_next_position' => 'L36',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['e2']
                        ], // 4
                        [
                            'team1_position' => 'L25',
                            'team2_position' => 'L26',
                            'winner_next_position' => 'L37',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['e2']
                        ], // 5
                        [
                            'team1_position' => 'L27',
                            'team2_position' => 'L28',
                            'winner_next_position' => 'L38',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['e2']
                        ], // 6
                        [
                            'team1_position' => 'L29',
                            'team2_position' => 'L30',
                            'winner_next_position' => 'L39',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['e2']
                        ], // 7
                        [
                            'team1_position' => 'L31',
                            'team2_position' => 'L32',
                            'winner_next_position' => 'L40',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['e2']
                        ], // 8
                    ],
                    'e3' => [
                        [
                            'team1_position' => 'L33',
                            'team2_position' => 'L34',
                            'winner_next_position' => 'L41',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['e3']
                        ], // 1
                        [
                            'team1_position' => 'L35',
                            'team2_position' => 'L36',
                            'winner_next_position' => 'L43',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['e3']

                        ], // 2
                        [
                            'team1_position' => 'L37',
                            'team2_position' => 'L38',
                            'winner_next_position' => 'L45',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['e3']

                        ], // 3
                        [
                            'team1_position' => 'L39',
                            'team2_position' => 'L40',
                            'winner_next_position' => 'L47',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['e3']

                        ], // 4
                    ],
                    'e4' => [
                        [
                            'team1_position' => 'L41',
                            'team2_position' => 'L42',
                            'winner_next_position' => 'L49',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['e4']

                        ], // 1
                        [
                            'team1_position' => 'L43',
                            'team2_position' => 'L44',
                            'winner_next_position' => 'L50',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['e4']

                        ], // 2
                        [
                            'team1_position' => 'L45',
                            'team2_position' => 'L46',
                            'winner_next_position' => 'L51',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['e4']

                        ], // 3
                        [
                            'team1_position' => 'L47',
                            'team2_position' => 'L48',
                            'winner_next_position' => 'L52',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['e4']

                        ], // 4
                    ],
                    'e5' => [
                        [
                            'team1_position' => 'L49',
                            'team2_position' => 'L50',
                            'winner_next_position' => 'L53',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['e5']

                        ], // 1
                        [
                            'team1_position' => 'L51',
                            'team2_position' => 'L52',
                            'winner_next_position' => 'L55',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['e5']

                        ], // 2
                    ],
                    'e6' => [
                        [
                            'team1_position' => 'L53',
                            'team2_position' => 'L54',
                            'winner_next_position' => 'L57',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['e6']

                        ], // 1
                        [
                            'team1_position' => 'L55',
                            'team2_position' => 'L56',
                            'winner_next_position' => 'L58',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['e6']

                        ], // 2
                    ],
                    'p1' => [
                        [
                            'team1_position' => 'L57',
                            'team2_position' => 'L58',
                            'winner_next_position' => 'L59',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['p1']

                        ], // 1
                    ],
                    'p2' => [
                        [
                            'team1_position' => 'L59',
                            'team2_position' => 'L60',
                            'winner_next_position' => 'G2',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['p2']

                        ], // 1
                    ],
                ],
            ];
        } elseif ($teamNumber == 16) {
            return [
                'F' => [
                    'F' => [[                       
                        'team1_position' => 'G1',
                        'team2_position' => 'G2',
                        'winner_next_position' => 'F',
                        'loser_next_position' => null,
                        ...$defaultValues,
                        'deadline' => $deadlines['F']['F']

                    ]],
                    'W' => [[
                        'team1_position' => 'F',
                        'team2_position' => null,
                        'winner_next_position' => null,
                        'loser_next_position' => null,
                        ...$defaultValues,
                        'deadline' => $deadlines['F']['W']
                    ]]
                ],
                'U' => [
                    'e1' => [
                        [
                            'team1_position' => 'W1',
                            'team2_position' => 'W2',
                            'winner_next_position' => 'U1',
                            'loser_next_position' => 'L1',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e1']

                        ], // 1
                        [
                            'team1_position' => 'W3',
                            'team2_position' => 'W4',
                            'winner_next_position' => 'U2',
                            'loser_next_position' => 'L2',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e1']

                        ], // 2
                        [
                            'team1_position' => 'W5',
                            'team2_position' => 'W6',
                            'winner_next_position' => 'U3',
                            'loser_next_position' => 'L3',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e1']

                        ], // 3
                        [
                            'team1_position' => 'W7',
                            'team2_position' => 'W8',
                            'winner_next_position' => 'U4',
                            'loser_next_position' => 'L4',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e1']

                        ], // 4
                        [
                            'team1_position' => 'W9',
                            'team2_position' => 'W10',
                            'winner_next_position' => 'U5',
                            'loser_next_position' => 'L6',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e1']

                        ], // 5
                        [
                            'team1_position' => 'W11',
                            'team2_position' => 'W12',
                            'winner_next_position' => 'U6',
                            'loser_next_position' => 'L6',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e1']

                        ], // 6
                        [
                            'team1_position' => 'W13',
                            'team2_position' => 'W14',
                            'winner_next_position' => 'U7',
                            'loser_next_position' => 'L7',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e1']

                        ], // 7
                        [
                            'team1_position' => 'W15',
                            'team2_position' => 'W16',
                            'winner_next_position' => 'U8',
                            'loser_next_position' => 'L8',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e1']

                        ], // 8
                    ],
                    'e2' => [
                        [
                            'team1_position' => 'U1',
                            'team2_position' => 'U2',
                            'winner_next_position' => 'U9',
                            'loser_next_position' => 'L10',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e2']

                        ], // 1
                        [
                            'team1_position' => 'U3',
                            'team2_position' => 'U4',
                            'winner_next_position' => 'U10',
                            'loser_next_position' => 'L12',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e2']

                        ], // 2
                        [
                            'team1_position' => 'U5',
                            'team2_position' => 'U6',
                            'winner_next_position' => 'U11',
                            'loser_next_position' => 'L14',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e2']
                        ], // 3
                        [
                            'team1_position' => 'U7',
                            'team2_position' => 'U8',
                            'order' => 4,
                            'winner_next_position' => 'U12',
                            'loser_next_position' => 'L16',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e2']
                        ], // 4
                    ],
                    'e3' => [
                        [
                            'team1_position' => 'U9',
                            'team2_position' => 'U10',
                            'winner_next_position' => 'U13',
                            'loser_next_position' => 'L22',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e3']
                        ], // 1
                        [
                            'team1_position' => 'U11',
                            'team2_position' => 'U12',
                            'winner_next_position' => 'U14',
                            'loser_next_position' => 'L24',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e3']
                        ], // 2
                    ],
                    'p0' => [
                        [
                            'team1_position' => 'U13',
                            'team2_position' => 'U14',
                            'winner_next_position' => 'G1',
                            'loser_next_position' => 'L28',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['p0']
                        ], // 1
                    ],
                ],
                'L' => [
                    'e1' => [
                        [
                            'team1_position' => 'L1',
                            'team2_position' => 'L2',
                            'winner_next_position' => 'L9',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['e1']
                        ], // 1
                        [
                            'team1_position' => 'L3',
                            'team2_position' => 'L4',
                            'winner_next_position' => 'L11',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['e1']
                        ], // 2
                        [
                            'team1_position' => 'L5',
                            'team2_position' => 'L6',
                            'winner_next_position' => 'L13',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['e1']
                        ], // 3
                        [
                            'team1_position' => 'L7',
                            'team2_position' => 'L8',
                            'winner_next_position' => 'L15',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['e1']
                        ], // 4
                    ],
                    'e2' => [
                        [
                            'team1_position' => 'L9',
                            'team2_position' => 'L10',
                            'winner_next_position' => 'L17',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['e2']
                        ], // 1
                        [
                            'team1_position' => 'L11',
                            'team2_position' => 'L12',
                            'winner_next_position' => 'L18',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['e2']
                        ], // 2
                        [
                            'team1_position' => 'L13',
                            'team2_position' => 'L14',
                            'winner_next_position' => 'L19',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['e2']
                        ], // 3
                        [
                            'team1_position' => 'L15',
                            'team2_position' => 'L16',
                            'winner_next_position' => 'L20',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['e2']
                        ], // 4
                    ],
                    'e3' => [
                        [
                            'team1_position' => 'L17',
                            'team2_position' => 'L18',
                            'winner_next_position' => 'L21',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['e3']
                        ], // 1
                        [
                            'team1_position' => 'L19',
                            'team2_position' => 'L20',
                            'winner_next_position' => 'L23',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['e3']
                        ], // 2
                    ],
                    'e4' => [
                        [
                            'team1_position' => 'L21',
                            'team2_position' => 'L22',
                            'winner_next_position' => 'L25',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['e4']
                        ], // 1
                        [
                            'team1_position' => 'L23',
                            'team2_position' => 'L24',
                            'winner_next_position' => 'L26',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['e4']
                        ], // 2
                    ],
                    'p1' => [
                        [
                            'team1_position' => 'L25',
                            'team2_position' => 'L26',
                            'winner_next_position' => 'L27',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['p1']
                        ], // 1
                    ],
                    'p2' => [
                        [
                            'team1_position' => 'L27',
                            'team2_position' => 'L28',
                            'winner_next_position' => 'G2',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['p2']
                        ], // 1
                    ],
                ],
            ];
        } else { 
            return [
                'F' => [
                    'F' => [[
                        'team1_position' => 'G1',
                        'team2_position' => 'G2',
                        'winner_next_position' => 'F',
                        'loser_next_position' => null,
                        ...$defaultValues,
                        'deadline' => $deadlines['F']['F']
                    ]],
                    'W' => [[
                        'team1_position' => 'F',
                        'team2_position' => null,
                        'winner_next_position' => null,
                        'loser_next_position' => null,
                        ...$defaultValues,
                        'deadline' => $deadlines['F']['W']
                    ]]
                ],
                'U' => [
                    'e1' => [
                        [
                            'team1_position' => 'W1',
                            'team2_position' => 'W2',
                            'winner_next_position' => 'U1',
                            'loser_next_position' => 'L1',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e1']
                        ], // 1
                        [
                            'team1_position' => 'W3',
                            'team2_position' => 'W4',
                            'winner_next_position' => 'U2',
                            'loser_next_position' => 'L2',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e1']
                        ], // 2
                        [
                            'team1_position' => 'W5',
                            'team2_position' => 'W6',
                            'winner_next_position' => 'U3',
                            'loser_next_position' => 'L3',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e1']
                        ], // 3
                        [
                            'team1_position' => 'W7',
                            'team2_position' => 'W8',
                            'winner_next_position' => 'U4',
                            'loser_next_position' => 'L4',
                            ...$defaultValues,
                            'deadline' => $deadlines['U']['e1']
                        ], // 4
                    ],
                    'e2' => [
                        [
                            'team1_position' => 'U1',
                            'team2_position' => 'U2',
                            ...$defaultValues,
                            'winner_next_position' => 'U5',
                            'loser_next_position' => 'L6',
                            'deadline' => $deadlines['U']['e2']
                        ], // 1
                        [
                            'team1_position' => 'U3',
                            'team2_position' => 'U4',
                            ...$defaultValues,
                            'winner_next_position' => 'U6',
                            'loser_next_position' => 'L8',
                            'deadline' => $deadlines['U']['e2']
                        ], // 2
                    ],
                    'p0' => [
                        [
                            'team1_position' => 'U5',
                            'team2_position' => 'U6',
                            ...$defaultValues,
                            'winner_next_position' => 'G1',
                            'loser_next_position' => 'L12',
                            'deadline' => $deadlines['U']['p0']
                        ], // 1
                    ],
                ],
                'L' => [
                    'e1' => [
                        [
                            'team1_position' => 'L1',
                            'team2_position' => 'L2',
                            ...$defaultValues,
                            'winner_next_position' => 'L5',
                            'loser_next_position' => null,
                            'deadline' => $deadlines['L']['e1']
                        ], // 1
                        [
                            'team1_position' => 'L3',
                            'team2_position' => 'L4',
                            ...$defaultValues,
                            'winner_next_position' => 'L7',
                            'loser_next_position' => null,
                            'deadline' => $deadlines['L']['e1']
                        ], // 2
                    ],
                    'e2' => [
                        [
                            'team1_position' => 'L5',
                            'team2_position' => 'L6',
                            'winner_next_position' => 'L9',
                            ...$defaultValues,
                            'loser_next_position' => null,
                            'deadline' => $deadlines['L']['e2']
                        ], // 1
                        [
                            'team1_position' => 'L7',
                            'team2_position' => 'L8',
                            'winner_next_position' => 'L10',
                            ...$defaultValues,
                            'loser_next_position' => null,
                            'deadline' => $deadlines['L']['e2']
                        ], // 2
                    ],
                    'p1' => [
                        [
                            'team1_position' => 'L9',
                            'team2_position' => 'L10',
                            'winner_next_position' => 'L11',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['p1']
                        ], // 1
                    ],
                    'p2' => [
                        [
                            'team1_position' => 'L11',
                            'team2_position' => 'L12',
                            'winner_next_position' => 'G2',
                            'loser_next_position' => null,
                            ...$defaultValues,
                            'deadline' => $deadlines['L']['p2']
                        ], // 1
                    ],
                ],
            ];
        }
    }
}
