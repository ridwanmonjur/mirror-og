
<input type="hidden" id="eventId" value="{{ $event->id }}">
<input type="hidden" id="previousValues" value="{{ json_encode($previousValues) }}">
<input type="hidden" id="joinEventTeamId" value="{{ $existingJoint?->team_id }}">
<input type="hidden" id="userLevelEnums" value="{{ json_encode($USER_ACCESS) }}">
@include('includes.BracketModal.Report')
@include('includes.BracketModal.Dispute')
<div id="bracket-list" class="custom-scrollbar tab-bracketlist">

        @if (isset($bracketList['league']['standings']))
            <h5 class="mb-2 text-start"><u>League Standings</u></h5>
            <div class="mb-2 row px-0 mx-0">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Position</th>
                                    <th>Team</th>
                                    <th>Played</th>
                                    <th>Won</th>
                                    <th>Lost</th>
                                    <th>Points</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bracketList['league']['standings'] as $position => $team)
                                    <tr>
                                        <td>{{ $position + 1 }}</td>
                                        <td>{{ $team['name'] }}</td>
                                        <td>{{ $team['played'] }}</td>
                                        <td>{{ $team['won'] }}</td>
                                        <td>{{ $team['lost'] }}</td>
                                        <td>{{ $team['points'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        @if (isset($bracketList) && !empty($bracketList))
            <h5 class="mt-3 mb-2 text-start"><u>Rounds & Results</u></h5>
            <div class="mb-2 px-0 mx-0">
                    
                    @foreach ($bracketList['table'] as $roundKey => $roundData)
                        <div class="my-3 ">
                            <h6>Round {{ $roundKey }}</h6>
                            <div class="row">
                                @foreach ($roundData as $matchKey => $match)
                                    
                                    <div class="my-3 col-12 col-lg-6">
                                        <div class=" d-none-until-hover2-parent">
                                            <div class="table-report middle-item {{ $match['team1_position'] }} {{ $match['team2_position'] }} popover-parent "
                                                tabindex="0" data-bracket="{{ json_encode($match) }}" 
                                               data-stage_name="table"
                                                data-inner_stage_name="{{ $roundKey }}" 
                                                data-order="{{ $match['order'] }}" 
                                                data-item-type="middle"
                                            >
                                                <x-brackets.bracket-table :bracket="$match" :isLeague="false"  />
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
            </div>
        @endif

</div>
