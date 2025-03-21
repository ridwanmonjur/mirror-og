<div id="bracket-list"  style="overflow-x: auto; overflow-y: visible;">
    @include('includes.__BracketModalPartials.Report')
    @include('includes.__BracketModalPartials.Update')
    @include('includes.__BracketModalPartials.Dispute')
    @if (isset($bracketList['U']))
 
        <h5 class=" mb-2 text-start"><u>Upper bracket</u></h5>
        <div class="row mb-2 custom-scrollbar">
            <div class="tournament-bracket tournament-bracket--rounded ">
                <div class="tournament-bracket__round tournament-bracket__round--quarterfinals">
                    <div class="tournament-bracket__list">
                        @foreach ($bracketList['U']['e1'] as $order => $bracket)
                            <x-brackets.bracket-first-item :bracket="$bracket" :stageName="'U'" :innerStageName="'e1'"
                                :order="$order" 
                                :wire:key="'U'. 'e1'. $loop->index" 
                            />
                        @endforeach
                    </div>
                </div>

                @php
                    $upperBracketRounds = [
                        'e2' => 'semifinals',
                        'e3' => 'semifinals',
                        'e4' => 'semifinals',
                        'pre' => 'gold',
                    ];
                @endphp

                @foreach ($upperBracketRounds as $stage => $roundClass)
                    @if (isset($bracketList['U'][$stage]))
                        <div class="tournament-bracket__round mb-2 tournament-bracket__round--{{ $stage }}">
                            <div class="tournament-bracket__list">
                                @foreach ($bracketList['U'][$stage] as $order => $bracket)
                                    <x-brackets.bracket-middle-item :bracket="$bracket" :stageName="'U'" :innerStageName="$stage"
                                        :order="$order" 
                                        :wire:key="'U'. $stage. $loop->index" 
                                    />
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach

                <div class="tournament-bracket__round tournament-bracket__round--gold mt-2">
                </div>
            </div>
            <x-brackets.bracket-winner-item :bracket="$bracketList['fin']['fin'][0]" :stageName="'fin'" :innerStageName="'fin'" :order="0"
                :winner="$bracketList['fin']['winner'][0]"
            />

        </div>
        <h5 class="mb-2 text-start"><u>Lower bracket</u></h5>
        <div class="tournament-bracket tournament-bracket--rounded custom-scrollbar">
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
                                <x-brackets.bracket-middle-item :bracket="$bracket" :stageName="'L'" :innerStageName="$stage"
                                    :order="$order" 
                                    :wire:key="'L'. $stage. $loop->index" 
                                />
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach


            <div class="tournament-bracket__round tournament-bracket__round--semifinals">
                <div class="tournament-bracket__list tournament-bracket__joined-list tournament-bracket__joined-odd-list">
                    @foreach ($bracketList['L']['pre1'] as $order => $bracket)
                        <x-brackets.bracket-middle-item :bracket="$bracket" :order="$order" :stageName="'L'"
                            :innerStageName="'pre1'"  />
                    @endforeach
                </div>
            </div>


            <div class="tournament-bracket__round tournament-bracket__round--semifinals">
                <div class="tournament-bracket__list tournament-bracket__joined-list tournament-bracket__joined-even-list">
                    @foreach ($bracketList['L']['pre2'] as $order => $bracket)
                        <x-brackets.bracket-middle-item :bracket="$bracket" :order="$order" :stageName="'L'"
                            :innerStageName="'pre2'"  />
                    @endforeach
                </div>
            </div>

            <div class="tournament-bracket__round tournament-bracket__round--gold mt-2">

            </div>
        </div>

    @else
        <h5 class=" mb-2 text-start"><u>Upper bracket</u></h5>
        <p class="mmt-2"> Bracket is not set </p>
    @endif

</div>
