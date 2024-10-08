<div class="tournament-bracket__item tournament  d-none-until-hover2-parent">
    @php

      
    @endphp
    <div class="tournament-bracket__match tournament first-item {{ $bracket['team1_position'] }} {{ $bracket['team2_position'] }}"
        tabindex="0" data-bracket="{{ $bracket['willJsonTeam'] ? json_encode($bracket) : '[]' }}" data-stage_name="{{ $stageName }}"
        data-inner_stage_name="{{ $innerStageName }}" data-order="{{ $order }}"
        data-item-type="first"
    >
        <x-brackets.bracket-table :bracket="$bracket" />
        <div class="text-center mx-auto tournament-bracket__displayLargeScreen position-relative  ">

            <x-brackets.bracket-first-item-popover 
                :position="$bracket['team1_position']" 
                :teamBanner="$bracket['team1_teamBanner']" 
                :teamId="$bracket['team1_id']"
                :teamName="$bracket['team1_teamName']"
                :roster="$bracket['team1_roster']" 
            />

            <x-brackets.bracket-first-item-popover 
                :position="$bracket['team2_position']" 
                :teamBanner="$bracket['team2_teamBanner']" 
                :teamId="$bracket['team2_id']"
                :roster="$bracket['team2_roster']" 
                :teamName="$bracket['team2_teamName']"

                />
            
            <x-brackets.bracket-middle-item-popover
                :position1="$bracket['team1_position']"
                :teamBanner1="$bracket['team1_teamBanner']"
                :teamId1="$bracket['team1_id']"
                :score1="$bracket['team1_score']"
                :position2="$bracket['team2_position']"
                :teamBanner2="$bracket['team2_teamBanner']"
                :teamName2="$bracket['team2_teamName']"
                :teamId2="$bracket['team2_id']"
                :score2="$bracket['team2_score']"
                :teamName1="$bracket['team1_teamName']"
                :winner_next_position="$bracket['winner_next_position']"
                :loser_next_position="$bracket['loser_next_position']"
                :status="$bracket['status']"
            />

            <small class="position-absolute winner-label ">
                <span class="d-none-until-hover2" onclick="updateModalShow(event);">
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
