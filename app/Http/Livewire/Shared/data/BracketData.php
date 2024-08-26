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
                        'team1Position' => 'G1',
                        'team2Position' => 'G2',
                        'order' => 1,
                        'winnerNext' => null,
                        'loserNext' => null,
                    ], 
                ],
                'upperBracket' => [
                    'eliminator1' => [
                        [
                            'team1Position' => '',
                            'team2Position' => '',
                            'order' => 1,
                            'winnerNext' => 'U1',
                            'loserNext' => 'L1',
                        ], // 1
                        [
                            'team1Position' => '',
                            'team2Position' => '',
                            'order' => 2,
                            'winnerNext' => 'U2',
                            'loserNext' => 'L2',
                        ], // 2
                        [
                            'team1Position' => '',
                            'team2Position' => '',
                            'order' => 3,
                            'winnerNext' => 'U3',
                            'loserNext' => 'L3',
                        ], // 3
                        [
                            'team1Position' => '',
                            'team2Position' => '',
                            'order' => 4,
                            'winnerNext' => 'U4',
                            'loserNext' => 'L4',
                        ], // 4
                        [
                            'team1Position' => '',
                            'team2Position' => '',
                            'order' => 5,
                            'winnerNext' => 'U5',
                            'loserNext' => 'L6',
                        ], // 5
                        [
                            'team1Position' => '',
                            'team2Position' => '',
                            'order' => 6,
                            'winnerNext' => 'U6',
                            'loserNext' => 'L6',
                        ], // 6
                        [
                            'team1Position' => '',
                            'team2Position' => '',
                            'order' => 7,
                            'winnerNext' => 'U7',
                            'loserNext' => 'L7',
                        ], // 7
                        [
                            'team1Position' => '',
                            'team2Position' => '',
                            'order' => 8,
                            'winnerNext' => 'U8',
                            'loserNext' => 'L8',
                        ], // 8
                        [
                            'team1Position' => '',
                            'team2Position' => '',
                            'order' => 9,
                            'winnerNext' => 'U9',
                            'loserNext' => 'L9',
                        ], // 9
                        [
                            'team1Position' => '',
                            'team2Position' => '',
                            'order' => 10,
                            'winnerNext' => 'U10',
                            'loserNext' => 'L10',
                        ], // 10
                        [
                            'team1Position' => '',
                            'team2Position' => '',
                            'order' => 11,
                            'winnerNext' => 'U11',
                            'loserNext' => 'L11',
                        ], // 11
                        [
                            'team1Position' => '',
                            'team2Position' => '',
                            'order' => 12,
                            'winnerNext' => 'U12',
                            'loserNext' => 'L12',
                        ], // 12
                        [
                            'team1Position' => '',
                            'team2Position' => '',
                            'order' => 13,
                            'winnerNext' => 'U13',
                            'loserNext' => 'L13',
                        ], // 13
                        [
                            'team1Position' => '',
                            'team2Position' => '',
                            'order' => 14,
                            'winnerNext' => 'U14',
                            'loserNext' => 'L14',
                        ], // 14
                        [
                            'team1Position' => '',
                            'team2Position' => '',
                            'order' => 15,
                            'winnerNext' => 'U15',
                            'loserNext' => 'L15',
                        ], // 15
                        [
                            'team1Position' => '',
                            'team2Position' => '',
                            'order' => 16,
                            'winnerNext' => 'U16',
                            'loserNext' => 'L16',
                        ], // 16
                    ],
                    'eliminator2' => [
                        [
                            'team1Position' => 'U1',
                            'team2Position' => 'U2',
                            'order' => 1,
                            'winnerNext' => 'U17',
                            'loserNext' => 'L18',
                        ], // 1
                        [
                            'team1Position' => 'U3',
                            'team2Position' => 'U4',
                            'order' => 2,
                            'winnerNext' => 'U18',
                            'loserNext' => 'L20',
                        ], // 2
                        [
                            'team1Position' => 'U5',
                            'team2Position' => 'U6',
                            'order' => 3,
                            'winnerNext' => 'U19',
                            'loserNext' => 'L22',
                        ], // 3
                        [
                            'team1Position' => 'U7',
                            'team2Position' => 'U8',
                            'order' => 4,
                            'winnerNext' => 'U20',
                            'loserNext' => 'L24',
                        ], // 4
                        [
                            'team1Position' => 'U9',
                            'team2Position' => 'U10',
                            'order' => 5,
                            'winnerNext' => 'U21',
                            'loserNext' => 'L26',
                        ], // 5
                        [
                            'team1Position' => 'U11',
                            'team2Position' => 'U12',
                            'order' => 6,
                            'winnerNext' => 'U22',
                            'loserNext' => 'L28',
                        ], // 6
                        [
                            'team1Position' => 'U13',
                            'team2Position' => 'U14',
                            'order' => 7,
                            'winnerNext' => 'U23',
                            'loserNext' => 'L30',
                        ], // 7
                        [
                            'team1Position' => 'U15',
                            'team2Position' => 'U16',
                            'order' => 8,
                            'winnerNext' => 'U24',
                            'loserNext' => 'L32',
                        ], // 8
                    ],
                    'eliminator3' => [
                        [
                            'team1Position' => 'U17',
                            'team2Position' => 'U18',
                            'order' => 1,
                            'winnerNext' => 'U25',
                            'loserNext' => 'L42',
                        ], // 1
                        [
                            'team1Position' => 'U19',
                            'team2Position' => 'U20',
                            'order' => 2,
                            'winnerNext' => 'U26',
                            'loserNext' => 'L44',
                        ], // 2
                        [
                            'team1Position' => 'U21',
                            'team2Position' => 'U22',
                            'order' => 3,
                            'winnerNext' => 'U27',
                            'loserNext' => 'L46',
                        ], // 3
                        [
                            'team1Position' => 'U23',
                            'team2Position' => 'U24',
                            'order' => 4,
                            'winnerNext' => 'U28',
                            'loserNext' => 'L48',
                        ], // 4
                    ],
                    'eliminator4' => [
                        [
                            'team1Position' => 'U25',
                            'team2Position' => 'U26',
                            'order' => 1,
                            'winnerNext' => 'U29',
                            'loserNext' => 'L54',
                        ], // 1
                        [
                            'team1Position' => 'U27',
                            'team2Position' => 'U28',
                            'order' => 2,
                            'winnerNext' => 'U30',
                            'loserNext' => 'L56',
                        ], // 2
                    ],
                    'prefinals' => [
                        [
                            'team1Position' => 'U29',
                            'team2Position' => 'U30',
                            'order' => 1,
                            'winnerNext' => 'G1',
                            'loserNext' => 'L60',
                        ], // 1
                    ],
                ],
                'lowerBracket' => [
                    'eliminator1' => [
                        [
                            'team1Position' => 'L1',
                            'team2Position' => 'L2',
                            'order' => 1,
                            'winnerNext' => 'L17',
                            'loserNext' => null,
                        ], // 1
                        [
                            'team1Position' => 'L3',
                            'team2Position' => 'L4',
                            'order' => 2,
                            'winnerNext' => 'L19',
                            'loserNext' => null,
                        ], // 2
                        [
                            'team1Position' => 'L5',
                            'team2Position' => 'L6',
                            'order' => 3,
                            'winnerNext' => 'L21',
                            'loserNext' => null,
                        ], // 3
                        [
                            'team1Position' => 'L7',
                            'team2Position' => 'L8',
                            'order' => 4,
                            'winnerNext' => 'L23',
                            'loserNext' => null,
                        ], // 4
                        [
                            'team1Position' => 'L9',
                            'team2Position' => 'L10',
                            'order' => 5,
                            'winnerNext' => 'L25',
                            'loserNext' => null,
                        ], // 5
                        [
                            'team1Position' => 'L11',
                            'team2Position' => 'L12',
                            'order' => 6,
                            'winnerNext' => 'L27',
                            'loserNext' => null,
                        ], // 6
                        [
                            'team1Position' => 'L13',
                            'team2Position' => 'L14',
                            'order' => 7,
                            'winnerNext' => 'L29',
                            'loserNext' => null,
                        ], // 7
                        [
                            'team1Position' => 'L15',
                            'team2Position' => 'L16',
                            'order' => 8,
                            'winnerNext' => 'L31',
                            'loserNext' => null,
                        ], // 8
                    ],
                    'eliminator2' => [
                        [
                            'team1Position' => 'L17',
                            'team2Position' => 'L18',
                            'winnerNext' => 'L33',
                            'loserNext' => null,
                            'order' => 1,
                        ], // 1
                        [
                            'team1Position' => 'L19',
                            'team2Position' => 'L20',
                            'winnerNext' => 'L34',
                            'loserNext' => null,
                            'order' => 2
                        ], // 2
                        [
                            'team1Position' => 'L21',
                            'team2Position' => 'L22',
                            'winnerNext' => 'L35',
                            'loserNext' => null,
                            'order' => 3
                        ], // 3
                        [
                            'team1Position' => 'L23',
                            'team2Position' => 'L24',
                            'winnerNext' => 'L36',
                            'loserNext' => null,
                            'order' => 4
                        ], // 4
                        [
                            'team1Position' => 'L25',
                            'team2Position' => 'L26',
                            'winnerNext' => 'L37',
                            'loserNext' => null,
                            'order' => 5
                        ], // 5
                        [
                            'team1Position' => 'L27',
                            'team2Position' => 'L28',
                            'winnerNext' => 'L38',
                            'loserNext' => null,
                            'order' => 6
                        ], // 6
                        [
                            'team1Position' => 'L29',
                            'team2Position' => 'L30',
                            'winnerNext' => 'L39',
                            'loserNext' => null,
                            'order' => 7
                        ], // 7
                        [
                            'team1Position' => 'L31',
                            'team2Position' => 'L32',
                            'winnerNext' => 'L40',
                            'loserNext' => null,
                            'order' => 8
                        ], // 8
                    ],
                    'eliminator3' => [
                        [
                            'team1Position' => 'L33',
                            'team2Position' => 'L34',
                            'winnerNext' => 'L41',
                            'loserNext' => null,
                            'order' => 1,
                        ], // 1
                        [
                            'team1Position' => 'L35',
                            'team2Position' => 'L36',
                            'winnerNext' => 'L43',
                            'loserNext' => null,
                            'order' => 2
                        ], // 2
                        [
                            'team1Position' => 'L37',
                            'team2Position' => 'L38',
                            'winnerNext' => 'L45',
                            'loserNext' => null,
                            'order' => 3
                        ], // 3
                        [
                            'team1Position' => 'L39',
                            'team2Position' => 'L40',
                            'winnerNext' => 'L47',
                            'loserNext' => null,
                            'order' => 4
                        ], // 4
                    ],
                    'eliminator4' => [
                        [
                            'team1Position' => 'L41',
                            'team2Position' => 'L42',
                            'winnerNext' => 'L49',
                            'loserNext' => null,
                            'order' => 1,
                        ], // 1
                        [
                            'team1Position' => 'L43',
                            'team2Position' => 'L44',
                            'winnerNext' => 'L50',
                            'loserNext' => null,
                            'order' => 2,
                        ], // 2
                        [
                            'team1Position' => 'L45',
                            'team2Position' => 'L46',
                            'winnerNext' => 'L51',
                            'loserNext' => null,
                            'order' => 3
                        ], // 3
                        [
                            'team1Position' => 'L47',
                            'team2Position' => 'L48',
                            'winnerNext' => 'L52',
                            'loserNext' => null,
                            'order' => 4
                        ], // 4
                    ],
                    'eliminator5' => [
                        [
                            'team1Position' => 'L49',
                            'team2Position' => 'L50',
                            'winnerNext' => 'L53',
                            'loserNext' => null,

                        ], // 1
                        [
                            'team1Position' => 'L51',
                            'team2Position' => 'L52',
                            'winnerNext' => 'L55',
                            'loserNext' => null,
                            'order' => 2,
                        ], // 2
                    ],
                    'eliminator6' => [
                        [
                            'team1Position' => 'L53',
                            'team2Position' => 'L54',
                            'winnerNext' => 'L57',
                            'loserNext' => null,
                            'order' => 1,
                        ], // 1
                        [
                            'team1Position' => 'L55',
                            'team2Position' => 'L56',
                            'winnerNext' => 'L58',
                            'loserNext' => null,
                            'order' => 2,
                        ], // 2
                    ],
                    'prefinals1' => [
                        [
                            'team1Position' => 'L57',
                            'team2Position' => 'L58',
                            'winnerNext' => 'L59',
                            'loserNext' => null,
                            'order' => 1,
                        ], // 1
                    ],
                    'prefinals2' => [
                        [
                            'team1Position' => 'L59',
                            'team2Position' => 'L60',
                            'winnerNext' => 'G2',
                            'loserNext' => null,
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
                        'team1Position' => 'G1',
                        'team2Position' => 'G2',
                        'order' => 1,
                        'winnerNext' => null,
                        'loserNext' => null,
                    ], 
                ],
                'upperBracket' => [
                    'eliminator1' => [
                        [
                            'team1Position' => '',
                            'team2Position' => '',
                            'order' => 1,
                            'winnerNext' => 'U1',
                            'loserNext' => 'L1',
                        ], // 1
                        [
                            'team1Position' => '',
                            'team2Position' => '',
                            'order' => 2,
                            'winnerNext' => 'U2',
                            'loserNext' => 'L2',
                        ], // 2
                        [
                            'team1Position' => '',
                            'team2Position' => '',
                            'order' => 3,
                            'winnerNext' => 'U3',
                            'loserNext' => 'L3',
                        ], // 3
                        [
                            'team1Position' => '',
                            'team2Position' => '',
                            'order' => 4,
                            'winnerNext' => 'U4',
                            'loserNext' => 'L4',
                        ], // 4
                        [
                            'team1Position' => '',
                            'team2Position' => '',
                            'order' => 5,
                            'winnerNext' => 'U5',
                            'loserNext' => 'L6',
                        ], // 5
                        [
                            'team1Position' => '',
                            'team2Position' => '',
                            'order' => 6,
                            'winnerNext' => 'U6',
                            'loserNext' => 'L6',
                        ], // 6
                        [
                            'team1Position' => '',
                            'team2Position' => '',
                            'order' => 7,
                            'winnerNext' => 'U7',
                            'loserNext' => 'L7',
                        ], // 7
                        [
                            'team1Position' => '',
                            'team2Position' => '',
                            'order' => 8,
                            'winnerNext' => 'U8',
                            'loserNext' => 'L8',
                        ], // 8
                    ],
                    'eliminator2' => [
                        [
                            'team1Position' => 'U1',
                            'team2Position' => 'U2',
                            'order' => 1,
                            'winnerNext' => 'U9',
                            'loserNext' => 'L10',
                        ], // 1
                        [
                            'team1Position' => 'U3',
                            'team2Position' => 'U4',
                            'order' => 2,
                            'winnerNext' => 'U10',
                            'loserNext' => 'L12',
                        ], // 2
                        [
                            'team1Position' => 'U5',
                            'team2Position' => 'U6',
                            'order' => 3,
                            'winnerNext' => 'U11',
                            'loserNext' => 'L14',
                        ], // 3
                        [
                            'team1Position' => 'U7',
                            'team2Position' => 'U8',
                            'order' => 4,
                            'winnerNext' => 'U12',
                            'loserNext' => 'L16',
                        ], // 4
                    ],
                    'eliminator3' => [
                        [
                            'team1Position' => 'U9',
                            'team2Position' => 'U10',
                            'order' => 1,
                            'winnerNext' => 'U13',
                            'loserNext' => 'L22',
                        ], // 1
                        [
                            'team1Position' => 'U11',
                            'team2Position' => 'U12',
                            'order' => 2,
                            'winnerNext' => 'U14',
                            'loserNext' => 'L24',
                        ], // 2
                    ],
                    'prefinals' => [
                        [
                            'team1Position' => 'U13',
                            'team2Position' => 'U14',
                            'order' => 1,
                            'winnerNext' => 'G1',
                            'loserNext' => 'L28',
                        ], // 1
                    ],
                ],
                'lowerBracket' => [
                    'eliminator1' => [
                        [
                            'team1Position' => 'L1',
                            'team2Position' => 'L2',
                            'order' => 1,
                            'winnerNext' => 'L9',
                            'loserNext' => null,
                        ], // 1
                        [
                            'team1Position' => 'L3',
                            'team2Position' => 'L4',
                            'order' => 2,
                            'winnerNext' => 'L10',
                            'loserNext' => null,
                        ], // 2
                        [
                            'team1Position' => 'L5',
                            'team2Position' => 'L6',
                            'order' => 3,
                            'winnerNext' => 'L11',
                            'loserNext' => null,
                        ], // 3
                        [
                            'team1Position' => 'L7',
                            'team2Position' => 'L8',
                            'order' => 4,
                            'winnerNext' => 'L12',
                            'loserNext' => null,
                        ], // 4
                    ],
                    'eliminator2' => [
                        [
                            'team1Position' => 'L9',
                            'team2Position' => 'L10',
                            'winnerNext' => 'L17',
                            'loserNext' => null,
                            'order' => 1,
                        ], // 1
                        [
                            'team1Position' => 'L11',
                            'team2Position' => 'L12',
                            'winnerNext' => 'L18',
                            'loserNext' => null,
                            'order' => 2
                        ], // 2
                        [
                            'team1Position' => 'L13',
                            'team2Position' => 'L14',
                            'winnerNext' => 'L19',
                            'loserNext' => null,
                            'order' => 3
                        ], // 3
                        [
                            'team1Position' => 'L15',
                            'team2Position' => 'L16',
                            'winnerNext' => 'L20',
                            'loserNext' => null,
                            'order' => 4
                        ], // 4
                    ],
                    'eliminator3' => [
                        [
                            'team1Position' => 'L17',
                            'team2Position' => 'L18',
                            'winnerNext' => 'L21',
                            'loserNext' => null,
                            'order' => 1,
                        ], // 1
                        [
                            'team1Position' => 'L19',
                            'team2Position' => 'L20',
                            'winnerNext' => 'L23',
                            'loserNext' => null,
                            'order' => 2
                        ], // 2
                    ],
                    'eliminator4' => [
                        [
                            'team1Position' => 'L21',
                            'team2Position' => 'L22',
                            'winnerNext' => 'L25',
                            'loserNext' => null,
                            'order' => 1,
                        ], // 1
                        [
                            'team1Position' => 'L23',
                            'team2Position' => 'L24',
                            'winnerNext' => 'L26',
                            'loserNext' => null,
                            'order' => 2,
                        ], // 2
                    ],
                    'prefinals1' => [
                        [
                            'team1Position' => 'L25',
                            'team2Position' => 'L26',
                            'winnerNext' => 'L53',
                            'loserNext' => null,
                            'order' => 1,
                        ], // 1
                    ],
                    'prefinals2' => [
                        [
                            'team1Position' => 'L27',
                            'team2Position' => 'L28',
                            'winnerNext' => 'L27',
                            'loserNext' => null,
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
                        'team1Position' => 'G1',
                        'team2Position' => 'G2',
                        'order' => 1,
                        'winnerNext' => null,
                        'loserNext' => null,
                    ], 
                ],
                'upperBracket' => [
                    'eliminator1' => [
                        [
                            'team1Position' => '',
                            'team2Position' => '',
                            'order' => 1,
                            'winnerNext' => 'U1',
                            'loserNext' => 'L1',
                        ], // 1
                        [
                            'team1Position' => '',
                            'team2Position' => '',
                            'order' => 2,
                            'winnerNext' => 'U2',
                            'loserNext' => 'L2',
                        ], // 2
                        [
                            'team1Position' => '',
                            'team2Position' => '',
                            'order' => 3,
                            'winnerNext' => 'U3',
                            'loserNext' => 'L3',
                        ], // 3
                        [
                            'team1Position' => '',
                            'team2Position' => '',
                            'order' => 4,
                            'winnerNext' => 'U4',
                            'loserNext' => 'L4',
                        ], // 4
                    ],
                    'eliminator2' => [
                        [
                            'team1Position' => 'U1',
                            'team2Position' => 'U2',
                            'order' => 1,
                            'winnerNext' => 'U5',
                            'loserNext' => 'L6',
                        ], // 1
                        [
                            'team1Position' => 'U3',
                            'team2Position' => 'U4',
                            'order' => 2,
                            'winnerNext' => 'U6',
                            'loserNext' => 'L8',
                        ], // 2
                    ],
                    'prefinals' => [
                        [
                            'team1Position' => 'U5',
                            'team2Position' => 'U6',
                            'order' => 1,
                            'winnerNext' => 'G1',
                            'loserNext' => 'L12',
                        ], // 1
                    ],
                ],
                'lowerBracket' => [
                    'eliminator1' => [
                        [
                            'team1Position' => 'L1',
                            'team2Position' => 'L2',
                            'order' => 1,
                            'winnerNext' => 'L5',
                            'loserNext' => null,
                        ], // 1
                        [
                            'team1Position' => 'L3',
                            'team2Position' => 'L4',
                            'order' => 2,
                            'winnerNext' => 'L7',
                            'loserNext' => null,
                        ], // 2
                    ],
                    'eliminator2' => [
                        [
                            'team1Position' => 'L5',
                            'team2Position' => 'L6',
                            'winnerNext' => 'L9',
                            'order' => 1,
                            'loserNext' => null,
                        ], // 1
                        [
                            'team1Position' => 'L7',
                            'team2Position' => 'L8',
                            'winnerNext' => 'L10',
                            'order' => 2,
                            'loserNext' => null,
                        ], // 2
                    ],
                    'prefinals1' => [
                        [
                            'team1Position' => 'L9',
                            'team2Position' => 'L10',
                            'winnerNext' => 'L11',
                            'loserNext' => null,
                            'order' => 1
                        ], // 1
                    ],
                    'prefinals2' => [
                        [
                            'team1Position' => 'L11',
                            'team2Position' => 'L12',
                            'winnerNext' => 'G2',
                            'loserNext' => null,
                            'order' => 1,
                        ], // 1
                    ],
                ],
            ],
        ];
    }
}
}