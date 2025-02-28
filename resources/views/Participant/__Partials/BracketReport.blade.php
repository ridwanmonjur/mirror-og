<input type="hidden" id="eventId" value="{{$event->id}}">
<input type="hidden" id="previousValues" value="{{ json_encode($previousValues) }}">
<input type="hidden" id="joinEventTeamId" value="{{$existingJoint?->team_id }}">
<input type="hidden" id="userLevelEnums" value="{{json_encode($USER_ACCESS)}}">
@include('Shared.__BracketModalPartials.Report')
@include('Shared.__BracketModalPartials.Dispute')
<div id="bracket-list custom-scrollbar"  class="tab-bracketlist"
>
    @if (isset($bracketList['upperBracket']))

        <h5 class=" mb-2 text-start"><u>Upper bracket</u></h5>
        <div class="mb-2">
            <div class="tournament-bracket tournament-bracket--rounded ">
                <div class="tournament-bracket__round tournament-bracket__round--quarterfinals">
                    <div class="tournament-bracket__list">
                        @foreach ($bracketList['upperBracket']['eliminator1'] as $order => $bracket)
                            <x-brackets.bracket-first-item :bracket="$bracket" :stageName="'upperBracket'" :innerStageName="'eliminator1'"
                                :order="$order" 
                                :wire:key="'upperBracket'. 'eliminator1'. $loop->index" />
                        @endforeach
                    </div>
                </div>

                @php
                    $upperBracketRounds = [
                        'eliminator2' => 'semifinals',
                        'eliminator3' => 'semifinals',
                        'eliminator4' => 'semifinals',
                        'prefinals' => 'gold',
                    ];
                @endphp

                @foreach ($upperBracketRounds as $stage => $roundClass)
                    @if (isset($bracketList['upperBracket'][$stage]))
                        <div class="tournament-bracket__round mb-2 tournament-bracket__round--{{ $stage }}">
                            <div class="tournament-bracket__list">
                                @foreach ($bracketList['upperBracket'][$stage] as $order => $bracket)
                                    <x-brackets.bracket-middle-item :bracket="$bracket" :stageName="'upperBracket'" :innerStageName="$stage"
                                        :order="$order" 
                                        :wire:key="'upperBracket'. $stage. $loop->index" />
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach

                <div class="tournament-bracket__round tournament-bracket__round--gold mt-2">
                </div>
            </div>

           <x-brackets.bracket-winner-item :bracket="$bracketList['finals']['finals'][0]" :stageName="'finals'" :innerStageName="'finals'" :order="0"
                :winner="$bracketList['finals']['winner'][0]"
            />

        </div>
        <h5 class="mb-2 text-start"><u>Lower bracket</u></h5>
        <div class="tournament-bracket tournament-bracket--rounded custom-scrollbar" >
            @php
                $rounds = [
                    'eliminator1' => 'tournament-bracket__joined-odd-list',
                    'eliminator2' => 'tournament-bracket__joined-even-list',
                    'eliminator3' => 'tournament-bracket__joined-odd-list',
                    'eliminator4' => 'tournament-bracket__joined-even-list',
                    'eliminator5' => 'tournament-bracket__joined-odd-list',
                    'eliminator6' => 'tournament-bracket__joined-even-list',
                ];
            @endphp

            @foreach ($rounds as $stage => $listClass)
                @if (isset($bracketList['lowerBracket'][$stage]))
                    <div class="tournament-bracket__round tournament-bracket__round--{{$stage}}">
                        <div class="tournament-bracket__list {{ $listClass }}">
                            @foreach ($bracketList['lowerBracket'][$stage] as $order => $bracket)
                                <x-brackets.bracket-middle-item :bracket="$bracket" :stageName="'lowerBracket'" :innerStageName="$stage"
                                    :order="$order" 
                                    :wire:key="'lowerBracket'. $stage. $loop->index" />
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach


            <div class="tournament-bracket__round tournament-bracket__round--semifinals">
                <div class="tournament-bracket__list tournament-bracket__joined-list tournament-bracket__joined-odd-list">
                    @foreach ($bracketList['lowerBracket']['prefinals1'] as $order => $bracket)
                        <x-brackets.bracket-middle-item :bracket="$bracket" :order="$order" :stageName="'lowerBracket'"
                            :innerStageName="'prefinals1'"  />
                    @endforeach
                </div>
            </div>


            <div class="tournament-bracket__round tournament-bracket__round--semifinals">
                <div class="tournament-bracket__list tournament-bracket__joined-list tournament-bracket__joined-even-list">
                    @foreach ($bracketList['lowerBracket']['prefinals2'] as $order => $bracket)
                        <x-brackets.bracket-middle-item :bracket="$bracket" :order="$order" :stageName="'lowerBracket'"
                            :innerStageName="'prefinals2'"  />
                    @endforeach
                </div>
            </div>

            <div class="tournament-bracket__round tournament-bracket__round--gold mt-2 mb-0 pb-0">

            </div>
        </div>

    @else
        <h5 class=" mb-2 text-start"><u>Upper bracket</u></h5>
        <p class="mt-2"> Bracket is not set </p>
    @endif
</div>
