<?php
namespace App\Http\Livewire\Shared\data;

class BracketData
{
    public function getData(int $membersCount)
    {
    
    if ($membersCount === 32) {
        return [
            'doubleElimination' => [
                'finals' => [
                    'finals' => [
                        [
                            'team1_position' => 'G1',
                            'team2_position' => 'G2',
                            'order' => 1,
                            'winner_next_position' => null,
                            'loser_next_position' => null,
                        ]
                    ], 
                ],
                'upperBracket' => [
                    'eliminator1' => [
                        [
                            'team1_position' => '',
                            'team2_position' => '',
                            'order' => 1,
                            'winner_next_position' => 'U1',
                            'loser_next_position' => 'L1',
                        ], // 1
                        [
                            'team1_position' => '',
                            'team2_position' => '',
                            'order' => 2,
                            'winner_next_position' => 'U2',
                            'loser_next_position' => 'L2',
                        ], // 2
                        [
                            'team1_position' => '',
                            'team2_position' => '',
                            'order' => 3,
                            'winner_next_position' => 'U3',
                            'loser_next_position' => 'L3',
                        ], // 3
                        [
                            'team1_position' => '',
                            'team2_position' => '',
                            'order' => 4,
                            'winner_next_position' => 'U4',
                            'loser_next_position' => 'L4',
                        ], // 4
                        [
                            'team1_position' => '',
                            'team2_position' => '',
                            'order' => 5,
                            'winner_next_position' => 'U5',
                            'loser_next_position' => 'L6',
                        ], // 5
                        [
                            'team1_position' => '',
                            'team2_position' => '',
                            'order' => 6,
                            'winner_next_position' => 'U6',
                            'loser_next_position' => 'L6',
                        ], // 6
                        [
                            'team1_position' => '',
                            'team2_position' => '',
                            'order' => 7,
                            'winner_next_position' => 'U7',
                            'loser_next_position' => 'L7',
                        ], // 7
                        [
                            'team1_position' => '',
                            'team2_position' => '',
                            'order' => 8,
                            'winner_next_position' => 'U8',
                            'loser_next_position' => 'L8',
                        ], // 8
                        [
                            'team1_position' => '',
                            'team2_position' => '',
                            'order' => 9,
                            'winner_next_position' => 'U9',
                            'loser_next_position' => 'L9',
                        ], // 9
                        [
                            'team1_position' => '',
                            'team2_position' => '',
                            'order' => 10,
                            'winner_next_position' => 'U10',
                            'loser_next_position' => 'L10',
                        ], // 10
                        [
                            'team1_position' => '',
                            'team2_position' => '',
                            'order' => 11,
                            'winner_next_position' => 'U11',
                            'loser_next_position' => 'L11',
                        ], // 11
                        [
                            'team1_position' => '',
                            'team2_position' => '',
                            'order' => 12,
                            'winner_next_position' => 'U12',
                            'loser_next_position' => 'L12',
                        ], // 12
                        [
                            'team1_position' => '',
                            'team2_position' => '',
                            'order' => 13,
                            'winner_next_position' => 'U13',
                            'loser_next_position' => 'L13',
                        ], // 13
                        [
                            'team1_position' => '',
                            'team2_position' => '',
                            'order' => 14,
                            'winner_next_position' => 'U14',
                            'loser_next_position' => 'L14',
                        ], // 14
                        [
                            'team1_position' => '',
                            'team2_position' => '',
                            'order' => 15,
                            'winner_next_position' => 'U15',
                            'loser_next_position' => 'L15',
                        ], // 15
                        [
                            'team1_position' => '',
                            'team2_position' => '',
                            'order' => 16,
                            'winner_next_position' => 'U16',
                            'loser_next_position' => 'L16',
                        ], // 16
                    ],
                    'eliminator2' => [
                        [
                            'team1_position' => 'U1',
                            'team2_position' => 'U2',
                            'order' => 1,
                            'winner_next_position' => 'U17',
                            'loser_next_position' => 'L18',
                        ], // 1
                        [
                            'team1_position' => 'U3',
                            'team2_position' => 'U4',
                            'order' => 2,
                            'winner_next_position' => 'U18',
                            'loser_next_position' => 'L20',
                        ], // 2
                        [
                            'team1_position' => 'U5',
                            'team2_position' => 'U6',
                            'order' => 3,
                            'winner_next_position' => 'U19',
                            'loser_next_position' => 'L22',
                        ], // 3
                        [
                            'team1_position' => 'U7',
                            'team2_position' => 'U8',
                            'order' => 4,
                            'winner_next_position' => 'U20',
                            'loser_next_position' => 'L24',
                        ], // 4
                        [
                            'team1_position' => 'U9',
                            'team2_position' => 'U10',
                            'order' => 5,
                            'winner_next_position' => 'U21',
                            'loser_next_position' => 'L26',
                        ], // 5
                        [
                            'team1_position' => 'U11',
                            'team2_position' => 'U12',
                            'order' => 6,
                            'winner_next_position' => 'U22',
                            'loser_next_position' => 'L28',
                        ], // 6
                        [
                            'team1_position' => 'U13',
                            'team2_position' => 'U14',
                            'order' => 7,
                            'winner_next_position' => 'U23',
                            'loser_next_position' => 'L30',
                        ], // 7
                        [
                            'team1_position' => 'U15',
                            'team2_position' => 'U16',
                            'order' => 8,
                            'winner_next_position' => 'U24',
                            'loser_next_position' => 'L32',
                        ], // 8
                    ],
                    'eliminator3' => [
                        [
                            'team1_position' => 'U17',
                            'team2_position' => 'U18',
                            'order' => 1,
                            'winner_next_position' => 'U25',
                            'loser_next_position' => 'L42',
                        ], // 1
                        [
                            'team1_position' => 'U19',
                            'team2_position' => 'U20',
                            'order' => 2,
                            'winner_next_position' => 'U26',
                            'loser_next_position' => 'L44',
                        ], // 2
                        [
                            'team1_position' => 'U21',
                            'team2_position' => 'U22',
                            'order' => 3,
                            'winner_next_position' => 'U27',
                            'loser_next_position' => 'L46',
                        ], // 3
                        [
                            'team1_position' => 'U23',
                            'team2_position' => 'U24',
                            'order' => 4,
                            'winner_next_position' => 'U28',
                            'loser_next_position' => 'L48',
                        ], // 4
                    ],
                    'eliminator4' => [
                        [
                            'team1_position' => 'U25',
                            'team2_position' => 'U26',
                            'order' => 1,
                            'winner_next_position' => 'U29',
                            'loser_next_position' => 'L54',
                        ], // 1
                        [
                            'team1_position' => 'U27',
                            'team2_position' => 'U28',
                            'order' => 2,
                            'winner_next_position' => 'U30',
                            'loser_next_position' => 'L56',
                        ], // 2
                    ],
                    'prefinals' => [
                        [
                            'team1_position' => 'U29',
                            'team2_position' => 'U30',
                            'order' => 1,
                            'winner_next_position' => 'G1',
                            'loser_next_position' => 'L60',
                        ], // 1
                    ],
                ],
                'lowerBracket' => [
                    'eliminator1' => [
                        [
                            'team1_position' => 'L1',
                            'team2_position' => 'L2',
                            'order' => 1,
                            'winner_next_position' => 'L17',
                            'loser_next_position' => null,
                        ], // 1
                        [
                            'team1_position' => 'L3',
                            'team2_position' => 'L4',
                            'order' => 2,
                            'winner_next_position' => 'L19',
                            'loser_next_position' => null,
                        ], // 2
                        [
                            'team1_position' => 'L5',
                            'team2_position' => 'L6',
                            'order' => 3,
                            'winner_next_position' => 'L21',
                            'loser_next_position' => null,
                        ], // 3
                        [
                            'team1_position' => 'L7',
                            'team2_position' => 'L8',
                            'order' => 4,
                            'winner_next_position' => 'L23',
                            'loser_next_position' => null,
                        ], // 4
                        [
                            'team1_position' => 'L9',
                            'team2_position' => 'L10',
                            'order' => 5,
                            'winner_next_position' => 'L25',
                            'loser_next_position' => null,
                        ], // 5
                        [
                            'team1_position' => 'L11',
                            'team2_position' => 'L12',
                            'order' => 6,
                            'winner_next_position' => 'L27',
                            'loser_next_position' => null,
                        ], // 6
                        [
                            'team1_position' => 'L13',
                            'team2_position' => 'L14',
                            'order' => 7,
                            'winner_next_position' => 'L29',
                            'loser_next_position' => null,
                        ], // 7
                        [
                            'team1_position' => 'L15',
                            'team2_position' => 'L16',
                            'order' => 8,
                            'winner_next_position' => 'L31',
                            'loser_next_position' => null,
                        ], // 8
                    ],
                    'eliminator2' => [
                        [
                            'team1_position' => 'L17',
                            'team2_position' => 'L18',
                            'winner_next_position' => 'L33',
                            'loser_next_position' => null,
                            'order' => 1,
                        ], // 1
                        [
                            'team1_position' => 'L19',
                            'team2_position' => 'L20',
                            'winner_next_position' => 'L34',
                            'loser_next_position' => null,
                            'order' => 2
                        ], // 2
                        [
                            'team1_position' => 'L21',
                            'team2_position' => 'L22',
                            'winner_next_position' => 'L35',
                            'loser_next_position' => null,
                            'order' => 3
                        ], // 3
                        [
                            'team1_position' => 'L23',
                            'team2_position' => 'L24',
                            'winner_next_position' => 'L36',
                            'loser_next_position' => null,
                            'order' => 4
                        ], // 4
                        [
                            'team1_position' => 'L25',
                            'team2_position' => 'L26',
                            'winner_next_position' => 'L37',
                            'loser_next_position' => null,
                            'order' => 5
                        ], // 5
                        [
                            'team1_position' => 'L27',
                            'team2_position' => 'L28',
                            'winner_next_position' => 'L38',
                            'loser_next_position' => null,
                            'order' => 6
                        ], // 6
                        [
                            'team1_position' => 'L29',
                            'team2_position' => 'L30',
                            'winner_next_position' => 'L39',
                            'loser_next_position' => null,
                            'order' => 7
                        ], // 7
                        [
                            'team1_position' => 'L31',
                            'team2_position' => 'L32',
                            'winner_next_position' => 'L40',
                            'loser_next_position' => null,
                            'order' => 8
                        ], // 8
                    ],
                    'eliminator3' => [
                        [
                            'team1_position' => 'L33',
                            'team2_position' => 'L34',
                            'winner_next_position' => 'L41',
                            'loser_next_position' => null,
                            'order' => 1,
                        ], // 1
                        [
                            'team1_position' => 'L35',
                            'team2_position' => 'L36',
                            'winner_next_position' => 'L43',
                            'loser_next_position' => null,
                            'order' => 2
                        ], // 2
                        [
                            'team1_position' => 'L37',
                            'team2_position' => 'L38',
                            'winner_next_position' => 'L45',
                            'loser_next_position' => null,
                            'order' => 3
                        ], // 3
                        [
                            'team1_position' => 'L39',
                            'team2_position' => 'L40',
                            'winner_next_position' => 'L47',
                            'loser_next_position' => null,
                            'order' => 4
                        ], // 4
                    ],
                    'eliminator4' => [
                        [
                            'team1_position' => 'L41',
                            'team2_position' => 'L42',
                            'winner_next_position' => 'L49',
                            'loser_next_position' => null,
                            'order' => 1,
                        ], // 1
                        [
                            'team1_position' => 'L43',
                            'team2_position' => 'L44',
                            'winner_next_position' => 'L50',
                            'loser_next_position' => null,
                            'order' => 2,
                        ], // 2
                        [
                            'team1_position' => 'L45',
                            'team2_position' => 'L46',
                            'winner_next_position' => 'L51',
                            'loser_next_position' => null,
                            'order' => 3
                        ], // 3
                        [
                            'team1_position' => 'L47',
                            'team2_position' => 'L48',
                            'winner_next_position' => 'L52',
                            'loser_next_position' => null,
                            'order' => 4
                        ], // 4
                    ],
                    'eliminator5' => [
                        [
                            'team1_position' => 'L49',
                            'team2_position' => 'L50',
                            'winner_next_position' => 'L53',
                            'loser_next_position' => null,
                            'order' => 1
                        ], // 1
                        [
                            'team1_position' => 'L51',
                            'team2_position' => 'L52',
                            'winner_next_position' => 'L55',
                            'loser_next_position' => null,
                            'order' => 2,
                        ], // 2
                    ],
                    'eliminator6' => [
                        [
                            'team1_position' => 'L53',
                            'team2_position' => 'L54',
                            'winner_next_position' => 'L57',
                            'loser_next_position' => null,
                            'order' => 1,
                        ], // 1
                        [
                            'team1_position' => 'L55',
                            'team2_position' => 'L56',
                            'winner_next_position' => 'L58',
                            'loser_next_position' => null,
                            'order' => 2,
                        ], // 2
                    ],
                    'prefinals1' => [
                        [
                            'team1_position' => 'L57',
                            'team2_position' => 'L58',
                            'winner_next_position' => 'L59',
                            'loser_next_position' => null,
                            'order' => 1,
                        ], // 1
                    ],
                    'prefinals2' => [
                        [
                            'team1_position' => 'L59',
                            'team2_position' => 'L60',
                            'winner_next_position' => 'G2',
                            'loser_next_position' => null,
                            'order' => 1,
                        ], // 1
                    ],
                ],
            ],
        ];
    }

    if ($membersCount === 16) {
        return [
            'doubleElimination' => [
                'finals' => [
                    'finals' => [
                        [
                            'team1_position' => 'G1',
                            'team2_position' => 'G2',
                            'order' => 1,
                            'winner_next_position' => null,
                            'loser_next_position' => null,
                        ]
                    ], 
                ],
                'upperBracket' => [
                    'eliminator1' => [
                        [
                            'team1_position' => '',
                            'team2_position' => '',
                            'order' => 1,
                            'winner_next_position' => 'U1',
                            'loser_next_position' => 'L1',
                        ], // 1
                        [
                            'team1_position' => '',
                            'team2_position' => '',
                            'order' => 2,
                            'winner_next_position' => 'U2',
                            'loser_next_position' => 'L2',
                        ], // 2
                        [
                            'team1_position' => '',
                            'team2_position' => '',
                            'order' => 3,
                            'winner_next_position' => 'U3',
                            'loser_next_position' => 'L3',
                        ], // 3
                        [
                            'team1_position' => '',
                            'team2_position' => '',
                            'order' => 4,
                            'winner_next_position' => 'U4',
                            'loser_next_position' => 'L4',
                        ], // 4
                        [
                            'team1_position' => '',
                            'team2_position' => '',
                            'order' => 5,
                            'winner_next_position' => 'U5',
                            'loser_next_position' => 'L6',
                        ], // 5
                        [
                            'team1_position' => '',
                            'team2_position' => '',
                            'order' => 6,
                            'winner_next_position' => 'U6',
                            'loser_next_position' => 'L6',
                        ], // 6
                        [
                            'team1_position' => '',
                            'team2_position' => '',
                            'order' => 7,
                            'winner_next_position' => 'U7',
                            'loser_next_position' => 'L7',
                        ], // 7
                        [
                            'team1_position' => '',
                            'team2_position' => '',
                            'order' => 8,
                            'winner_next_position' => 'U8',
                            'loser_next_position' => 'L8',
                        ], // 8
                    ],
                    'eliminator2' => [
                        [
                            'team1_position' => 'U1',
                            'team2_position' => 'U2',
                            'order' => 1,
                            'winner_next_position' => 'U9',
                            'loser_next_position' => 'L10',
                        ], // 1
                        [
                            'team1_position' => 'U3',
                            'team2_position' => 'U4',
                            'order' => 2,
                            'winner_next_position' => 'U10',
                            'loser_next_position' => 'L12',
                        ], // 2
                        [
                            'team1_position' => 'U5',
                            'team2_position' => 'U6',
                            'order' => 3,
                            'winner_next_position' => 'U11',
                            'loser_next_position' => 'L14',
                        ], // 3
                        [
                            'team1_position' => 'U7',
                            'team2_position' => 'U8',
                            'order' => 4,
                            'winner_next_position' => 'U12',
                            'loser_next_position' => 'L16',
                        ], // 4
                    ],
                    'eliminator3' => [
                        [
                            'team1_position' => 'U9',
                            'team2_position' => 'U10',
                            'order' => 1,
                            'winner_next_position' => 'U13',
                            'loser_next_position' => 'L22',
                        ], // 1
                        [
                            'team1_position' => 'U11',
                            'team2_position' => 'U12',
                            'order' => 2,
                            'winner_next_position' => 'U14',
                            'loser_next_position' => 'L24',
                        ], // 2
                    ],
                    'prefinals' => [
                        [
                            'team1_position' => 'U13',
                            'team2_position' => 'U14',
                            'order' => 1,
                            'winner_next_position' => 'G1',
                            'loser_next_position' => 'L28',
                        ], // 1
                    ],
                ],
                'lowerBracket' => [
                    'eliminator1' => [
                        [
                            'team1_position' => 'L1',
                            'team2_position' => 'L2',
                            'order' => 1,
                            'winner_next_position' => 'L9',
                            'loser_next_position' => null,
                        ], // 1
                        [
                            'team1_position' => 'L3',
                            'team2_position' => 'L4',
                            'order' => 2,
                            'winner_next_position' => 'L10',
                            'loser_next_position' => null,
                        ], // 2
                        [
                            'team1_position' => 'L5',
                            'team2_position' => 'L6',
                            'order' => 3,
                            'winner_next_position' => 'L11',
                            'loser_next_position' => null,
                        ], // 3
                        [
                            'team1_position' => 'L7',
                            'team2_position' => 'L8',
                            'order' => 4,
                            'winner_next_position' => 'L12',
                            'loser_next_position' => null,
                        ], // 4
                    ],
                    'eliminator2' => [
                        [
                            'team1_position' => 'L9',
                            'team2_position' => 'L10',
                            'winner_next_position' => 'L17',
                            'loser_next_position' => null,
                            'order' => 1,
                        ], // 1
                        [
                            'team1_position' => 'L11',
                            'team2_position' => 'L12',
                            'winner_next_position' => 'L18',
                            'loser_next_position' => null,
                            'order' => 2
                        ], // 2
                        [
                            'team1_position' => 'L13',
                            'team2_position' => 'L14',
                            'winner_next_position' => 'L19',
                            'loser_next_position' => null,
                            'order' => 3
                        ], // 3
                        [
                            'team1_position' => 'L15',
                            'team2_position' => 'L16',
                            'winner_next_position' => 'L20',
                            'loser_next_position' => null,
                            'order' => 4
                        ], // 4
                    ],
                    'eliminator3' => [
                        [
                            'team1_position' => 'L17',
                            'team2_position' => 'L18',
                            'winner_next_position' => 'L21',
                            'loser_next_position' => null,
                            'order' => 1,
                        ], // 1
                        [
                            'team1_position' => 'L19',
                            'team2_position' => 'L20',
                            'winner_next_position' => 'L23',
                            'loser_next_position' => null,
                            'order' => 2
                        ], // 2
                    ],
                    'eliminator4' => [
                        [
                            'team1_position' => 'L21',
                            'team2_position' => 'L22',
                            'winner_next_position' => 'L25',
                            'loser_next_position' => null,
                            'order' => 1,
                        ], // 1
                        [
                            'team1_position' => 'L23',
                            'team2_position' => 'L24',
                            'winner_next_position' => 'L26',
                            'loser_next_position' => null,
                            'order' => 2,
                        ], // 2
                    ],
                    'prefinals1' => [
                        [
                            'team1_position' => 'L25',
                            'team2_position' => 'L26',
                            'winner_next_position' => 'L53',
                            'loser_next_position' => null,
                            'order' => 1,
                        ], // 1
                    ],
                    'prefinals2' => [
                        [
                            'team1_position' => 'L27',
                            'team2_position' => 'L28',
                            'winner_next_position' => 'L27',
                            'loser_next_position' => null,
                            'order' => 1,
                        ], // 1
                    ],
                ],
            ],
        ];
    }

    if ($membersCount === 8) {
        return [
            'doubleElimination' => [
                'finals' => [
                    'finals' => [
                        [
                            'team1_position' => 'G1',
                            'team2_position' => 'G2',
                            'order' => 1,
                            'winner_next_position' => null,
                            'loser_next_position' => null,
                        ]
                    ], 
                ],
                'upperBracket' => [
                    'eliminator1' => [
                        [
                            'team1_position' => '',
                            'team2_position' => '',
                            'order' => 1,
                            'winner_next_position' => 'U1',
                            'loser_next_position' => 'L1',
                        ], // 1
                        [
                            'team1_position' => '',
                            'team2_position' => '',
                            'order' => 2,
                            'winner_next_position' => 'U2',
                            'loser_next_position' => 'L2',
                        ], // 2
                        [
                            'team1_position' => '',
                            'team2_position' => '',
                            'order' => 3,
                            'winner_next_position' => 'U3',
                            'loser_next_position' => 'L3',
                        ], // 3
                        [
                            'team1_position' => '',
                            'team2_position' => '',
                            'order' => 4,
                            'winner_next_position' => 'U4',
                            'loser_next_position' => 'L4',
                        ], // 4
                    ],
                    'eliminator2' => [
                        [
                            'team1_position' => 'U1',
                            'team2_position' => 'U2',
                            'order' => 1,
                            'winner_next_position' => 'U5',
                            'loser_next_position' => 'L6',
                        ], // 1
                        [
                            'team1_position' => 'U3',
                            'team2_position' => 'U4',
                            'order' => 2,
                            'winner_next_position' => 'U6',
                            'loser_next_position' => 'L8',
                        ], // 2
                    ],
                    'prefinals' => [
                        [
                            'team1_position' => 'U5',
                            'team2_position' => 'U6',
                            'order' => 1,
                            'winner_next_position' => 'G1',
                            'loser_next_position' => 'L12',
                        ], // 1
                    ],
                ],
                'lowerBracket' => [
                    'eliminator1' => [
                        [
                            'team1_position' => 'L1',
                            'team2_position' => 'L2',
                            'order' => 1,
                            'winner_next_position' => 'L5',
                            'loser_next_position' => null,
                        ], // 1
                        [
                            'team1_position' => 'L3',
                            'team2_position' => 'L4',
                            'order' => 2,
                            'winner_next_position' => 'L7',
                            'loser_next_position' => null,
                        ], // 2
                    ],
                    'eliminator2' => [
                        [
                            'team1_position' => 'L5',
                            'team2_position' => 'L6',
                            'winner_next_position' => 'L9',
                            'order' => 1,
                            'loser_next_position' => null,
                        ], // 1
                        [
                            'team1_position' => 'L7',
                            'team2_position' => 'L8',
                            'winner_next_position' => 'L10',
                            'order' => 2,
                            'loser_next_position' => null,
                        ], // 2
                    ],
                    'prefinals1' => [
                        [
                            'team1_position' => 'L9',
                            'team2_position' => 'L10',
                            'winner_next_position' => 'L11',
                            'loser_next_position' => null,
                            'order' => 1
                        ], // 1
                    ],
                    'prefinals2' => [
                        [
                            'team1_position' => 'L11',
                            'team2_position' => 'L12',
                            'winner_next_position' => 'G2',
                            'loser_next_position' => null,
                            'order' => 1,
                        ], // 1
                    ],
                ],
            ],
        ];
    }
}
}