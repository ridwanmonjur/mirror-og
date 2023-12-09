@foreach ($eventList as $event)
@php
$stylesEventStatus = bladeEventStatusStyleMapping($event->status);
$stylesEventStatus .= 'padding-top: -150px; ';

$stylesEventRatio= bladeEventRatioStyleMapping($event->registeredParticipants, $event->totalParticipants);
$eventTierLower= $event->eventTier ? strtolower($event->eventTier) : 'Choose tier!';
$dateArray = bladeGenerateEventStartEndDateStr($event->startDate, $event->startTime);
[
$datePart,
$timePart,
$dayStr,
$combinedStr,
$dateStr
] = array_values($dateArray);

$eventTierLowerImg = bladeEventTierImage($event->eventTier);

$eventBannerImg = bladeImageNull($event->eventBanner);

$bladeEventGameImage = bladeImageNull($event->game->gameIcon);

@endphp
<a href="{{ route('event.show', $event->id) }}" style="text-decoration: none;">
    <div class="{{'rounded-box rounded-box-' . $eventTierLower }}">
        <div class="centered-absolute-game-tier">
            <img src="{{  $eventTierLowerImg }}" width="100" style="object-fit: cover;">
        </div>
        <div class="{{'card-image card-image-' . $eventTierLower }}">
            <img width="200" height="200" style="object-fit: cover; " 
            {!! trustedBladeHandleImageFailure(); !!}
            src="{{ $eventBannerImg }}" alt="">
        </div>
        <div class="card-text">
            <div>
                <div class="flexbox-centered-space">
                    <img src="{{ $bladeEventGameImage }}" alt="menu" height="50">
                    <span> {{ $event->game->gameTitle }} </span>
                    <button class="oceans-gaming-default-button" style="@php echo $stylesEventStatus; @endphp">
                        {{$event->status}}
                    </button>
                </div>
                <br>
                <p style="height : 60px; text-overflow:ellipsis; overflow:hidden; "><u>{{$event->eventName}}</u></p>
                <p class="small-text"><i>
                        {{ $organizer->companyName ?? 'Not set' }}
                    </i></p>
                <div class="flexbox-welcome">

                    <div>@php echo $dateStr; @endphp</div>
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
                <div>
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
            </div>
        </div>
        <br>
    </div>
</a>
@endforeach