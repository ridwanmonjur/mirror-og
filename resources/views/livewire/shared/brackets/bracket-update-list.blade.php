@section('content')

    <main>
        @include('__CommonPartials.NavbarGoToSearchPage')
        <div class="px-4">
            @livewire('shared.brackets.bracket-update-modal', [
                'event' => $event,
                'teamList' => $teamList
            ])
            <h5 class="mt-5 mb-4  text-start">Upper bracket</h5>
            <div class="row ">
                <div class="tournament-bracket tournament-bracket--rounded col-lg-9 col-xl-8 col-xxl-6">
                    <div class="tournament-bracket__round tournament-bracket__round--quarterfinals">
                        <div class="tournament-bracket__list">
                            @foreach ($bracketList['upperBracket']['eliminator1'] as $order => $bracket)
                                <x-brackets.bracket-first-item :bracket="$bracket"
                                    :stageName="'upperBracket'"
                                    :innerStageName="'eliminator1'"
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
                            <div class="tournament-bracket__round tournament-bracket__round--{{ $roundClass }}">
                                <div class="tournament-bracket__list">
                                    @foreach ($bracketList['upperBracket'][$stage] as $order => $bracket)
                                        <x-brackets.bracket-middle-item :bracket="$bracket"
                                            :stageName="'upperBracket'"
                                            :innerStageName="$stage"
                                            :order="$order"
                                            :wire:key="'upperBracket'. $stage. $loop->index" />
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach

                    <div class="tournament-bracket__round tournament-bracket__round--gold">
                    </div>
                </div>

                @foreach ($bracketList['finals']['finals'] as $order => $bracket)
                    <x-brackets.bracket-winner-item :bracket="$bracket"
                        :stageName="'finals'"
                        :innerStageName="'finals'"
                        :order="$order"
                        :wire:key="'upperBracket'. 'eliminator1'. $loop->index" />
                @endforeach

            </div>
            <h5 class="mt-5 mb-4 text-start">Lower bracket</h5>
            <div class="tournament-bracket tournament-bracket--rounded">
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
                        <div class="tournament-bracket__round tournament-bracket__round--semifinals">
                            <div class="tournament-bracket__list {{ $listClass }}">
                                @foreach ($bracketList['lowerBracket'][$stage] as $order => $bracket)
                                    <x-brackets.bracket-middle-item :bracket="$bracket"
                                        :stageName="'lowerBracket'" :innerStageName="$stage"
                                        :order="$order"
                                        :wire:key="'lowerBracket'. $stage. $loop->index" />
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach


                <div class="tournament-bracket__round tournament-bracket__round--semifinals">
                    <div
                        class="tournament-bracket__list tournament-bracket__joined-list tournament-bracket__joined-odd-list">
                        @foreach ($bracketList['lowerBracket']['prefinals1'] as $order => $bracket)
                            <x-brackets.bracket-middle-item :bracket="$bracket"  :order="$order"
                                :stageName="'lowerBracket'" :innerStageName="'prefinals1'"
                            />
                        @endforeach
                    </div>
                </div>


                <div class="tournament-bracket__round tournament-bracket__round--semifinals">
                    <div
                        class="tournament-bracket__list tournament-bracket__joined-list tournament-bracket__joined-even-list">
                        @foreach ($bracketList['lowerBracket']['prefinals2'] as $order => $bracket)
                            <x-brackets.bracket-middle-item :bracket="$bracket"  :order="$order"
                                :stageName="'lowerBracket'" :innerStageName="'prefinals2'"
                            />
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
            let parentWithDataset = getParentByClassName(button, "tournament-bracket__match");
            
            let dataset = JSON.parse(parentWithDataset.dataset.bracket);
            const stageName = parentWithDataset.dataset.stage_name;
            const innerStageName = parentWithDataset.dataset.inner_stage_name;
            const order = parentWithDataset.dataset.order;
            dataset.stage_name = stageName;
            dataset.inner_stage_name = innerStageName;
            dataset.order = order;
            const modalElement = document.getElementById('firstMatchModal');

            const inputs = modalElement.querySelectorAll('input, select, textarea');

            inputs.forEach(input => {
                const inputName = input.getAttribute('name');
                if (dataset[inputName] !== undefined) {
                    input.value = dataset[inputName];
                }
            });

            console.log({selectMap, dataset});
            console.log({selectMap, dataset});
            console.log({selectMap, dataset});
            console.log({selectMap, dataset});

            ['result', 'status', 'team1_id', 'team2_id', 'winner_id'].forEach((selectName)=> {
                selectMap[selectName]?.updateSelectElement(dataset[selectName]);
            })

            let modal = bootstrap.Modal.getInstance(modalElement);

            if (modal) {
                modal.show();
            } else {
                modal = new bootstrap.Modal(modalElement);
                modal.show();
            };
        }
        
        window.onload = () => {
            const parentElements = document.querySelectorAll(".popover-parent");
            parentElements.forEach(parent => {
                const contentElement = parent.querySelector(".popover-content");
                const parentElement = parent.querySelector(".popover-button");
                if (contentElement) {
                    window.addPopover(parentElement, contentElement, 'mouseenter');
                }
            });
        };
        
        let selectMap = {};
        document.querySelectorAll('[data-dynamic-select]').forEach(select => {
            selectMap[select.name] = new DynamicSelect(select);
        });

    </script>
@endsection
