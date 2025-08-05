
<div id="bracket-list" style="overflow-x: auto; overflow-y: visible;">
    @include('includes.BracketModal.Report')
    @include('includes.BracketModal.Update')
    @include('includes.BracketModal.Dispute')
    @if ($event?->type?->eventType == "Tournament")
        @if (isset($bracketList['U']))

            <h5 class="mt-4 mb-2 text-start"><u>Upper bracket</u></h5>
            <div class="row mb-2 custom-scrollbar h-100  px-0 mx-0 ">
                <div class="col-12 col-xl-6 d-inline-flex tournament-bracket tournament-bracket--rounded ">
                    <div class="tournament-bracket__round tournament-bracket__round--quarterfinals">
                        <div class="tournament-bracket__list">
                            @foreach ($bracketList['U']['e1'] as $order => $bracket)
                                <x-brackets.bracket-first-item :bracket="$bracket" :stageName="'U'" :innerStageName="'e1'"
                                    :order="$order" :wire:key="'U'. 'e1'. $loop->index" />
                            @endforeach
                        </div>
                    </div>

                    @php
                        $upperBracketRounds = [
                            'e2' => 'semifinals',
                            'e3' => 'semifinals',
                            'e4' => 'semifinals',
                            'p0' => 'gold',
                        ];
                    @endphp

                    @foreach ($upperBracketRounds as $stage => $roundClass)
                        @if (isset($bracketList['U'][$stage]))
                            <div class="tournament-bracket__round mb-2 tournament-bracket__round--{{ $stage }}">
                                <div class="tournament-bracket__list">
                                    @foreach ($bracketList['U'][$stage] as $order => $bracket)
                                        <x-brackets.bracket-middle-item :bracket="$bracket" :stageName="'U'"
                                            :innerStageName="$stage" :order="$order" :wire:key="'U'. $stage. $loop->index" />
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach

                    <div class="tournament-bracket__round tournament-bracket__round--gold mt-2">
                    </div>
                </div>
                <x-brackets.bracket-winner-item :bracket="$bracketList['F']['F'][0]" :stageName="'F'" :innerStageName="'F'"
                    :order="0" :winner="$bracketList['F']['W'][0]" />

            </div>
            <h5 class="mb-2 text-start"><u>Lower bracket</u></h5>
            <div class="tournament-bracket tournament-bracket--rounded ">
                @php
                    $rounds = [
                        'e1' => 'tournament-bracket__joined-odd-list',
                        'e2' => 'tournament-bracket__joined-even-list',
                        'e3' => 'tournament-bracket__joined-odd-list',
                        'e4' => 'tournament-bracket__joined-even-list',
                        'e5' => 'tournament-bracket__joined-odd-list',
                        'e6' => 'tournament-bracket__joined-even-list',
                    ];
                @endphp

                @foreach ($rounds as $stage => $listClass)
                    @if (isset($bracketList['L'][$stage]))
                        <div class="tournament-bracket__round tournament-bracket__round--{{ $stage }}">
                            <div class="tournament-bracket__list {{ $listClass }}">
                                @foreach ($bracketList['L'][$stage] as $order => $bracket)
                                    <x-brackets.bracket-middle-item :bracket="$bracket" :stageName="'L'"
                                        :innerStageName="$stage" :order="$order" :wire:key="'L'. $stage. $loop->index" />
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach


                <div class="tournament-bracket__round tournament-bracket__round--semifinals">
                    <div
                        class="tournament-bracket__list tournament-bracket__joined-list tournament-bracket__joined-odd-list">
                        @foreach ($bracketList['L']['p1'] as $order => $bracket)
                            <x-brackets.bracket-middle-item :bracket="$bracket" :order="$order" :stageName="'L'"
                                :innerStageName="'p1'" />
                        @endforeach
                    </div>
                </div>


                <div class="tournament-bracket__round tournament-bracket__round--semifinals">
                    <div
                        class="tournament-bracket__list tournament-bracket__joined-list tournament-bracket__joined-even-list">
                        @foreach ($bracketList['L']['p2'] as $order => $bracket)
                            <x-brackets.bracket-middle-item :bracket="$bracket" :order="$order" :stageName="'L'"
                                :innerStageName="'p2'" />
                        @endforeach
                    </div>
                </div>

                <div class="tournament-bracket__round tournament-bracket__round--gold mt-2">

                </div>
            </div>
        @else
            <h5 class=" mb-2 text-start"><u>Upper bracket</u></h5>
            <p class="mmt-2"> Bracket is not ready till you choose a tier </p>
        @endif
    @elseif ($event?->type?->eventType == "League")
        <div id="bracket-list" class="custom-scrollbar ">

            {{-- Pagination Component --}}
            @if(isset($pagination))
                <x-pagination.league-pagination :pagination="$pagination" />
            @endif


            @if (isset($bracketList) && !empty($bracketList))
                <h5 class="mt-3 mb-2 text-start"><u>Rounds & Results</u></h5>
                <div class="mb-2 mx-auto px-0 mx-0 ">

                    @foreach ($bracketList as $roundKey => $roundData)
                        <div class="my-3 ">
                            <h6>Round {{ $roundKey }}</h6>
                            <div class="row">
                                @foreach ($roundData as $fakeKey => $actualRoundData[0])
                                     {{-- <h5>{{$fakeKey}}</h5> --}}
                                     {{-- <h5>{{json_encode($actualRoundData[0])}}</h5> --}}
                                     @foreach ($actualRoundData[0] as $match) 
                                    <div class="my-3 col-12 col-md-6 col-xxl-4">
                                        <div>
                                            {{-- <h5>{{$fakeKey}}</h5> --}}
                                            <div class=" d-none-until-hover2-parent">
                                                <div class="table-report middle-item {{ $match['team1_position'] }} {{ $match['team2_position'] }} popover-parent "
                                                    tabindex="0" data-bracket="{{ json_encode($match) }}"
                                                    data-stage_name="{{ $fakeKey }}"
                                                    data-inner_stage_name="{{ $fakeKey }}" 
                                                    data-order="{{ $match['order'] }}" 
                                                    data-item-type="middle">
                                                    <x-brackets.bracket-table :bracket="$match" :isLeague="true" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach  
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
        
    @endif

</div>
