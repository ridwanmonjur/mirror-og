<div class="tournament-bracket__item code{{ $bracket['team1_position'] }}code code{{ $bracket['team1_position'] }}code">
    @php
        $defaultValues = [
            'match_type' => 'tournament',
            'stage_name' => '',
            'inner_stage_name' => '',
            'team1_id' => '',
            'team2_id' => '',
            'team1_score' => '0',
            'team2_score' => '0',
            'team1_position' => '',
            'team2_position' => '',
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

        $bracket['team1Score'] = $bracket['team1Score'] ?? '0';
        $bracket['team2Score'] = $bracket['team2Score'] ?? '0';
        $bracket['winner_next_position'] = $bracket['winner_next_position'] ?? 'N/A';
        $bracket['loser_next_position'] = $bracket['loser_next_position'] ?? null;

    @endphp
    <div class="tournament-bracket__match code{{ $bracket['team1_position'] }}code code{{ $bracket['team1_position'] }}code"
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
                            title="{{ $bracket['team1_positionMobile'] }}">{{ $bracket['team1_positionMobile'] }}</abbr>
                    </td>
                    <td class="tournament-bracket__score">
                        <span class="tournament-bracket__number">{{ $bracket['team1Score'] }}</span>
                    </td>
                </tr>
                <tr class="tournament-bracket__team">
                    <td class="tournament-bracket__country">
                        <abbr class="tournament-bracket__code"
                            title="{{ $bracket['team2_positionMobile'] }}">{{ $bracket['team2_positionMobile'] }}</abbr>
                    </td>
                    <td class="tournament-bracket__score">
                        <span class="tournament-bracket__number">{{ $bracket['team2Score'] }}</span>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="text-center mx-auto tournament-bracket__displayLargeScreen position-relative d-none-until-hover-parent"
            style="z-index: 999;">
            <div class="tournament-bracket__box code{{ $bracket['team1Code'] }}code bg-light">
                <span>{{ $bracket['team1_position'] }}</span>
            </div>
            <div class="tournament-bracket__box code{{ $bracket['team2Code'] }}code bg-light">
                <span>{{ $bracket['team2_position'] }}</span>
            </div>
            <small class="position-absolute winner-label ">
                <span class="d-none-when-hover">Winner to {{ $bracket['winner_next_position'] }} </span>
                <span class="d-none-until-hover" onclick="fillModalInputs(event); event.preventDefault();">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-pencil-square" viewBox="0 0 16 16">
                        <path
                            d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                        <path fill-rule="evenodd"
                            d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                    </svg>
                </span>
            </small>
            <small @class([
                'position-absolute loser-label',
                'd-none-until-hover' => !$bracket['loser_next_position'],
            ]) @style([
                'left: 100%;' => !$bracket['loser_next_position'],
            ])>
                @if ($bracket['loser_next_position'])
                    <span class="d-none-when-hover">Loser to {{ $bracket['loser_next_position'] }} </span>
                @endif

                <span class="d-none-until-hover" 
                      onclick="fillModalInputs(event); event.preventDefault();" 

                     data-match_type="tournament" {{-- get props --}}
                    data-stage_name="" {{-- get props --}} data-inner_stage_name=""
                    data-order="{{ $bracket['order'] }}" data-team1_id="{{ $bracket['team1_id'] }}"
                    data-team2_id="{{ $bracket['team2_id'] }}" data-team1_score="{{ $bracket['team1_score'] }}"
                    data-team2_score="{{ $bracket['team2_score'] }}"
                    data-team1_position="{{ $bracket['team1_position'] }}"
                    data-team2_position="{{ $bracket['team2_position'] }}"
                    data-winner_id="{{ $bracket['winner_id'] }}" data-status="{{ $bracket['status'] }}"
                    data-result="{{ $bracket['result'] }}"
                    data-winner_next_position="{{ $bracket['winner_next_position'] }}"
                    data-loser_next_position="{{ $bracket['loser_next_position'] }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-pencil-square" viewBox="0 0 16 16">
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
