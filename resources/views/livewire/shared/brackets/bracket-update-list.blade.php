@section('content')

    <main>
        @include('__CommonPartials.NavbarGoToSearchPage')
        <div class="px-4">
            @livewire('shared.brackets.bracket-first-items-update-modal', [
                'event' => $event,
                'teamList' => $teamList
            ])
            <h5 class="mt-5 mb-4  text-start">Upper bracket</h5>
            <div class="row ">
                <div class="tournament-bracket tournament-bracket--rounded col-lg-9 col-xl-8 col-xxl-6">
                    <div class="tournament-bracket__round tournament-bracket__round--quarterfinals">
                        <div class="tournament-bracket__list">
                            @foreach ($bracketList['upperBracket']['eliminator1'] as $bracket)
                                <x-brackets.bracket-first-item :bracket="$bracket"
                                    :wire:key="'upperBracket'. 'eliminator1'. $loop->index" />
                            @endforeach
                        </div>
                    </div>

                    <div class="tournament-bracket__round tournament-bracket__round--semifinals">
                        <div class="tournament-bracket__list">
                            @foreach ($bracketList['upperBracket']['eliminator2'] as $bracket)
                                <x-brackets.bracket-middle-item :bracket="$bracket"
                                    :wire:key="'upperBracket'. 'eliminator2'. $loop->index" />
                            @endforeach
                        </div>
                    </div>

                    @if (isset($bracketList['upperBracket']['eliminator3']))
                        <div class="tournament-bracket__round tournament-bracket__round--semifinals">
                            <div class="tournament-bracket__list">
                                @foreach ($bracketList['upperBracket']['eliminator3'] as $bracket)
                                    <x-brackets.bracket-middle-item :bracket="$bracket"
                                        :wire:key="'upperBracket'. 'eliminator3'. $loop->index" />
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if (isset($bracketList['upperBracket']['eliminator4']))
                        <div class="tournament-bracket__round tournament-bracket__round--semifinals">
                            <div class="tournament-bracket__list">
                                @foreach ($bracketList['upperBracket']['eliminator4'] as $bracket)
                                    <x-brackets.bracket-middle-item :bracket="$bracket"
                                        :wire:key="'upperBracket'. 'eliminator4'. $loop->index" />
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="tournament-bracket__round tournament-bracket__round--gold">
                        <div class="tournament-bracket__list">
                            @foreach ($bracketList['upperBracket']['prefinals'] as $bracket)
                                <x-brackets.bracket-middle-item :bracket="$bracket"
                                    :wire:key="'upperBracket'. 'prefinals'. $loop->index" />
                            @endforeach
                        </div>
                    </div>
                    <div class="tournament-bracket__round tournament-bracket__round--gold">
                    </div>
                </div>

                @foreach ($bracketList['finals']['finals'] as $bracket)
                    <x-brackets.bracket-winner-item :bracket="$bracket"
                        :wire:key="'upperBracket'. 'eliminator1'. $loop->index" />
                @endforeach

            </div>
            <h5 class="mt-5 mb-4 text-start">Lower bracket</h5>
            <div class="tournament-bracket tournament-bracket--rounded">
                <div class="tournament-bracket__round tournament-bracket__round--quarterfinals">
                    <div
                        class="tournament-bracket__list tournament-bracket__joined-list tournament-bracket__joined-odd-list">
                        @foreach ($bracketList['lowerBracket']['eliminator1'] as $bracket)
                            <x-brackets.bracket-first-lower-item :bracket="$bracket"
                                :wire:key="'lowerBracket'. 'eliminator1'. $loop->index" />
                        @endforeach
                    </div>
                </div>

                <div class="tournament-bracket__round tournament-bracket__round--semifinals">
                    <div
                        class="tournament-bracket__list tournament-bracket__joined-list tournament-bracket__joined-even-list">
                        @foreach ($bracketList['lowerBracket']['eliminator2'] as $bracket)
                            <x-brackets.bracket-middle-item :bracket="$bracket"
                                :wire:key="'lowerBracket'. 'eliminator2'. $loop->index" />
                        @endforeach
                    </div>
                </div>

                @if (isset($bracketList['lowerBracket']['eliminator3']))
                    <div class="tournament-bracket__round tournament-bracket__round--semifinals">
                        <div
                            class="tournament-bracket__list tournament-bracket__joined-list tournament-bracket__joined-odd-list">
                            @foreach ($bracketList['lowerBracket']['eliminator3'] as $bracket)
                                <x-brackets.bracket-middle-item :bracket="$bracket"
                                    :wire:key="$loop->index. 'lowerBracket'. 'eliminator3'" />
                            @endforeach
                        </div>
                    </div>
                @endif

                @if (isset($bracketList['lowerBracket']['eliminator4']))
                    <div class="tournament-bracket__round tournament-bracket__round--semifinals">
                        <div
                            class="tournament-bracket__list tournament-bracket__joined-list tournament-bracket__joined-even-list">
                            @foreach ($bracketList['lowerBracket']['eliminator4'] as $bracket)
                                <x-brackets.bracket-middle-item :bracket="$bracket"
                                    :wire:key="$loop->index. 'lowerBracket'. 'eliminator4'" />
                            @endforeach
                        </div>
                    </div>
                @endif

                @if (isset($bracketList['lowerBracket']['eliminator5']))
                    <div class="tournament-bracket__round tournament-bracket__round--semifinals">
                        <div
                            class="tournament-bracket__list tournament-bracket__joined-list tournament-bracket__joined-odd-list">
                            @foreach ($bracketList['lowerBracket']['eliminator5'] as $bracket)
                                <x-brackets.bracket-middle-item :bracket="$bracket"
                                    :wire:key="$loop->index. 'lowerBracket'. 'eliminator5'" />
                            @endforeach
                        </div>
                    </div>
                @endif

                @if (isset($bracketList['lowerBracket']['eliminator6']))
                    <div class="tournament-bracket__round tournament-bracket__round--semifinals">
                        <div
                            class="tournament-bracket__list tournament-bracket__joined-list tournament-bracket__joined-even-list">
                            @foreach ($bracketList['lowerBracket']['eliminator6'] as $bracket)
                                <x-brackets.bracket-middle-item :bracket="$bracket"
                                    :wire:key="$loop->index. 'lowerBracket'. 'eliminator6'" />
                            @endforeach
                        </div>
                    </div>
                @endif


                <div class="tournament-bracket__round tournament-bracket__round--semifinals">
                    <div
                        class="tournament-bracket__list tournament-bracket__joined-list tournament-bracket__joined-odd-list">
                        @foreach ($bracketList['lowerBracket']['prefinals1'] as $bracket)
                            <x-brackets.bracket-middle-item :bracket="$bracket" />
                        @endforeach
                    </div>
                </div>


                <div class="tournament-bracket__round tournament-bracket__round--semifinals">
                    <div
                        class="tournament-bracket__list tournament-bracket__joined-list tournament-bracket__joined-even-list">
                        @foreach ($bracketList['lowerBracket']['prefinals2'] as $bracket)
                            <x-brackets.bracket-middle-item :bracket="$bracket" />
                        @endforeach
                    </div>
                </div>

                <div class="tournament-bracket__round tournament-bracket__round--gold">

                </div>
            </div>
            <br><br><br>
    </main>
    
    <script>
        var bracketItemList = document.querySelectorAll('.codeCANcode.tournament-bracket__item');
        bracketItemList.forEach(item => {
            item.classList.add('special-item-right');
        });


        var bracketteamList = document.querySelectorAll('.codeCANcode.tournament-bracket__match');
        bracketItemList.forEach(item => {
            console.log({
                hi: true
            });
            item.classList.add('special-item2');
            item.style.setProperty('--border-color', 'red');
        });

        var bracketBoxList = document.querySelectorAll('.codeCANcode .tournament-bracket__box.codeCANcode');
        bracketBoxList.forEach(item => {
            console.log({
                hi: true
            });
            item.style.setProperty('--border2-color', 'red');
        });

        function getParentByClassName(element, targetClassName) {
            let parent = element.parentElement;

            while (parent && !parent.classList.contains(targetClassName)) {
                parent = parent.parentElement;
            }

            return parent;
        }

        function fillModalInputs(event) {
            event.stopPropagation();
            
            const button = event.currentTarget;
            let parentWithDataset = getParentByClassName(button, "tournament-bracket__displayLargeScreen");
        
            console.log({
                parentWithDataset, button
            });

            const dataset = parentWithDataset.dataset;
            const modalElement = document.getElementById('firstMatchModal');

            const inputs = modalElement.querySelectorAll('input, select, textarea');

            inputs.forEach(input => {
                const inputName = input.getAttribute('name');
                console.log({dataset, inputName, value: dataset[inputName]});
                if (dataset[inputName] !== undefined) {
                    input.value = dataset[inputName];
                }
            });


            console.log({inputs});

            let modal = bootstrap.Modal.getInstance(modalElement);

            if (modal) {
                modal.show();
            } else {
                modal = new bootstrap.Modal(modalElement);
                modal.show();
            };
        }

    </script>
@endsection
