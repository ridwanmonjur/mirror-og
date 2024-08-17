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
@endphp

<body>
    @include('__CommonPartials.NavbarGoToSearchPage')

    <div class="ps-4">
        <h1>Responsive Tournament Bracket</h1>
        <h2>Ice hockey at the 1998 Winter Olympics – Men's tournament</h2>
        <div class="tournament-bracket tournament-bracket--rounded">
            <div class="tournament-bracket__round tournament-bracket__round--quarterfinals">
                <h3 class="tournament-bracket__round-title">Quarterfinals</h3>
                <ul class="tournament-bracket__list">
                    <li class="tournament-bracket__item">
                        <x-bracket-item team1="Canada" team1Code="CAN" team1Score="4" team2="Kazakhstan" team2Code="KAZ"
                            team2Score="1" date="1998-02-18" />
                    </li>
                    <li class="tournament-bracket__item">
                        <x-bracket-item team1="Czech Republic" team1Code="CZE" team1Score="4"
                            team2="United States of America" team2Code="USA" team2Score="1" date="1998-02-18" />
                    </li>
                    <li class="tournament-bracket__item">
                        <x-bracket-item team1="Finland" team1Code="FIN" team1Score="2" team2="Sweden" team2Code="SVE"
                            team2Score="1" date="1998-02-18" />
                    </li>
                    <li class="tournament-bracket__item">
                        <x-bracket-item team1="Russia" team1Code="RUS" team1Score="4" team2="Belarus" team2Code="BEL"
                            team2Score="1" date="1998-02-18" />
                    </li>
                </ul>
            </div>
            <div class="tournament-bracket__round tournament-bracket__round--semifinals">
                <h3 class="tournament-bracket__round-title">Semifinals</h3>
                <ul class="tournament-bracket__list">
                    <li class="tournament-bracket__item">
                        <x-bracket-item team1="Canada" team1Code="CAN" team1Score="1" team2="Czech Republic"
                            team2Code="CZE" team2Score="2" date="1998-02-20" />
                    </li>

                    <li class="tournament-bracket__item">
                        <x-bracket-item team1="Finland" team1Code="FIN" team1Score="4" team2="Russia" team2Code="RUS"
                            team2Score="7" date="1998-02-20" />
                    </li>
                </ul>
            </div>
            <div class="tournament-bracket__round tournament-bracket__round--bronze">
                <h3 class="tournament-bracket__round-title">Bronze medal game</h3>
                <ul class="tournament-bracket__list">
                    <li class="tournament-bracket__item">
                        <x-bracket-item team1="Finland" team1Code="FIN" team1Score="3" team2="Canada" team2Code="CAN"
                            team2Score="2" date="1998-02-21" medal="bronze" />
                    </li>
                </ul>
            </div>
            <div class="tournament-bracket__round tournament-bracket__round--gold">
                <h3 class="tournament-bracket__round-title">Gold medal game</h3>
                <ul class="tournament-bracket__list">
                    <li class="tournament-bracket__item">
                        <x-bracket-item team1="Finland" team1Code="FIN" team1Score="3" team2="Canada" team2Code="CAN"
                            team2Score="2" date="1998-02-21" medal="bronze" />
                    </li>
                </ul>
            </div>
        </div>
        </main>
        <script src="{{ asset('/assets/js/jsUtils.js') }}"></script>
</body>
