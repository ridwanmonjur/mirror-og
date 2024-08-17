<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tournamnet Demo</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/common/tournament.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @include('__CommonPartials.HeadIcon')
    <style>

    </style>

</head>
@php
    use Carbon\Carbon;
    $matchesCount = 16; // 8, 16, 32

    $matches32 = [
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

    $quarterfinals = [];
    for ($i = 0; $i < $matchesCount; $i += 2) {
        $quarterfinals[] = [
            'team1' => $matches32[$i]['team'],
            'team1Code' => $matches32[$i]['code'],
            'team1Score' => rand(1, 5),
            'team2' => $matches32[$i+1]['team'],
            'team2Code' => $matches32[$i+1]['code'],
            'team2Score' => rand(1, 5),
            'date' => '1998-02-18',
        ];
    }

    $semifinals = [];
    for ($i = 0; $i < $matchesCount/2; $i += 2) {
        $semifinals[] = [
            'team1' => $quarterfinals[$i]['team1'],
            'team1Code' => $quarterfinals[$i]['team1Code'],
            'team1Score' => rand(1, 5),
            'team2' => $quarterfinals[$i+1]['team1'],
            'team2Code' => $quarterfinals[$i+1]['team1Code'],
            'team2Score' => rand(1, 5),
            'date' => '1998-02-20',
        ];
    }

    $semifinals2 = [];
    if ($matchesCount/4 > 2 ) {
        for ($i = 0; $i < $matchesCount/4; $i += 2) {
            $semifinals2[] = [
                'team1' => $quarterfinals[$i]['team1'],
                'team1Code' => $quarterfinals[$i]['team1Code'],
                'team1Score' => rand(1, 5),
                'team2' => $quarterfinals[$i+1]['team1'],
                'team2Code' => $quarterfinals[$i+1]['team1Code'],
                'team2Score' => rand(1, 5),
                'date' => '1998-02-20',
            ];
        }
    }


    $semifinals3 = [];
    if ($matchesCount/8 > 2) {
        for ($i = 0; $i < $matchesCount/8; $i += 2) {
            $semifinals3[] = [
                'team1' => $quarterfinals[$i]['team1'],
                'team1Code' => $quarterfinals[$i]['team1Code'],
                'team1Score' => rand(1, 5),
                'team2' => $quarterfinals[$i+1]['team1'],
                'team2Code' => $quarterfinals[$i+1]['team1Code'],
                'team2Score' => rand(1, 5),
                'date' => '1998-02-20',
            ];
        }
    }

    $gold = [
        [
            'team1' => $semifinals[0]['team1'],
            'team1Code' => $semifinals[0]['team1Code'],
            'team1Score' => rand(1, 5),
            'team2' => $semifinals[0]['team2'],
            'team2Code' => $semifinals[0]['team2Code'],
            'team2Score' => rand(1, 5),
            'date' => '1998-02-22',
            'medal' => 'gold'
        ]
    ];


    $matches = [
        'quarterfinals' => $quarterfinals,
        'semifinals' => $semifinals,
        'semifinals2' => $semifinals2,
        'semifinals3' => $semifinals3,
        'gold' => $gold,
    ];
@endphp

<body>
    @include('__CommonPartials.NavbarGoToSearchPage')
    @include('Shared.data.data')
    <div class="px-4">
        <h1>Responsive Tournament Bracket</h1>
        <h2>Ice hockey at the 1998 Winter Olympics – Men's tournament</h2>
        <div class="tournament-bracket tournament-bracket--rounded">
            <div class="tournament-bracket__round tournament-bracket__round--quarterfinals">
                <h3 class="tournament-bracket__round-title">Quarterfinals</h3>
                <ul class="tournament-bracket__list">
                    @foreach ($matches['quarterfinals'] as $match)
                            <x-bracket-item :team1="$match['team1']" :team1Code="$match['team1Code']" :team1Score="$match['team1Score']" :team2="$match['team2']"
                                :team2Code="$match['team2Code']" :team2Score="$match['team2Score']" :date="$match['date']" />
                    @endforeach
                </ul>
            </div>

            <div class="tournament-bracket__round tournament-bracket__round--semifinals">
                <h3 class="tournament-bracket__round-title">Semifinals 1</h3>
                <ul class="tournament-bracket__list">
                    @foreach ($matches['semifinals'] as $match)
                            <x-bracket-item :team1="$match['team1']" :team1Code="$match['team1Code']" :team1Score="$match['team1Score']" :team2="$match['team2']"
                                :team2Code="$match['team2Code']" :team2Score="$match['team2Score']" :date="$match['date']" />
                    @endforeach
                </ul>
            </div>
            @if (isset($matches['semifinals2'][0]))
                <div class="tournament-bracket__round tournament-bracket__round--semifinals">
                    <h3 class="tournament-bracket__round-title">Semifinals 2</h3>
                    <ul class="tournament-bracket__list">
                        @foreach ($matches['semifinals2'] as $match)
                                <x-bracket-item :team1="$match['team1']" :team1Code="$match['team1Code']" :team1Score="$match['team1Score']" :team2="$match['team2']"
                                    :team2Code="$match['team2Code']" :team2Score="$match['team2Score']" :date="$match['date']" />
                        @endforeach
                    </ul>
                </div>
            @endif
             @if (isset($matches['semifinals3'][0]))
                <div class="tournament-bracket__round tournament-bracket__round--semifinals">
                    <h3 class="tournament-bracket__round-title">Semifinals  3</h3>
                    <ul class="tournament-bracket__list">
                        @foreach ($matches['semifinals3'] as $match)
                                <x-bracket-item :team1="$match['team1']" :team1Code="$match['team1Code']" :team1Score="$match['team1Score']" :team2="$match['team2']"
                                    :team2Code="$match['team2Code']" :team2Score="$match['team2Score']" :date="$match['date']" />
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="tournament-bracket__round tournament-bracket__round--gold">
                <h3 class="tournament-bracket__round-title">Gold medal game</h3>
                <ul class="tournament-bracket__list">
                    @foreach ($matches['gold'] as $match)
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
