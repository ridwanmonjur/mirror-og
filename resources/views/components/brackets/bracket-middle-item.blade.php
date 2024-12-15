@props(['bracket', 'tournament', 'stageName', 'innerStageName', 'order'])
<div class="tournament-bracket__item tournament d-none-until-hover2-parent">
    <div class="tournament-bracket__match  middle-item {{ $bracket['team1_position'] }} {{ $bracket['team2_position'] }} popover-parent "
        tabindex="0" data-bracket="{{ json_encode($bracket) }}" data-stage_name="{{ $stageName }}"
        data-inner_stage_name="{{ $innerStageName }}" data-order="{{ $order }}" data-item-type="middle"
    >
        <x-brackets.bracket-table :bracket="$bracket" />
        <div class="text-center mx-auto tournament-bracket__displayLargeScreen position-relative  ">
            <x-brackets.bracket-middle-item-popover :position1="$bracket['team1_position']" :teamBanner1="$bracket['team1_teamBanner']" :teamId1="$bracket['team1_id']"
                :position2="$bracket['team2_position']" :teamBanner2="$bracket['team2_teamBanner']" :teamName2="$bracket['team2_teamName']" :teamId2="$bracket['team2_id']"
                :teamName1="$bracket['team1_teamName']" :winner_next_position="$bracket['winner_next_position']" :loser_next_position="$bracket['loser_next_position']" 
            />
            <x-brackets.bracket-middle-desktop-item :position1="$bracket['team1_position']" :teamBanner1="$bracket['team1_teamBanner']" :teamId1="$bracket['team1_id']"
                :position2="$bracket['team2_position']" :teamBanner2="$bracket['team2_teamBanner']" :teamId2="$bracket['team2_id']" />
            <small class="position-absolute winner-label ">
                @if($bracket['user_level'] === $USER_ACCESS['IS_ORGANIZER'])
                    <span class="d-none-until-hover2">
                        <svg 
                            data-team1_id="{{$bracket['team1_position']}}" data-team2_id="{{$bracket['team2_position']}}"
                            onclick="updateModalShow(event); " style="z-index: 999;"
                            xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-pencil-square  cursor-pointer me-2" viewBox="0 0 16 16">
                            <path
                                d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                            <path fill-rule="evenodd"
                                d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                        </svg>
                    </span>
                @endif
                <span class="d-none-when-hover">Winner to {{ $bracket['winner_next_position'] }} </span>
            </small>
            <small @class(['position-absolute loser-label']) @style([
                'left: 100%;' => !$bracket['loser_next_position'],
            ])>
                @if ($bracket['loser_next_position'])
                    <span class="d-none-when-hover">Loser to {{ $bracket['loser_next_position'] }} </span>
                @endif
            </small>
        </div>
    </div>
</div>
