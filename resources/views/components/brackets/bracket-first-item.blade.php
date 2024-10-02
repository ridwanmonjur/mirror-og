<div class="tournament-bracket__item tournament  d-none-until-hover2-parent">
    @php

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
    <div class="tournament-bracket__match tournament {{ $bracket['team1_position'] }} {{ $bracket['team2_position'] }}"
        tabindex="0" data-bracket="{{ json_encode($bracket) }}" data-stage_name="{{ $stageName }}"
        data-inner_stage_name="{{ $innerStageName }}" data-order="{{ $order }}">
        <x-brackets.bracket-table :bracket="$bracket" />
        <div class="text-center mx-auto tournament-bracket__displayLargeScreen position-relative  ">
            <x-brackets.bracket-middle-desktop-item-plus-popover :position="$bracket['team1_position']" :teamBanner="$bracket['team1_teamBanner']" :teamId="$bracket['team1_id']"
                :roster="$bracket['team1_roster']" />

            <x-brackets.bracket-middle-desktop-item-plus-popover :position="$bracket['team2_position']" :teamBanner="$bracket['team2_teamBanner']" :teamId="$bracket['team2_id']"
                :roster="$bracket['team2_roster']" />

            <small class="position-absolute winner-label ">
                <span class="d-none-until-hover2" onclick="fillModalInputs(event); event.preventDefault();">
                    <svg style="z-index: 999;" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                        fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
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
