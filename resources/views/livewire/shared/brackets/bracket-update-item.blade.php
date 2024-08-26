@php
    if (isset($bracket['team1Code'])) {
        $bracket['team1Display'] = true;
    } else {
        $bracket['team1Display'] = false;
        $bracket['team1Code'] = "N/A";
    }

    if (isset($bracket['team2Code'])) {
        $bracket['team2Display'] = true;
    } else {
        $bracket['team2Display'] = false;
        $bracket['team2Code'] = "N/A";
    }

    $bracket['team1Score'] = $bracket['team1Score'] ?? 'N/A';
    $bracket['team2Score'] = $bracket['team2Score'] ?? 'N/A';
    $bracket['winnerNext'] = $bracket['winnerNext'] ?? 'N/A';
    $bracket['loserNext'] = $bracket['loserNext'] ?? null;
    $bracket['team1PositionMobile'] = $bracket['team1Position'];
    $bracket['team2PositionMobile'] = $bracket['team2Position'];

    if (!$bracket['team1Position']) {
        $bracket['team1Position'] = "";
        $bracket['team1PositionMobile'] = "TBD";
    }

    if (!$bracket['team2Position']) {
        $bracket['team2Position'] = "";
        $bracket['team2PositionMobile'] = "TBD";
    }

@endphp

<li class="tournament-bracket__item code{{ $bracket['team1Position'] }}code code{{ $bracket['team1Position'] }}code">
    <div class="tournament-bracket__match code{{ $bracket['team1Position'] }}code code{{ $bracket['team1Position'] }}code" tabindex="0">
        <table class="tournament-bracket__table mx-auto">
            <thead class="sr-only">
                <tr>
                    <th>Country</th>
                    <th>Score</th>
                </tr>
            </thead>
            <tbody class="tournament-bracket__content">
                <tr class="tournament-bracket__team tournament-bracket__team--winner">
                    <td class="tournament-bracket__country">
                        <abbr class="tournament-bracket__code" title="{{ $bracket['team1PositionMobile'] }}">{{ $bracket['team1PositionMobile'] }}</abbr>
                    </td>
                    <td class="tournament-bracket__score">
                        <span class="tournament-bracket__number">{{ $bracket['team1Score'] }}</span>
                    </td>
                </tr>
                <tr class="tournament-bracket__team">
                    <td class="tournament-bracket__country">
                        <abbr class="tournament-bracket__code" title="{{ $bracket['team2PositionMobile'] }}">{{ $bracket['team2PositionMobile'] }}</abbr>
                    </td>
                    <td class="tournament-bracket__score">
                        <span class="tournament-bracket__number">{{ $bracket['team2Score'] }}</span>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="text-center mx-auto tournament-bracket__displayLargeScreen position-relative">
            <div class="tournament-bracket__box code{{ $bracket['team1Code'] }}code">
                <span >{{ $bracket['team1Position'] }}</span>
            </div>
            <div class="tournament-bracket__box code{{ $bracket['team2Code'] }}code">
                <span >{{ $bracket['team2Position'] }}</span>
            </div>

            <small class="position-absolute winner-label">Winner to {{ $bracket['winnerNext'] }}</small>
            @if ($bracket['loserNext'])
                <small class="position-absolute loser-label">Loser to {{ $bracket['loserNext'] }}</small>
            @endif
        </div>
    </div>
</li>
