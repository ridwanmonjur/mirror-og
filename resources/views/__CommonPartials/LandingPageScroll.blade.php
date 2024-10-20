@foreach ($events as $event)
    @php
        $status = $event->statusResolved();
        $eventTierLowerImg = bladeEventTierImage($event->tier ? $event->tier?->eventTier: null);
        $eventBannerImg = bladeImageNull($event->eventBanner);
        $bladeEventGameImage = bladeImageNull($event->game ? $event->game?->gameIcon : null);
        $stylesEventRatio = bladeEventRatioStyleMapping($event->join_events_count, $event->tierTeamSlot);
        $willShowStartsInCountDown = $status === 'ONGOING';
        $isEnded = $status === 'ENDED';
        extract($event->startDatesReadableForLanding($willShowStartsInCountDown));
    @endphp
    <div class="{{'rounded-box-' . strtoLower($event->tier?->eventTier) . ' event' }}" 
        style="background-color: rgba(255, 255, 255, 0.6);"
    >
        <a class="d-block" href="/event/{{ $event['id'] }}">
            <div style="display: flex; justify-content: center;">
                <button  
                    @class([
                        " rounded-pill mt-2 py-2 px-4",
                        'EventStatus-' .  $status . '-HOME'
                    ])  
                    style="padding-top: -150px; position: absolute !important; top: -35px !important; z-index: 111; "
                >
                   
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white" class="d-none me-3 bi bi-stopwatch UPCOMING" viewBox="0 0 16 16">
                    <path d="M8.5 5.6a.5.5 0 1 0-1 0v2.9h-3a.5.5 0 0 0 0 1H8a.5.5 0 0 0 .5-.5z"/>
                    <path d="M6.5 1A.5.5 0 0 1 7 .5h2a.5.5 0 0 1 0 1v.57c1.36.196 2.594.78 3.584 1.64l.012-.013.354-.354-.354-.353a.5.5 0 0 1 .707-.708l1.414 1.415a.5.5 0 1 1-.707.707l-.353-.354-.354.354-.013.012A7 7 0 1 1 7 2.071V1.5a.5.5 0 0 1-.5-.5M8 3a6 6 0 1 0 .001 12A6 6 0 0 0 8 3"/>
                    
                    </svg>

                     <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" class="d-none me-3 ONGOING" fill="white"
                        class="bi bi-broadcast" viewBox="0 0 16 16">
                        <path
                            d="M3.05 3.05a7 7 0 0 0 0 9.9.5.5 0 0 1-.707.707 8 8 0 0 1 0-11.314.5.5 0 0 1 .707.707m2.122 2.122a4 4 0 0 0 0 5.656.5.5 0 1 1-.708.708 5 5 0 0 1 0-7.072.5.5 0 0 1 .708.708m5.656-.708a.5.5 0 0 1 .708 0 5 5 0 0 1 0 7.072.5.5 0 1 1-.708-.708 4 4 0 0 0 0-5.656.5.5 0 0 1 0-.708m2.122-2.12a.5.5 0 0 1 .707 0 8 8 0 0 1 0 11.313.5.5 0 0 1-.707-.707 7 7 0 0 0 0-9.9.5.5 0 0 1 0-.707zM10 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0" />
                    </svg>
                   
                 
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white" 
                        class="bi bi-flag d-none me-3 ENDED" viewBox="0 0 16 16"
                    >
                    <path d="M14.778.085A.5.5 0 0 1 15 .5V8a.5.5 0 0 1-.314.464L14.5 8l.186.464-.003.001-.006.003-.023.009a12 12 0 0 1-.397.15c-.264.095-.631.223-1.047.35-.816.252-1.879.523-2.71.523-.847 0-1.548-.28-2.158-.525l-.028-.01C7.68 8.71 7.14 8.5 6.5 8.5c-.7 0-1.638.23-2.437.477A20 20 0 0 0 3 9.342V15.5a.5.5 0 0 1-1 0V.5a.5.5 0 0 1 1 0v.282c.226-.079.496-.17.79-.26C4.606.272 5.67 0 6.5 0c.84 0 1.524.277 2.121.519l.043.018C9.286.788 9.828 1 10.5 1c.7 0 1.638-.23 2.437-.477a20 20 0 0 0 1.349-.476l.019-.007.004-.002h.001M14 1.221c-.22.078-.48.167-.766.255-.81.252-1.872.523-2.734.523-.886 0-1.592-.286-2.203-.534l-.008-.003C7.662 1.21 7.139 1 6.5 1c-.669 0-1.606.229-2.415.478A21 21 0 0 0 3 1.845v6.433c.22-.078.48-.167.766-.255C4.576 7.77 5.638 7.5 6.5 7.5c.847 0 1.548.28 2.158.525l.028.01C9.32 8.29 9.86 8.5 10.5 8.5c.668 0 1.606-.229 2.415-.478A21 21 0 0 0 14 7.655V1.222z"/>
                    </svg>
                    <span>{{ $status }}</span>
                </button>
            </div>
            <img src="{{ $eventBannerImg }}" {!! trustedBladeHandleImageFailure() !!} class="cover" style="min-height: 150px !important; ">
            <div class="frame1 d-flex justify-content-between flex-wrap px-3">
                <div>
                    <img 
                        src="{{ $eventTierLowerImg }}" 
                        class="pe-3 tierIcon mt-2"
                        alt=""
                    >
                    <img 
                        src="{{ $bladeEventGameImage }}" 
                        class="logo2 mt-2 object-fit-cover gameIcon" 
                        alt=""
                    >
                </div>
                <button class="btn rounded-pill mt-2" style="@php echo $stylesEventRatio; @endphp">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-person me-2" viewBox="0 0 16 16">
                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
                    </svg>
                    <span>{{ $event->join_events_count }}/{{ $event->tier?->tierTeamSlot ?? 'Not Available' }}</span>
                </button>
            </div>
            <div class="league_name mt-4 mb-2">
                <p class="{{ 'ms-0 mb-0 p-0 ' . 'Color-' . $event->tier->eventTier }}"><b>{{ $event->eventName }}</b></p>
                <small class=" px-0 ms-0 pb-2 fw-lighter">
                    <span class="px-0 text-start">
                        <i class="d-inline">{{ $event->user->organizer->companyName  }}</i>
                    </span>
                    <span class="px-0 text-start d-block d-lg-inline">
                        <i class="ms-1 me-1 d-inline">▪️</i>
                        <i>{{ $event->user->follows_count }} followers</i>
                    </span>
                </small>
            </div>
            <div class="ms-3 fs-7">
                <div class="mb-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="feather feather-info">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    &nbsp;
                    <span>{{ $event->type?->eventType ?? 'Choose a type' }}</span>
                </div>
                <div class="mb-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="feather feather-user">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    &nbsp;
                    @if ($event->tier)
                        <span>RM {{ $event->tier?->tierPrizePool ?? 'No Prize' }}</span>
                    @else
                        <span>Select event tier</span>
                    @endif
                </div>
                <div class="mb-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="feather feather-dollar-sign">
                        <line x1="12" y1="1" x2="12" y2="23"></line>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                    &nbsp;
                    @if ($event->tier)
                        <span>RM {{ $event->tier?->tierEntryFee ?? 'Free' }} </span>
                    @else
                        <span>Entry fee not available</span>
                    @endif
                </div>
                <div>
                    <h5 @class([
                        'py-0 my-0 mt-3 d-flex justify-content-center Color-' . $event->tier->eventTier,
                        ' text-secondary' => $isEnded
                    ])>
                        <span> {{$formattedStartDate}} </span>
                        <span> <span class="ms-3 me-2">▪️ </span>{{$formattedStartTime}} </span>
                    </h5>
                    @if ($willShowStartsInCountDown) 
                        <div class="text-center">
                            <p class="my-0 py-0"> Starts in 
                                <span class="{{ ' Color-' . $event->tier->eventTier }}">{{$formmattedStartsIn}}</span>
                            </p>
                        </div>
                    @else
                        <div class="text-center mt-1">
                            <button class="btn btn-small py-1 px-2 border-primary text-primary"> See bracket</button>
                        </div>
                    @endif
                </div>
            </div>
        </a>
    </div>
@endforeach
