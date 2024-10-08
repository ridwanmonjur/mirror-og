@props(['bracket', 'tournament', 'stageName', 'innerStageName', 'order'])
<div class="tournament-bracket__item tournament d-none-until-hover2-parent">
    @php

    @endphp

    <div class="tournament-bracket__match  middle-item {{ $bracket['team1_position'] }} {{ $bracket['team2_position'] }} popover-parent "
        tabindex="0" data-bracket="{{ json_encode($bracket) }}" data-stage_name="{{ $stageName }}"
        data-inner_stage_name="{{ $innerStageName }}" data-order="{{ $order }}" data-item-type="middle">
        <x-brackets.bracket-table :bracket="$bracket" />

        <div class="text-center mx-auto tournament-bracket__displayLargeScreen position-relative  ">
            <x-brackets.bracket-middle-item-popover :position1="$bracket['team1_position']" :teamBanner1="$bracket['team1_teamBanner']" :teamId1="$bracket['team1_id']"
                :score1="$bracket['team1_score']" :position2="$bracket['team2_position']" :teamBanner2="$bracket['team2_teamBanner']" :teamName2="$bracket['team2_teamName']" :teamId2="$bracket['team2_id']"
                :score2="$bracket['team2_score']" :teamName1="$bracket['team1_teamName']" :winner_next_position="$bracket['winner_next_position']" :loser_next_position="$bracket['loser_next_position']" :status="$bracket['status']" />
            <x-brackets.bracket-middle-desktop-item :position1="$bracket['team1_position']" :teamBanner1="$bracket['team1_teamBanner']" :teamId1="$bracket['team1_id']"
                :score1="$bracket['team1_score']" :position2="$bracket['team2_position']" :teamBanner2="$bracket['team2_teamBanner']" :teamId2="$bracket['team2_id']" :score2="$bracket['team2_score']" />

            <small class="position-absolute winner-label ">
                <span class="d-none-until-hover2">
                    <svg onclick="updateModalShow(event); " style="z-index: 999;"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-pencil-square  cursor-pointer me-2" viewBox="0 0 16 16">
                        <path
                            d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                        <path fill-rule="evenodd"
                            d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                    </svg>
                    <svg
                         onclick="reportModalShow(event);" 
                        style="z-index: 999;" class="me-2  cursor-pointer " fill="currentColor" height="16px"
                        width="16px" version="1.1" xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 128 128" xml:space="preserve">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                        <g id="SVGRepo_iconCarrier">
                            <g id="H1_copy">
                                <g>
                                    <g>
                                        <path
                                            d="M64.4,89.6l-3.1,0l0-16.5l3.1,0L64.4,89.6z M64.5,56.6l-3.1,0l0-16.5l3.1,0L64.5,56.6z M64.6,23.6l-3.1,0l0-16.5l3.1,0 L64.6,23.6z">
                                        </path>
                                    </g>
                                </g>
                            </g>
                            <g id="Man">
                                <g>
                                    <circle cx="101" cy="17.4" r="9.7"></circle>
                                    <path
                                        d="M113.2,29.6H89c-6.7,0-12.1,5.4-12.1,12.1v29.6c0,2.3,1.9,4.3,4.3,4.3s4.3-1.9,4.3-4.3V44.2c0-0.7,0.6-1.2,1.2-1.2 c0.7,0,1.2,0.6,1.2,1.2v73.5c0,3.6,2.7,6.6,6.1,6.6c3.4,0,6.1-3,6.1-6.6V75.8c0-0.7,0.6-1.2,1.2-1.2c0.7,0,1.2,0.6,1.2,1.2v41.9 c0,3.6,2.7,6.6,6.1,6.6c3.4,0,6.1-3,6.1-6.6V44.2c0-0.7,0.6-1.2,1.2-1.2c0.7,0,1.2,0.6,1.2,1.2v27.3c0,2.3,1.9,4.3,4.3,4.3 s4.3-1.9,4.3-4.3V41.7C125.3,35,119.8,29.6,113.2,29.6z">
                                    </path>
                                </g>
                                <g>
                                    <circle cx="27.2" cy="17.4" r="9.7"></circle>
                                    <path
                                        d="M39.4,29.6H15.1C8.4,29.6,3,35,3,41.7v29.6c0,2.3,1.9,4.3,4.3,4.3s4.3-1.9,4.3-4.3V44.2c0-0.7,0.6-1.2,1.2-1.2 c0.7,0,1.2,0.6,1.2,1.2v73.5c0,3.6,2.7,6.6,6.1,6.6c3.4,0,6.1-3,6.1-6.6V75.8c0-0.7,0.6-1.2,1.2-1.2s1.2,0.6,1.2,1.2v41.9 c0,3.6,2.7,6.6,6.1,6.6c3.4,0,6.1-3,6.1-6.6V44.2c0-0.7,0.6-1.2,1.2-1.2s1.2,0.6,1.2,1.2v27.3c0,2.3,1.9,4.3,4.3,4.3 c2.4,0,4.3-1.9,4.3-4.3V41.7C51.5,35,46,29.6,39.4,29.6z">
                                    </path>
                                </g>
                            </g>
                            <g id="Layer_1">
                                <path
                                    d="M74.6,116.4c-0.7-2.6-4.2-4.7-7.2-1.9l-2.8-10.2l6.2-17.4c-0.2-0.1-0.5,0-0.7,0.4l-7,12.8l-2-3.6v6.4l0.5,1.4l-2.8,10.2 c-3-2.8-6.5-0.7-7.2,1.9c-0.6,2.3,0.5,4.6,3.2,5.3c2.1,0.6,4.5-0.7,5.2-3.1l2.7-10.1h0.9l2.7,10.1c0.7,2.4,3.1,3.6,5.2,3.1 C74.1,121,75.3,118.7,74.6,116.4z M57.8,118c-0.3,1.1-1.5,1.8-2.6,1.5c-1.1-0.3-1.8-1.5-1.5-2.6c0.3-1.1,1.5-1.8,2.6-1.5 C57.4,115.7,58.1,116.9,57.8,118z M63.1,105.2c-0.5,0-0.9-0.4-0.9-0.9c0-0.5,0.4-0.9,0.9-0.9c0.5,0,0.9,0.4,0.9,0.9 C64,104.9,63.6,105.2,63.1,105.2z M71.1,119.5c-1.1,0.3-2.3-0.4-2.6-1.5c-0.3-1.1,0.4-2.3,1.5-2.6c1.1-0.3,2.3,0.4,2.6,1.5 C72.9,118,72.3,119.2,71.1,119.5z">
                                </path>
                            </g>
                        </g>
                    </svg>

                </span>
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
