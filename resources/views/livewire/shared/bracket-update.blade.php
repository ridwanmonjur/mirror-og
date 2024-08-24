
@php
    use Carbon\Carbon;
    $matchesUpperCount = intval($event->tier->tierTeamSlot); // 8, 16, 32

    $matchesUpper32 = [
        ['team' => 'Canada', 'code' => 'CAN'],
        ['team' => 'Kazakhstan', 'code' => 'KAZ'],
        ['team' => 'Czech Republic', 'code' => 'CZE'],
        ['team' => 'United States of America', 'code' => 'USA'],
        ['team' => 'Finland', 'code' => 'FIN'],
        ['team' => 'Sweden', 'code' => 'SWE'],
        ['team' => 'Russia', 'code' => 'RUS'],
        ['team' => 'Belarus', 'code' => 'BLR'],
        ['team' => 'Germany', 'code' => 'GER'],
        ['team' => 'Norway', 'code' => 'NOR'],
        ['team' => 'France', 'code' => 'FRA'],
        ['team' => 'Italy', 'code' => 'ITA'],
        ['team' => 'Switzerland', 'code' => 'SUI'],
        ['team' => 'Austria', 'code' => 'AUT'],
        ['team' => 'Denmark', 'code' => 'DEN'],
        ['team' => 'Slovakia', 'code' => 'SVK'],
        ['team' => 'Poland', 'code' => 'POL'],
        ['team' => 'Latvia', 'code' => 'LAT'],
        ['team' => 'Ukraine', 'code' => 'UKR'],
        ['team' => 'Lithuania', 'code' => 'LTU'],
        ['team' => 'Hungary', 'code' => 'HUN'],
        ['team' => 'Romania', 'code' => 'ROU'],
        ['team' => 'Slovenia', 'code' => 'SVN'],
        ['team' => 'Belgium', 'code' => 'BEL'],
        ['team' => 'Netherlands', 'code' => 'NED'],
        ['team' => 'Spain', 'code' => 'ESP'],
        ['team' => 'Portugal', 'code' => 'POR'],
        ['team' => 'Turkey', 'code' => 'TUR'],
        ['team' => 'Greece', 'code' => 'GRE'],
        ['team' => 'Croatia', 'code' => 'CRO'],
        ['team' => 'Serbia', 'code' => 'SRB'],
        ['team' => 'Bulgaria', 'code' => 'BUL']
    ];

    $quarterfinalsUpper = [];
    for ($i = 0; $i < $matchesUpperCount; $i += 2) {
        $quarterfinalsUpper[] = [
            'team1' => $matchesUpper32[$i]['team'],
            'team1Code' => $matchesUpper32[$i]['code'],
            'team1Score' => rand(1, 5),
            'team2' => $matchesUpper32[$i+1]['team'],
            'team2Code' => $matchesUpper32[$i+1]['code'],
            'team2Score' => rand(1, 5),
            'date' => '1998-02-18',
        ];
    }

    $semifinalsUpper = [];
    for ($i = 0; $i < $matchesUpperCount/2; $i += 2) {
        $semifinalsUpper[] = [
            'team1' => $quarterfinalsUpper[$i]['team1'],
            'team1Code' => $quarterfinalsUpper[$i]['team1Code'],
            'team1Score' => rand(1, 5),
            'team2' => $quarterfinalsUpper[$i+1]['team1'],
            'team2Code' => $quarterfinalsUpper[$i+1]['team1Code'],
            'team2Score' => rand(1, 5),
            'date' => '1998-02-20',
        ];
    }

    $semifinalsUpper2 = [];
    if ($matchesUpperCount/4 > 2 ) {
        for ($i = 0; $i < $matchesUpperCount/4; $i += 2) {
            $semifinalsUpper2[] = [
                'team1' => $quarterfinalsUpper[$i]['team1'],
                'team1Code' => $quarterfinalsUpper[$i]['team1Code'],
                'team1Score' => rand(1, 5),
                'team2' => $quarterfinalsUpper[$i+1]['team1'],
                'team2Code' => $quarterfinalsUpper[$i+1]['team1Code'],
                'team2Score' => rand(1, 5),
                'date' => '1998-02-20',
            ];
        }
    }


    $semifinalsUpper3 = [];
    if ($matchesUpperCount/8 > 2) {
        for ($i = 0; $i < $matchesUpperCount/8; $i += 2) {
            $semifinalsUpper3[] = [
                'team1' => $quarterfinalsUpper[$i]['team1'],
                'team1Code' => $quarterfinalsUpper[$i]['team1Code'],
                'team1Score' => rand(1, 5),
                'team2' => $quarterfinalsUpper[$i+1]['team1'],
                'team2Code' => $quarterfinalsUpper[$i+1]['team1Code'],
                'team2Score' => rand(1, 5),
                'date' => '1998-02-20',
            ];
        }
    }

    $gold = [
        [
            'team1' => $semifinalsUpper[0]['team1'],
            'team1Code' => $semifinalsUpper[0]['team1Code'],
            'team1Score' => rand(1, 5),
            'team2' => $semifinalsUpper[0]['team2'],
            'team2Code' => $semifinalsUpper[0]['team2Code'],
            'team2Score' => rand(1, 5),
            'date' => '1998-02-22',
            'medal' => 'gold'
        ]
    ];


    $matchesUpper = [
        'quarterfinalsUpper' => $quarterfinalsUpper,
        'semifinalsUpper' => $semifinalsUpper,
        'semifinalsUpper2' => $semifinalsUpper2,
        'semifinalsUpper3' => $semifinalsUpper3,
        'gold' => $gold,
    ];
@endphp

<body>
    @include('__CommonPartials.NavbarGoToSearchPage')
    <div class="px-4">
        <h5 class="mt-5 mb-4  text-start">Upper bracket</h5>
        <div class="tournament-bracket tournament-bracket--rounded">
            <div class="tournament-bracket__round tournament-bracket__round--quarterfinalsUpper">
                <h3 class="tournament-bracket__round-title">Quarterfinals</h3>
                <ul class="tournament-bracket__list">
                    @foreach ($matchesUpper['quarterfinalsUpper'] as $match)
                            <x-bracket-item :team1="$match['team1']" :team1Code="$match['team1Code']" :team1Score="$match['team1Score']" :team2="$match['team2']"
                                :team2Code="$match['team2Code']" :team2Score="$match['team2Score']" :date="$match['date']" />
                    @endforeach
                </ul>
            </div>

            <div class="tournament-bracket__round tournament-bracket__round--semifinalsUpper">
                <h3 class="tournament-bracket__round-title">Semifinals 1</h3>
                <ul class="tournament-bracket__list">
                    @foreach ($matchesUpper['semifinalsUpper'] as $match)
                            <x-bracket-item :team1="$match['team1']" :team1Code="$match['team1Code']" :team1Score="$match['team1Score']" :team2="$match['team2']"
                                :team2Code="$match['team2Code']" :team2Score="$match['team2Score']" :date="$match['date']" />
                    @endforeach
                </ul>
            </div>
            @if (isset($matchesUpper['semifinalsUpper2'][0]))
                <div class="tournament-bracket__round tournament-bracket__round--semifinalsUpper">
                    <h3 class="tournament-bracket__round-title">Semifinals 2</h3>
                    <ul class="tournament-bracket__list">
                        @foreach ($matchesUpper['semifinalsUpper2'] as $match)
                                <x-bracket-item :team1="$match['team1']" :team1Code="$match['team1Code']" :team1Score="$match['team1Score']" :team2="$match['team2']"
                                    :team2Code="$match['team2Code']" :team2Score="$match['team2Score']" :date="$match['date']" />
                        @endforeach
                    </ul>
                </div>
            @endif
             @if (isset($matchesUpper['semifinalsUpper3'][0]))
                <div class="tournament-bracket__round tournament-bracket__round--semifinalsUpper">
                    <h3 class="tournament-bracket__round-title">Semifinals  3</h3>
                    <ul class="tournament-bracket__list">
                        @foreach ($matchesUpper['semifinalsUpper3'] as $match)
                                <x-bracket-item :team1="$match['team1']" :team1Code="$match['team1Code']" :team1Score="$match['team1Score']" :team2="$match['team2']"
                                    :team2Code="$match['team2Code']" :team2Score="$match['team2Score']" :date="$match['date']" />
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="tournament-bracket__round tournament-bracket__round--gold">
                <h3 class="tournament-bracket__round-title">Gold medal game</h3>
                <ul class="tournament-bracket__list">
                    @foreach ($matchesUpper['gold'] as $match)
                            <x-bracket-item :team1="$match['team1']" :team1Code="$match['team1Code']" :team1Score="$match['team1Score']" :team2="$match['team2']"
                                :team2Code="$match['team2Code']" :team2Score="$match['team2Score']" :date="$match['date']" :medal="$match['medal']" />
                    @endforeach
                </ul>
            </div>

            <div class="tournament-bracket__round tournament-bracket__round--gold">
                
            </div> 
        </div>
        <h5 class="mt-5 mb-4 text-start">Lower bracket</h5>
        <div class="tournament-bracket tournament-bracket--rounded">
            <div class="tournament-bracket__round tournament-bracket__round--quarterfinalsUpper">
                <h3 class="tournament-bracket__round-title">Quarterfinals</h3>
                <ul class="tournament-bracket__list">
                    @foreach ($matchesUpper['quarterfinalsUpper'] as $match)
                            <x-bracket-item :team1="$match['team1']" :team1Code="$match['team1Code']" :team1Score="$match['team1Score']" :team2="$match['team2']"
                                :team2Code="$match['team2Code']" :team2Score="$match['team2Score']" :date="$match['date']" />
                    @endforeach
                </ul>
            </div>

            <div class="tournament-bracket__round tournament-bracket__round--semifinalsUpper">
                <h3 class="tournament-bracket__round-title">Semifinals 1</h3>
                <ul class="tournament-bracket__list">
                    @foreach ($matchesUpper['semifinalsUpper'] as $match)
                            <x-bracket-item :team1="$match['team1']" :team1Code="$match['team1Code']" :team1Score="$match['team1Score']" :team2="$match['team2']"
                                :team2Code="$match['team2Code']" :team2Score="$match['team2Score']" :date="$match['date']" />
                    @endforeach
                </ul>
            </div>
            @if (isset($matchesUpper['semifinalsUpper2'][0]))
                <div class="tournament-bracket__round tournament-bracket__round--semifinalsUpper">
                    <h3 class="tournament-bracket__round-title">Semifinals 2</h3>
                    <ul class="tournament-bracket__list">
                        @foreach ($matchesUpper['semifinalsUpper2'] as $match)
                                <x-bracket-item :team1="$match['team1']" :team1Code="$match['team1Code']" :team1Score="$match['team1Score']" :team2="$match['team2']"
                                    :team2Code="$match['team2Code']" :team2Score="$match['team2Score']" :date="$match['date']" />
                        @endforeach
                    </ul>
                </div>
            @endif
             @if (isset($matchesUpper['semifinalsUpper3'][0]))
                <div class="tournament-bracket__round tournament-bracket__round--semifinalsUpper">
                    <h3 class="tournament-bracket__round-title">Semifinals  3</h3>
                    <ul class="tournament-bracket__list">
                        @foreach ($matchesUpper['semifinalsUpper3'] as $match)
                                <x-bracket-item :team1="$match['team1']" :team1Code="$match['team1Code']" :team1Score="$match['team1Score']" :team2="$match['team2']"
                                    :team2Code="$match['team2Code']" :team2Score="$match['team2Score']" :date="$match['date']" />
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="tournament-bracket__round tournament-bracket__round--gold">
                <h3 class="tournament-bracket__round-title">Gold medal game</h3>
                <ul class="tournament-bracket__list">
                    @foreach ($matchesUpper['gold'] as $match)
                            <x-bracket-item :team1="$match['team1']" :team1Code="$match['team1Code']" :team1Score="$match['team1Score']" :team2="$match['team2']"
                                :team2Code="$match['team2Code']" :team2Score="$match['team2Score']" :date="$match['date']" :medal="$match['medal']" />
                    @endforeach
                </ul>
            </div>

            <div class="tournament-bracket__round tournament-bracket__round--gold">
                
            </div> 
        </div>

        </main>
    <script>
        var bracketItemList = document.querySelectorAll('.codeCANcode.tournament-bracket__item');
        bracketItemList.forEach(item => {
            item.classList.add('special-item-right'); // Use 'item' instead of 'bracketItem'
        });

        var bracketMatchList = document.querySelectorAll('.codeCANcode.tournament-bracket__match');
        bracketItemList.forEach(item => {
            console.log({hi: true});
            item.classList.add('special-item2');
            item.style.setProperty('--border-color', 'red');   

        });



    </script>        
    <script src="{{ asset('/assets/js/jsUtils.js') }}"></script>
</body>