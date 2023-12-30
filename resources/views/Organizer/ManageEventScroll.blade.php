@foreach ($eventList as $event)
@php
$status = $event->statusResolved();
$stylesEventStatus = bladeEventStatusStyleMapping($status);
$stylesEventStatus .= 'padding-top: -150px; ';
$stylesEventRatio= bladeEventRatioStyleMapping($event->registeredParticipants, $event->totalParticipants);
$eventTierLower= bladeEventTowerLowerClass($event->eventTier);
$dateStartArray = bladeGenerateEventStartEndDateStr($event->startDate, $event->startTime);
$dateEndArray = bladeGenerateEventStartEndDateStr($event->endDate, $event->endTime);
$datePublishedArray = bladeGenerateEventStartEndDateStr($event->sub_action_public_date, $event->sub_action_public_time);
extract($dateStartArray);
$eventTierLowerImg = bladeEventTierImage($event->eventTier);
$eventBannerImg = bladeImageNull($event->eventBanner);
$bladeEventGameImage = bladeImageNull($event->game->gameIcon);
$eventId = $event->id;
$toolTip= "<div><b>Event ID: </b>" . $eventId . "<br>";
$toolTip.= "<b>Description: </b>" . $event->eventDescription . "<br>";
$toolTip.= "Start: " . $dateStartArray['timePart'] . " on " . $dateStartArray['combinedStr']. "<br>"; 
$toolTip.= "End: ". $dateEndArray['timePart'] . " on " . $dateEndArray['combinedStr'] . "<br>";
$toolTip.= "Published date: ". $datePublishedArray['timePart'] . " on " . $datePublishedArray['combinedStr'] ."</div>";
@endphp
<div class="{{'rounded-box rounded-box-' . $eventTierLower }} " 
    style="padding-bottom: 2px;"
>
    <div class="centered-absolute-game-tier">
        <img src="{{  $eventTierLowerImg }}" width="100" style="object-fit: cover;">
    </div>
    <div class="{{'card-image card-image-' . $eventTierLower }}">
        <img width="300" height="160"  {!! trustedBladeHandleImageFailure() !!} src="{{ $eventBannerImg }}" alt="">
    </div>
    <div class="card-text">
        <div>
            <div class="flexbox-centered-space">
                <img src="{{ $bladeEventGameImage }}" alt="menu" width="50" height="50" style="object-fit: cover; ">
                <button
                        data-toggle="tooltip" 
                    data-html="true" 
                    title="{{ $toolTip }}"
                    class="activate-tooltip oceans-gaming-default-button" style="@php echo $stylesEventStatus; @endphp">
                    <u> {{$status}} </u>
                </button>
                <button style="@php echo $stylesEventRatio; @endphp" class="oceans-gaming-default-button oceans-gaming-default-button-small flexbox-centered-space">
                    &nbsp;
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <span>
                        8 / 14
                    </span>
                    &nbsp;
                </button>
            </div>
            <br>
            <p style="max-height : 60px; text-overflow:ellipsis; overflow:hidden; "><u>{{$event->eventName}}</u></p>
            <p class="small-text"><i>
                    {{ $organizer->companyName ?? 'Not set' }}
                </i></p>
            <div class="flexbox-welcome">
                <div>@php echo $dateStr; @endphp</div>
            </div>
            <div style="color: #2F4A58;">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    &nbsp;
                    <span>Prize</span>
                </div>
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign">
                        <line x1="12" y1="1" x2="12" y2="23"></line>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                    &nbsp;
                    <span>Free</span>
                </div>
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-map-pin">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                    &nbsp;
                    <span>{{$event->region ?? 'South East Asia'}}</span>
                </div>
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-info">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    &nbsp;
                    <span>{{$event->type->eventType ?? 'Choose a type'}}</span>
                </div>
            </div>
            <div class="group-hover-flexbox icon2">
                @if ($status == 'UPCOMING' || $status == 'DRAFT')
                <a style="padding: none; margin: none;" href="{{ route('event.show', $event->id) }}">
                    <img onclick="goToLivePreview()" class="larger-hover" src="{{ asset('/assets/images/events/live-preview-icon.png') }}" alt="live preview" width="30" height="30" style="object-fit: cover; ">
                </a>
                <a style="padding: none; margin: none;">
                    <button onclick="" style="padding: none; margin: none; background-color: transparent; outline: none; border: none;" type="button" data-toggle="modal" data-target="#shareModal">
                        <img class="larger-hover" src="{{ asset('/assets/images/events/members-icon.png') }}" alt="members" width="30" height="30" style="object-fit: cover; ">
                    </button>
                </a>
                <a style="padding: none; margin: none;">
                    <img style="padding: none; margin: none;" onclick="copyUtil('event')" class="larger-hover" src="{{ asset('/assets/images/events/clipboard-icon.png') }}" alt="clipboard" width="40" height="30" style="object-fit: cover; ">
                </a>
                @if ($status != 'UPCOMING')
                <a style="padding: none; margin: none;" href="{{ route('event.edit', $event->id) }}">
                    <img onclick="goToEditScreen()" class="larger-hover" src="{{ asset('/assets/images/events/edit-icon.png') }}" alt="edit" width="30" height="30" style="object-fit: cover; ">
                </a>
                @endif
                @endif
            </div>
            <br>
            <!-- Modal -->
        </div>
        
        <script>

            const copyUtil = (urlType) => {
                let copyUrl = '';
                switch (urlType) {
                    case 'event':
                        copyUrl = "{{ route('event.index', $eventId) }}";
                        copyUrlFunction(copyUrl);
                        break;
                    case 'facebook':
                        copyUrl = "{{ route('organizer.live.view', $eventId) }}";
                        localStorage.setItem('copyUrl', copyUrl);
                        break;
                    default:
                        copyUrl = "Set copy url first!";
                        localStorage.setItem('copyUrl', copyUrl);
                        break;
                }

            }
        </script>
    </div>
</div>

@endforeach