<div class="tournament-bracket tournament-bracket--rounded col-lg-3 col-xl-4 col-xxl-6 align-items-start">
    <div class="tournament-bracket__round  tournament-bracket__round--gold">
        <h3 class="tournament-bracket__round-title">Finals</h3>
        <br class="d-none d-lg-block"><br class="d-none d-lg-block"><br>
        <div class="tournament-bracket__list tournament-bracket__joined-list tournament-bracket__joined-odd-list">
            <div
                class="tournament-bracket__item code{{ $bracket['team1Position'] }}code code{{ $bracket['team1Position'] }}code">
                @php
                    if (isset($bracket['team1Code'])) {
                        $bracket['team1Display'] = true;
                    } else {
                        $bracket['team1Display'] = false;
                        $bracket['team1Code'] = 'N/A';
                    }

                    if (isset($bracket['team2Code'])) {
                        $bracket['team2Display'] = true;
                    } else {
                        $bracket['team2Display'] = false;
                        $bracket['team2Code'] = 'N/A';
                    }

                    $bracket['team1Score'] = $bracket['team1Score'] ?? 'N/A';
                    $bracket['team2Score'] = $bracket['team2Score'] ?? 'N/A';
                    $bracket['winnerNext'] = $bracket['winnerNext'] ?? 'N/A';
                    $bracket['loserNext'] = $bracket['loserNext'] ?? null;

                    if (!$bracket['team1Position']) {
                        $bracket['team1Position'] = '';
                    }

                    if (!$bracket['team2Position']) {
                        $bracket['team2Position'] = '';
                    }
                @endphp

                <div class="tournament-bracket__match code{{ $bracket['team1Position'] }}code code{{ $bracket['team1Position'] }}code"
                    tabindex="0">
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
                                    <abbr class="tournament-bracket__code"
                                        title="{{ $bracket['team1Position'] }}">{{ $bracket['team1Position'] }}</abbr>
                                </td>
                                <td class="tournament-bracket__score">
                                    <span class="tournament-bracket__number">{{ $bracket['team1Score'] }}</span>
                                </td>
                            </tr>
                            <tr class="tournament-bracket__team">
                                <td class="tournament-bracket__country">
                                    <abbr class="tournament-bracket__code"
                                        title="{{ $bracket['team2Position'] }}">{{ $bracket['team2Position'] }}</abbr>
                                </td>
                                <td class="tournament-bracket__score">
                                    <span class="tournament-bracket__number">{{ $bracket['team2Score'] }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="text-center mx-auto tournament-bracket__displayLargeScreen position-relative d-none-until-hover-parent"
                        style="z-index: 999;">
                        <div class="tournament-bracket__box code{{ $bracket['team1Code'] }}code px-2 py-2">
                            <span>{{ $bracket['team1Position'] }}</span>
                        </div>
                        <div class="tournament-bracket__box code{{ $bracket['team2Code'] }}code px-2 py-2">
                            <span>{{ $bracket['team2Position'] }}</span>
                        </div>
                        <small class="position-absolute winner-label d-none-until-hover" style="left: 100%;">
                            <span class="d-none-until-hover">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                    <path
                                        d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                    <path fill-rule="evenodd"
                                        d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                </svg>
                            </span>
                        </small>
                        <small class="position-absolute loser-label d-none-until-hover" style="left: 100%;">
                            @if ($bracket['loserNext'])
                                <span class="d-none-when-hover">Loser to {{ $bracket['loserNext'] }} </span>
                            @endif

                            <span class="d-none-until-hover">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                    <path
                                        d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                    <path fill-rule="evenodd"
                                        d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                </svg>
                            </span>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tournament-bracket__round tournament-bracket__round--gold d-none d-lg-block">
        <h3 class="tournament-bracket__round-title" style="visibility: hidden;">random</h3>
        <br><br><br>
        <div class="tournament-bracket__list tournament-bracket__joined-list tournament-bracket__joined-even-list">
            <div
                class="tournament-bracket__item code{{ $bracket['team1Position'] }}code code{{ $bracket['team1Position'] }}code">
                @php
                    if (isset($bracket['team1Code'])) {
                        $bracket['team1Display'] = true;
                    } else {
                        $bracket['team1Display'] = false;
                        $bracket['team1Code'] = 'N/A';
                    }

                    $bracket['team1Score'] = $bracket['team1Score'] ?? 'N/A';

                    if (!$bracket['team1Position']) {
                        $bracket['team1Position'] = '';
                    }

                @endphp

                <div class="tournament-bracket__match code{{ $bracket['team1Position'] }}code code{{ $bracket['team1Position'] }}code"
                    tabindex="0">
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
                                    <abbr class="tournament-bracket__code"
                                        title="{{ $bracket['team1Position'] }}">{{ $bracket['team1Position'] }}</abbr>
                                </td>
                                <td class="tournament-bracket__score">
                                    <span class="tournament-bracket__number">{{ $bracket['team1Score'] }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="text-center mx-auto tournament-bracket__displayLargeScreen position-relative d-none-until-hover-parent"
                        style="z-index: 999; top: 50%; left: -50%">
                        <div class="tournament-bracket__box code{{ $bracket['team1Code'] }}code px-2 py-2">
                            <span><b>?</b></span>
                        </div>
                        <small class="position-absolute winner-label d-none-until-hover " style="left: 100%;">
                            <span class="d-none-until-hover">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                    <path
                                        d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                    <path fill-rule="evenodd"
                                        d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                </svg>
                            </span>
                        </small>
                    </div>
                </div>
            </div>
            <div class="tournament-bracket__round tournament-bracket__round--gold">
                <h3 class="tournament-bracket__round-title" style="visibility: hidden;">yyy</h3>
                <br><br><br>
                <div class="tournament-bracket__list">
                    <div class="tournament-bracket__round tournament-bracket__round--gold">
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>
