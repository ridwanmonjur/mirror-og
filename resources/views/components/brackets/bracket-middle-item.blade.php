<div class="tournament-bracket__item tournament  d-none-until-hover2-parent">
    @php
        $defaultValues = [
            'id' => null,
            'match_type' => 'tournament',
            'stage_name' => '',
            'inner_stage_name' => '',
            'team1_id' => '',
            'team2_id' => '',
            'team1_score' => '0',
            'team2_score' => '0',
            'team1_position' => '',
            'team2_position' => '',
            'team1_roster' => null,
            'team1_teamBanner' => null,
            'team2_roster' => null,
            'team2_teamBanner' => null,
            'winner_id' => '',
            'status' => '',
            'result' => '',
            'winner_next_position' => '',
            'loser_next_position' => '',
            'team1Code' => 'N/A',
            'team2Code' => 'N/A',
            'winner_next_position' => 'N/A',
            'loser_next_position' => null,
        ];

        foreach ($defaultValues as $key => $defaultValue) {
            $bracket[$key] = $bracket[$key] ?? $defaultValue;
        }

        if (isset($bracket['team1Code']) && $bracket['team1Code'] !== 'N/A') {
            $bracket['team1Display'] = true;
        } else {
            $bracket['team1Display'] = false;
            $bracket['team1Code'] = 'N/A';
        }

        if (isset($bracket['team2Code']) && $bracket['team2Code'] !== 'N/A') {
            $bracket['team2Display'] = true;
        } else {
            $bracket['team2Display'] = false;
            $bracket['team2Code'] = 'N/A';
        }

        if (!$bracket['team1_position']) {
            $bracket['team1_position'] = '';
            $bracket['team1_positionMobile'] = 'TBD';
        } else {
            $bracket['team1_positionMobile'] = $bracket['team1_position'];
        }

        if (!$bracket['team2_position']) {
            $bracket['team2_position'] = '';
            $bracket['team2_positionMobile'] = 'TBD';
        } else {
            $bracket['team2_positionMobile'] = $bracket['team2_position'];
        }

        $bracket['team1_score'] = $bracket['team1_score'] ?? '0';
        $bracket['team2_score'] = $bracket['team2_score'] ?? '0';
        $bracket['winner_next_position'] = $bracket['winner_next_position'] ?? 'N/A';
        $bracket['loser_next_position'] = $bracket['loser_next_position'] ?? null;
    @endphp

    <div class="tournament-bracket__match {{ $bracket['team1_position'] }} {{ $bracket['team2_position'] }}"
        tabindex="0" data-bracket="{{ json_encode($bracket) }}" data-stage_name="{{ $stageName }}"
        data-inner_stage_name="{{ $innerStageName }}" data-order="{{ $order }}">
        <table class="tournament-bracket__table mx-auto">
            <thead class="sr-only">
                <tr>
                    <th>Country</th>
                    <th>Score</th>
                </tr>
            </thead>
            <tbody class="tournament-bracket__content">
                <tr class="tournament-bracket__team tournament-bracket__team--winner">
                    <td class="tournament-bracket__country position-relative {{ $bracket['team2_position'] }}">
                        <abbr class="tournament-bracket__code position-absolute"
                            title="{{ $bracket['team1_positionMobile'] }}">{{ $bracket['team1_position'] }}</abbr>
                    </td>
                    <td class="tournament-bracket__score">
                        <span class="tournament-bracket__number">{{ $bracket['team1_score'] }}</span>
                    </td>
                </tr>
                <tr class="tournament-bracket__team">
                    <td class="tournament-bracket__country position-relative {{ $bracket['team2_position'] }}">
                        <abbr class="tournament-bracket__code position-absolute"
                            title="{{ $bracket['team2_positionMobile'] }}">{{ $bracket['team2_position'] }}</abbr>
                    </td>
                    <td class="tournament-bracket__score">
                        <span class="tournament-bracket__number">{{ $bracket['team2_score'] }}</span>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="text-center mx-auto tournament-bracket__displayLargeScreen position-relative  ">
            <div
                class="tournament-bracket__box -parent popover-parent position-relative {{ $bracket['team1_position'] }} tournament bg-light">
                <div class="popover-content d-none"
                    style="top: 100%; left: 100%; opacity: 1; z-index: 999 !important;">
                    <div class="popover-box px-5 py-2" style="margin-bottom: -10px; background: white !important;">
                        <img src="/storage/{{ $bracket['team1_teamBanner'] }}" alt="Team Banner" width="100"
                            height="100" class="mb-3" onerror="this.src='/assets/images/404.png';">
                        <br><small>{{$bracket['team1_position']}} </small>
                        @if ($bracket['team1_roster'])
                            <ul class="list-group">
                                @foreach ($bracket['team1_roster'] as $roster)
                                    <li class="d-flex align-items-center">
                                        <img width="30" height="30" onerror="this.src='/assets/images/404.png';"
                                            src="/storage/{{ $roster->user->userBanner }}" alt="User Banner"
                                            class="mb-2 rounded-circle object-fit-cover me-3">
                                        {{ $roster->user->name }}
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted">The team roster is empty.</p>
                        @endif
                    </div>
                </div>
                @if ($bracket['team1_id'])
                    <img src="/storage/{{ $bracket['team1_teamBanner'] }}" width="100%" height="30"
                        onerror="this.src='/assets/images/404.png';"
                        class="popover-button position-absolute d-none-when-hover object-fit-cover me-2" alt="Team View"
                        style="z-index: 99;">
                    <span ></span>
                @else
                     <span ></span>
                @endif
            </div>
            <div
                class="tournament-bracket__box -parent popover-parent position-relative {{ $bracket['team2_position'] }} tournament bg-light">
                <div class="popover-content d-none">
                    <div class="popover-box px-5 py-2" style="margin-bottom: -10px; background: white !important;">
                        <img src="/storage/{{ $bracket['team2_teamBanner'] }}" alt="Team Banner" width="100"
                            height="100" class="mb-3" onerror="this.src='/assets/images/404.png';">
                        <br><small>{{$bracket['team2_position']}} </small>
                        @if ($bracket['team1_roster'])
                            <ul class="list-group">
                                @foreach ($bracket['team2_roster'] as $roster)
                                    <li class="d-flex align-items-center">
                                        <img width="30" height="30" onerror="this.src='/assets/images/404.png';"
                                            src="/storage/{{ $roster->user->userBanner }}" alt="User Banner"
                                            class="mb-2 rounded-circle object-fit-cover me-3">
                                        {{ $roster->user->name }}
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted">The team roster is empty or not available.</p>
                        @endif
                    </div>
                </div>
                @if ($bracket['team2_id'])
                    <img src="/storage/{{ $bracket['team1_teamBanner'] }}" width="100%" height="30"
                        onerror="this.src='/assets/images/404.png';"
                        class="popover-button position-absolute d-none-when-hover object-fit-cover me-2" alt="Team View"
                        style="z-index: 99;">
                    <span></span>
                @else
                     <span ></span>
                @endif
            </div>
            <small class="position-absolute winner-label ">
                <span class="d-none-until-hover2" onclick="fillModalInputs(event); event.preventDefault();">
                    <svg 
                        
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-pencil-square" viewBox="0 0 16 16">
                        <path
                            d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                        <path fill-rule="evenodd"
                            d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                    </svg>
                </span>
                <span class="d-none-when-hover">Winner to {{ $bracket['winner_next_position'] }} </span>

            </small>
            <small @class([
                'position-absolute loser-label',
                'd-none' => !$bracket['loser_next_position'],
            ]) @style([
                'left: 100%;' => !$bracket['loser_next_position'],
            ])>
                @if ($bracket['loser_next_position'])
                    <span class="d-none-when-hover">Loser to {{ $bracket['loser_next_position'] }} </span>
                @endif

            </small>
        </div>
    </div>
</div>
