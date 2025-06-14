@foreach ($eventList as $event)
    
    @php
        $status = $event->statusResolved();
        $stylesEventRatio = bldRtMap($event->join_events_count, $event->tierTeamSlot);
        $tier = $event->tier ? $event->tier?->eventTier : null;
        $isEarly = false;
        $entryFee = 0;
        $regStatus = $event->getRegistrationStatus();
        if ($event?->tier) {
            $eventTierLower = bldLowerTIer($tier);
            $icon = $event->tier->tierIcon;
            $entryFee = $event->tier->tierEntryFee;
            if ($regStatus == config('constants.SIGNUP_STATUS.EARLY')) {
                $entryFee = $event->tier->earlyEntryFee ;
                $isEarly = true;
            }
        
        } else {
            $eventTierLower = $icon = null;
            $entryFee = 0;
        }
        
        $eventTierLowerImg = $icon ? asset('storage/'.$icon) : asset('assets/images/404q.png');
        $eventBannerImg = bldImg($event->eventBanner);
        $bladeEventGameImage = $event->game && $event->game->gameIcon ? asset('/' . 'storage/'. $event->game->gameIcon) : asset('assets/images/404q.png');
        
        $willShowStartsInCountDown = $status === 'UPCOMING';
        $isEnded = $status === 'ENDED';
        extract($event->startDatesReadable($willShowStartsInCountDown));
       
    @endphp

    <div class="{{ 'rounded-box event-box rounded-box-' . $eventTierLower. ' ' . $event->id }}  " style="margin-bottom: 2.15rem;">
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
        <div class="{{ 'card-image card-image-' . $eventTierLower }}">
            <img width="100%" height="auto" onerror="this.src='/assets/images/404.png';" src="{{ $eventBannerImg }}" alt="">
        </div>
        <div class="card-text">
            <div>
                <div class="flexbox-centered-space flex-wrap-height-at-mobile">
                    <div>
                    <img 
                        src="{{ $eventTierLowerImg }}" 

                        class="me-3 mt-2  rounded rounded-3  "
                        width="50"
                        height="40"
                        alt="{{$event?->tier?->eventTier}}"
                        onerror="this.src='/assets/images/404q.png';"
                    >
                    <img 
                        src="{{ $bladeEventGameImage }}" 
                        class="logo2 mt-2 rounded rounded-3  object-fit-cover gameIcon" 
                        alt="{{$event?->tier?->eventTier}}"
                        onerror="this.src='/assets/images/404q.png';"
             
                    >
                </div>
                <button class="btn rounded-pill mt-2" style="@php echo $stylesEventRatio; @endphp">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-person me-2" viewBox="0 0 16 16">
                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
                    </svg>
                    <span>{{ $event->join_events_count }}/{{ $event->tier?->tierTeamSlot ?? '0' }}</span>
                </button>
            </div>
            <div class="league_name mt-4 mb-2">
                <p 
                    class="{{ 'text-truncate w-100  ms-0 mb-2 p-0 ' . 'Color-' . $event->tier?->eventTier }}">{{ $event->eventName ?? 'Choose a name' }}</p>
                <div class="small d-inline-flex  px-0 ms-0 pb-2 text-truncate w-100" >
                    <div class="  px-0 text-start text-ellipsis me-2" style="max-width: 50%;">{{ $event->user->name  }}</div>
                    <div class="px-0 text-start ">
                        <span class="ms-1 me-1 d-inline">
                            <svg width="5" height="5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 4 4">
                            <circle cx="2" cy="2" r="2" fill="currentColor"/>
                            </svg>
                        </span>
                        <span>{{ $followersCount }} followers</span>
                    </div>
                </div>
            </div>

            <div class="">
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
                     <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-trophy " viewBox="0 0 16 16">
                        <path d="M2.5.5A.5.5 0 0 1 3 0h10a.5.5 0 0 1 .5.5q0 .807-.034 1.536a3 3 0 1 1-1.133 5.89c-.79 1.865-1.878 2.777-2.833 3.011v2.173l1.425.356c.194.048.377.135.537.255L13.3 15.1a.5.5 0 0 1-.3.9H3a.5.5 0 0 1-.3-.9l1.838-1.379c.16-.12.343-.207.537-.255L6.5 13.11v-2.173c-.955-.234-2.043-1.146-2.833-3.012a3 3 0 1 1-1.132-5.89A33 33 0 0 1 2.5.5m.099 2.54a2 2 0 0 0 .72 3.935c-.333-1.05-.588-2.346-.72-3.935m10.083 3.935a2 2 0 0 0 .72-3.935c-.133 1.59-.388 2.885-.72 3.935M3.504 1q.01.775.056 1.469c.13 2.028.457 3.546.87 4.667C5.294 9.48 6.484 10 7 10a.5.5 0 0 1 .5.5v2.61a1 1 0 0 1-.757.97l-1.426.356a.5.5 0 0 0-.179.085L4.5 15h7l-.638-.479a.5.5 0 0 0-.18-.085l-1.425-.356a1 1 0 0 1-.757-.97V10.5A.5.5 0 0 1 9 10c.516 0 1.706-.52 2.57-2.864.413-1.12.74-2.64.87-4.667q.045-.694.056-1.469z"/>
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
                        stroke-linejoin="round" class="feather feather-user">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    &nbsp;
                    @if ($event->tier) 
                        @if ($isEarly )
                            <div class="d-inline cursor-pointer" data-bs-toggle="tooltip" title="Early Bird Discount! Ends {{ 
                                date('d M y g:i A', strtotime($event->signup->normal_signup_start_advanced_close) + (8 * 3600)) 
                            }}.">
                            <span class="text-decoration-line-through me-1"> RM {{ $event->tier->tierEntryFee }}</span>
                            <span class="text-primary has-discount fw-bold">RM {{ $entryFee }}</span>
                            <span >
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-question-diamond" viewBox="0 0 16 16">
                                    <path d="M6.95.435c.58-.58 1.52-.58 2.1 0l6.515 6.516c.58.58.58 1.519 0 2.098L9.05 15.565c-.58.58-1.519.58-2.098 0L.435 9.05a1.48 1.48 0 0 1 0-2.098zm1.4.7a.495.495 0 0 0-.7 0L1.134 7.65a.495.495 0 0 0 0 .7l6.516 6.516a.495.495 0 0 0 .7 0l6.516-6.516a.495.495 0 0 0 0-.7L8.35 1.134z"/>
                                    <path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286m1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94"/>
                                </svg>
                            </span>
                            </div>                        
                        @else 
                            <span>RM {{ $entryFee }} </span>
                        @endif
                    @else
                        <span>RM 0</span>
                        
                    @endif
                </div>
                <div>
                    <h5 @class([
                        'py-0 my-0 mt-3 d-flex justify-content-center Color-' . $event->tier?->eventTier,
                        ' text-secondary' => $isEnded
                    ])>
                        <span> {{$fmtStartDt}} </span>
                        <span> <span class="ms-3 me-2"><svg width="5" height="5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 4 4">
                        <circle cx="2" cy="2" r="2" fill="currentColor"/>
                        </svg> </span>{{$fmtStartT}} </span>
                    </h5>
                    @if ($willShowStartsInCountDown) 
                        @if ($fmtStartIn)
                            <div class="text-center">
                                <p class="my-0 py-0"> Starts in 
                                    <span class="{{ ' Color-' . $event->tier?->eventTier }}">{{$fmtStartIn}}</span>
                                </p>
                            </div>
                        @endif
                    @else
                        <div class="text-center">
                            <p class="my-0 py-0"> 
                                <span class="{{ ' Color-' . $event->tier?->eventTier }}">No start time</span>
                            </p>
                        </div>
                    @endif
                </div>
            </div>
               
                <div style="font-size: 0.9375rem;">
                    <div class="d-flex justify-content-around popover-parent">
                        @if (!in_array($status, ['PENDING', 'DRAFT'])) 
                            @if ($status != "ENDED")
                                <a class="m-0 btn my-1 px-3  py-1 btn-link" href="{{ route('event.invitation.index', $event->id) }}">
                                    <span> <u> Invite </u> </span>
                                </a>
                            @endif    
                            <div class="popover-content2 d-none" style="z-index: 999 !important;">
                                <div class="popover-box py-2 px-1" style="z-index: 999 !important;">
                                    <a class="px-2 py-1 text-light me-3 btn btn-primary d-inline"  href="{{ route('event.matches.index', ['id' => $event->id]) }}">
                                        <small> Matches </small>
                                    </a>
                                
                                    <a class="px-2 py-1 text-light btn btn-primary d-inline" href="{{ route('event.awards.index', ['id' => $event->id ]) }}">
                                        <small> Awards </small>
                                    </a>
                                    
                                </div>
                            </div>
                            <button class="popover-button   py-1 my-1 btn btn-link">
                                <u> Results </u>
                            </button>
                        @else
                            <br>
                        @endif
                    </div>
                </div>
                
            </div>
            <div class="group-hover flex-wrap d-flex justify-content-center cursor-pointer ">
                   
                    @if (in_array($status, ['UPCOMING', 'DRAFT', 'SCHEDULED']))
                        <a class="m-0 mb-2 mx-4 p-0" href="{{ route('event.show', $event->id) }}">
                            <svg onclick="goToLivePreview()" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                            <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                            </svg>
                        </a>
                        <a 
                            data-event-id="{{$event->id}}"
                            class="share-button m-0 mb-1 mx-4 p-0" 
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-link-45deg" viewBox="0 0 16 16">
                                <path d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1 1 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4 4 0 0 1-.128-1.287z"/>
                                <path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 1 0-4.243-4.243z"/>
                            </svg>
                        </a>
                        @if ($status !== 'ONGOING')
                            <a class="m-0 mb-2 mx-4 p-0"  href="{{ route('event.edit', $event->id) }}">
                                <svg onclick="goToEditScreen()" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 16 16">
                                <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z"/>
                                </svg>
                            </a>
                        @endif
                    @elseif ($status === "PENDING")
                        <a class="m-0 mx-4 mb-2 p-0"  href="{{ route('organizer.checkout.view', $event->id) }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-cart-check" viewBox="0 0 16 16">
                            <path d="M11.354 6.354a.5.5 0 0 0-.708-.708L8 8.293 6.854 7.146a.5.5 0 1 0-.708.708l1.5 1.5a.5.5 0 0 0 .708 0z"/>
                            <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1zm3.915 10L3.102 4h10.796l-1.313 7zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
                            </svg>
                        </a>
                        <a class="m-0 mx-4 mb-2 p-0"  href="{{ route('event.edit', $event->id) }}">
                            <svg onclick="goToEditScreen()" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 16 16">
                            <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z"/>
                            </svg>
                        </a>
                    @endif
                     @if (in_array($status, ['UPCOMING', 'ONGOING', 'SCHEDULED']))
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-circle mb-2 mx-4" viewBox="0 0 16 16"
                            onclick="cancelEvent(event);"    data-url="{{route('event.destroy.action', ['id' => $event->id])}}"
                        >
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                        </svg>
                    @endif
                </div>

            
        </div>
    </div>
    
@endforeach
