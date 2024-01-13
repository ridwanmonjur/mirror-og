@include('Organizer.Layout.ViewEventHeadTag')

@php

    $status = $event->statusResolved();
    $stylesEventStatus = bladeEventStatusStyleMapping($status);
    $stylesEventRatio = bladeEventRatioStyleMapping($event->registeredParticipants, $event->totalParticipants);
    $eventTierLower = bladeEventTowerLowerClass($event->eventTier);
    $dateArray = bladeGenerateEventStartEndDateStr($event->startDate, $event->startTime);
    extract($dateArray);
    $eventTierLowerImg = bladeEventTierImage($event->eventTier);
    $eventBannerImg = bladeImageNull($event->eventBanner);

@endphp

<body>
    @include('CommonLayout.NavbarGoToSearchPage')

    <main>
        <br class="d-none-at-desktop">
        <div class="">
            <header class="flexbox-welcome">
                <u>
                    <h3>
                        View your events
                    </h3>
                </u>
            </header>
        </div>
        <br><br>
        <div class="grid-container">
            @if ($event->eventTier)
                <div class="{{ 'side-image side-image-' . $eventTierLower }}">
                    <img class="side-image-absolute-bottom" src="{{ $eventTierLowerImg }}" width="180" height="125">
                </div>
            @else
                <div></div>
            @endif
            <div>
                <div style="padding-left: 20px;padding-right:20px;">
                    <div class="mx-2 position-relative">

                        <div class="d-flex justify-content-center d-lg-none">
                            <img class="image-at-top" {!! trustedBladeHandleImageFailureResize() !!} src="{{ $eventTierLowerImg }}" width="75" height="65">
                        </div>
                        <img width="100%" height="auto" style="aspect-ratio: 7/3; object-fit: cover; margin: auto;"
                            @class(['rounded-banner', 'rounded-box-' . $eventTierLower]) {!! trustedBladeHandleImageFailureBanner() !!} src="{{ $eventBannerImg }}"
                            alt="">
                        @if ($event->eventBanner)
                        @else
                            <h5>
                                Please enter a banner image.
                            </h5>
                            <br><br>
                        @endif
                    </div>
                    <div class="grid-container-two-columns-at-desktop">
                        <div class="card-text">
                            <div>
                                <br>
                                <div class="flexbox-centered-space" style="align-items: center;">
                                    <p
                                        style="height:60px;text-overflow:ellipsis; overflow:hidden;font-size:20px;margin-right:60px;margin-bottom:20px">
                                        <u>{{ $event->eventName ?? 'No name yet' }}</u>
                                    </p>
                                    <svg style="position: relative; top: -20px; margin-left: -60px;"
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="feather feather-share-2">
                                        <circle cx="18" cy="5" r="3"></circle>
                                        <circle cx="6" cy="12" r="3"></circle>
                                        <circle cx="18" cy="19" r="3"></circle>
                                        <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                                        <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                                    </svg>
                                </div>
                                <div class="flexbox-centered-space card-subtitle">
                                    <div class="flexbox-centered-space">
                                        <img style="display: inline;" src="{{ asset('/assets/images/menu.png') }}"
                                            class="{{ 'rounded-image rounded-box-' . $eventTierLower }}" alt="menu">
                                        &nbsp;
                                        <div class="card-organizer">
                                            <p style="display: inline;"><u>
                                                    {{ $event->user->organizer->companyName ?? 'Add' }} </u> </p>
                                            <p class="small-text"> <i> 104 followers </i> </p>
                                        </div>
                                    </div>
                                    @if ($livePreview)
                                        <input type="submit" style="background-color: #8CCD39;"
                                            value="Resume creating...." onclick="goToEditScreen();">
                                    @endif
                                </div>
                                <br>
                                <h4> <u> {{ $combinedStr }} </u> </h4>
                                <h4> <u> {{ strtoupper($timePart) }} </u> </h4>
                                <br>
                                <div>
                                    <div class="tab">
                                        <button class="{{ 'side-image-' . $eventTierLower . ' tablinks active' }}"
                                            onclick="openTab(event, 'Overview')">Overview</button>
                                        <button class="{{ 'side-image-' . $eventTierLower . ' tablinks' }}"
                                            onclick="openTab(event, 'Bracket')">Bracket</button>
                                        <button class="{{ 'side-image-' . $eventTierLower . ' tablinks' }}"
                                            onclick="openTab(event, 'Teams')">Teams</button>
                                        <button class="{{ 'side-image-' . $eventTierLower . ' tablinks' }}"
                                            onclick="openTab(event, 'Result')">Result</button>
                                    </div>
                                    <br>
                                    <div id="Overview" class="tabcontent" style="display: block;">
                                        <h3><u>About this event</u></h3>
                                        <p>{{ $event->eventDescription ?? 'Not added description yet' }} </p>
                                    </div>

                                    <div id="Bracket" class="tabcontent">
                                        <h3><u>Bracket</u></h3>
                                        <p>Bracket is the capital of France.</p>
                                    </div>

                                    <div id="Teams" class="tabcontent">
                                        <h3><u>Teams</u></h3>
                                        <p>Teams tab.</p>
                                    </div>
                                    <div id="Result" class="tabcontent">
                                        <h3><u>Result</u></h3>
                                        <p>Result tab.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="pl-3">
                            <br><br>
                            <button class="oceans-gaming-default-button" style="@php echo $stylesEventStatus; @endphp">
                                <u>{{ $status ?? 'Choose event status' }}</u>
                            </button>
                            <br><br>
                            <div>
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="feather feather-user">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                    &nbsp;
                                    @if ($event->tier)
                                        <span style="position: relative; top: 5px;"> RM
                                            {{ $event->tier->tierPrizePool ?? 'No Prize' }} Prize Pool</span>
                                    @else
                                        <span>Select event tier</span>
                                    @endif
                                </div>
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="feather feather-dollar-sign">
                                        <line x1="12" y1="1" x2="12" y2="23"></line>
                                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                    </svg>
                                    &nbsp;
                                    @if ($event->tier)
                                        <span style="position: relative; top: 5px;">RM
                                            {{ $event->tier->tierEntryFee ?? 'Free' }} Entry Fees</span>
                                    @else
                                        <span>Entry fee unavailable</span>
                                    @endif
                                </div>
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="feather feather-map-pin">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                        <circle cx="12" cy="10" r="3"></circle>
                                    </svg>
                                    &nbsp;
                                    <span style="position: relative; top: 5px;">{{ $event->venue ?? 'SEA' }}</span>
                                </div>
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="feather feather-bar-chart-2">
                                        <line x1="18" y1="20" x2="18" y2="10"></line>
                                        <line x1="12" y1="20" x2="12" y2="4"></line>
                                        <line x1="6" y1="20" x2="6" y2="14"></line>
                                    </svg>
                                    &nbsp;
                                    @if ($event->tier)
                                        @foreach ($eventList as $index => $eventDetail)
                                            @if ($index == 0)
                                                <span style="position: relative; top: 5px;">
                                                    {{ $eventDetail->joinEventCount }}/{{ $event->tier->tierTeamSlot ?? 'Not Available' }}
                                                </span>
                                            @endif
                                        @endforeach
                                    @else
                                        <span>N/A</span>
                                    @endif
                                </div>
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="feather feather-info">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="12" y1="16" x2="12" y2="12"></line>
                                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                    </svg>
                                    &nbsp;
                                    <span
                                        style="position: relative; top: 5px;">{{ $event->eventType ?? 'Choose event type' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            @if ($event->eventTier)
                <div class="{{ 'side-image side-image-' . $eventTierLower }} ">
                    <img class="side-image-absolute-top" src="{{ $eventTierLowerImg }}" width="180"
                        height="125">
                </div>
            @else
                <!-- <div>Choose event tier</div> -->
                <div></div>
            @endif
        </div>
        <br>
    </main>
    @stack('script')
    @include('CommonLayout.BootstrapJs')

    <script>
        function goToCreateScreen() {
            let url = "{{ route('event.create') }}";
            window.location.href = url;
        }

        function goToEditScreen() {
            let url = "{{ route('event.edit', $event->id) }}";
            window.location.href = url;
        }
    </script>
    <script src="{{ asset('/assets/js/navbar/toggleNavbar.js') }}"></script>
    <script src="{{ asset('/assets/js/tab/tab.js') }}"></script>
