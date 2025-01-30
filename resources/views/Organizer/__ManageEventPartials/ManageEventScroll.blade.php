@foreach ($eventList as $event)
    
    @php
        $status = $event->statusResolved();
        $stylesEventRatio = bladeEventRatioStyleMapping($event->join_events_count, $event->tierTeamSlot);
        $tier = $event->tier ? $event->tier?->eventTier : null;
        $eventTierLower = bladeEventTowerLowerClass($tier);
        
        $dateStartArray = bladeGenerateEventStartEndDateStr($event->startDate, $event->startTime);
        $dateEndArray = bladeGenerateEventStartEndDateStr($event->endDate, $event->endTime);
        $datePublishedArray = bladeGenerateEventStartEndDateStr($event->sub_action_public_date, $event->sub_action_public_time);
        extract($dateStartArray);

        $eventTierLowerImg = bladeEventTierImage($tier);
        $eventBannerImg = bladeImageNull($event->eventBanner);
        $bladeEventGameImage = bladeImageNull($event->game ? $event->game?->gameIcon : null);
        
        $eventId = $event->id;
        $toolTip = '<b>Event ID: </b>' . $eventId . '<br>';
        $toolTip .= '<b>Description: </b>' . $event->eventDescription . '<br>';
        $toolTip .= '<b>Start: </b>' . $dateStartArray['timePart'] . ' on ' . $dateStartArray['combinedStr'] . '<br>';
        $toolTip .= '<b>End: </b>' . $dateEndArray['timePart'] . ' on ' . $dateEndArray['combinedStr'] . '<br>';
        $toolTip .= '<b>Visibilty: </b>' . $event->sub_action_private . '<br>' ;
        $toolTip .= '<b>Published date: </b>' . $datePublishedArray['timePart'] . ' on ' . $datePublishedArray['combinedStr'] ;    
    @endphp

    <div class="{{ 'rounded-box event-box rounded-box-' . $eventTierLower. ' ' . $eventId }}  " style="margin-bottom: 2.15rem;">
        <div class="centered-absolute-game-tier">
            <img src="{{ $eventTierLowerImg }}" width="70" height="70" class="object-fit-cover">
        </div>
        <div class="{{ 'card-image card-image-' . $eventTierLower }}">
            <img width="100%" height="auto" {!! trustedBladeHandleImageFailure() !!} src="{{ $eventBannerImg }}" alt="">
        </div>
        <div class="card-text">
            <div>
                <div class="flexbox-centered-space flex-wrap-height-at-mobile">
                    <img src="{{ $bladeEventGameImage }}" 
                        onerror="this.onerror=null;this.src='{{asset('assets/images/404.png')}}';"
                        alt="menu" width="40" height="40"
                        class="object-fit-cover"
                    >
                    <button data-bs-toggle="tooltip" data-bs-html="true" title="{{ $toolTip }}"
                        class="{{ 'activate-tooltip px-3 py-2 rounded-pill '. 'EventStatus-' .  $status }}"
                        style="padding-top: -150px; text-align: left; font-size: 0.875rem;">
                        <u> {{ $status }} </u>
                    </button>
                    <button style="@php echo $stylesEventRatio; @endphp"
                        class="px-2 oceans-gaming-default-button oceans-gaming-default-button-small flexbox-centered-space"
                        style="font-size: 0.875rem;"
                        >
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="feather mt-1 feather-user">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                      
                        @if ($event->tier)
                            @foreach ($eventList as $index => $eventDetail)
                                @if ($index === 0)
                                    <span>
                                        {{ $event->join_events_count }}/{{ $event->tier?->tierTeamSlot ?? 'Not Available' }}
                                    </span>
                                @endif
                            @endforeach
                        @else
                            <span>Missing</span>
                        @endif
                    </button>
                </div>
                <br>
                <p class="card-text-2-lines m-0">
                    <u>{{ $event->eventName ?? 'Choose a name' }}</u></p>
                <p class="small-text m-0"><i>
                    {{ $event?->user?->name ?? 'Choose organization name' }}
                </i></p>
                <div class="flexbox-welcome">
                    <div>@php echo $dateStr; @endphp</div>
                </div>
                <div style="font-size: 0.9375rem;">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="feather feather-user">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        &nbsp;
                        @if ($event->tier)
                            <span>RM {{ $event->tier?->tierPrizePool ?? 'No Prize' }} Prize Pool</span>
                        @else
                            <span>Select event tier</span>
                        @endif
                    </div>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="feather feather-dollar-sign">
                            <line x1="12" y1="1" x2="12" y2="23"></line>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                        &nbsp;
                        @if ($event->tier)
                            <span>RM {{ $event->tier?->tierEntryFee ?? 'Free' }} Entry Fees</span>
                        @else
                            <span>Entry fee not available</span>
                        @endif
                    </div>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="feather feather-map-pin">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        &nbsp;
                        <span>{{ $event->venue ?? 'South East Asia' }}</span>
                    </div>
                    <div>
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
                    <div class="d-flex justify-content-around popover-parent">
                        @if (!in_array($status, ['PENDING', 'DRAFT'])) 
                            @if ($status != "ENDED")
                                <a class="m-0 btn mt-2 mb-2 px-3 py-1 btn-link" href="{{ route('event.invitation.index', $event->id) }}">
                                    <span> <u> Invite </u> </span>
                                </a>
                        @endif    
                        <div class="popover-content2 d-none" style="z-index: 999 !important;">
                            <div class="popover-box py-2 px-1" style="z-index: 999 !important;">
                                <a class="px-2 py-1 text-light me-3 btn btn-primary d-inline"  href="{{ route('event.matches.index', ['id' => $event->id, 'eventType'=> $event->type->eventType]) }}">
                                    <small> Matches </small>
                                </a>
                               
                                <a class="px-2 py-1 text-light btn btn-primary d-inline" href="{{ route('event.awards.index', ['id' => $event->id ]) }}">
                                    <small> Awards </small>
                                </a>
                                
                            </div>
                        </div>
                        <button class="popover-button   mt-2  mb-2 btn btn-link">
                            <u> Results </u>
                        </button>
                        @else
                            <div class="d-flex justify-content-center align-items-center my-2 py-2">
                                <span> <i>Event is now {{strtolower($status)}}. </i> </span>
                            </div>
                        @endif
                    </div>
                </div>
                
            </div>
            <div class="group-hover flex-wrap d-flex justify-content-center cursor-pointer mb-2">
                   
                    @if (in_array($status, ['ONGOING', 'DRAFT', 'SCHEDULED']))
                        <a class="m-0 mx-4 p-0" href="{{ route('event.show', $event->id) }}">
                            <svg onclick="goToLivePreview()" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                            <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                            </svg>
                        </a>
                        <a class="m-0 mx-4 p-0" >
                            <button 
                                class="m-0 p-0" 
                                style="background-color: transparent; outline: none; border: none;"
                                type="button" data-bs-toggle="modal" data-bs-target="#shareModal">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-graph-up-arrow" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M0 0h1v15h15v1H0zm10 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V4.9l-3.613 4.417a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61L13.445 4H10.5a.5.5 0 0 1-.5-.5"/>
                                </svg>
                            </button>
                        </a>
                        <a class="m-0 mx-4 p-0" >
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-share-fill" viewBox="0 0 16 16">
                            <path d="M11 2.5a2.5 2.5 0 1 1 .603 1.628l-6.718 3.12a2.5 2.5 0 0 1 0 1.504l6.718 3.12a2.5 2.5 0 1 1-.488.876l-6.718-3.12a2.5 2.5 0 1 1 0-3.256l6.718-3.12A2.5 2.5 0 0 1 11 2.5"/>
                            </svg>
                        </a>
                        @if ($status !== 'ONGOING')
                            <a class="m-0 mx-4 p-0"  href="{{ route('event.edit', $event->id) }}">
                                <svg onclick="goToEditScreen()" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 16 16">
                                <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z"/>
                                </svg>
                            </a>
                        @endif
                    @elseif ($status === "PENDING")
                        <a class="m-0 mx-4 p-0"  href="{{ route('organizer.checkout.view', $event->id) }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-cart-check" viewBox="0 0 16 16">
                            <path d="M11.354 6.354a.5.5 0 0 0-.708-.708L8 8.293 6.854 7.146a.5.5 0 1 0-.708.708l1.5 1.5a.5.5 0 0 0 .708 0z"/>
                            <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1zm3.915 10L3.102 4h10.796l-1.313 7zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
                            </svg>
                        </a>
                        <a class="m-0 mx-4 p-0"  href="{{ route('event.edit', $event->id) }}">
                            <svg onclick="goToEditScreen()" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 16 16">
                            <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z"/>
                            </svg>
                        </a>
                    @endif
                     @if (in_array($status, ['UPCOMING', 'ONGOING', 'SCHEDULED']))
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-circle mx-4" viewBox="0 0 16 16"
                            onclick="cancelEvent(event);"    data-url="{{route('event.destroy.action', ['id' => $eventId])}}"
                        >
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                        </svg>
                    @endif
                </div>

            
        </div>
    </div>
    
@endforeach
