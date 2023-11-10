@foreach ($eventList as $event)
@php
$stylesEventStatus = '';
$stylesEventStatus .= 'padding-top: -150px; ';
$stylesEventStatus .= 'background-color: ' . $mappingEventState[$event->action]['buttonBackgroundColor'] .' ;' ;
$stylesEventStatus .= 'color: ' . $mappingEventState[$event->action]['buttonTextColor'] .' ; ' ;
$stylesEventStatus .= 'border: 1px solid ' . $mappingEventState[$event->action]['borderColor'] .' ; ';

/*
$stylesEventRatio = '';
$ratio = (double) $event->registeredParticipants / $event->totalParticipants;
if ($ratio > 0.9){
$stylesEventRatio .= "background-color: red; color: white;";
}
elseif ($ratio == 0){
$stylesEventRatio .= "background-color: #8CCD39; color: white;";
}
elseif ($ratio > 0.5){
$stylesEventRatio .= "background-color: #FA831F; color: white;";
}
elseif ($ratio <= 0.5){ $stylesEventRatio .="background-color: #FFE325; color: black;" ; } */ $stylesEventRatio="background-color: #FFE325; color: black;" ; $eventTierLower=strtolower($event->eventDetail->eventTier);
    @endphp
    <a href="{{ route('event.show', $event->id) }}" style="text-decoration: none;">
        <div class="{{'rounded-box rounded-box-' . $eventTierLower }}">
            <div class="centered-absolute-game-tier">
                <img src="{{ asset('/assets/images/dolphin.png') }}" width="120" height="80">
            </div>
            <div class="{{'card-image card-image-' . $eventTierLower }}">
                <img src="{{ asset('/assets/images/1.png') }}" alt="">
            </div>
            <div class="card-text">
                <div>
                    <div class="flexbox-centered-space">
                        <img src="{{ asset('/assets/images/menu.png') }}" alt="menu" width="50" height="40">
                        <button class="oceans-gaming-default-button" style="@php echo $stylesEventStatus; @endphp">
                            {{$event->status}}
                        </button>
                    </div>
                    <br>
                    <p style="height : 60px; text-overflow:ellipsis; overflow:hidden; "><u>{{$event->eventName}}</u></p>
                    <p class="small-text"><i>
                            {{ $organizer->companyName  }}
                        </i></p>
                    <div class="flexbox-welcome">
                        @php
                        $date = \Carbon\Carbon::parse($event->eventDetail->startDateTime);
                        $dateStr = $date->toFormattedDateString() . " " . $date->toTimeString();
                        @endphp
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
                            <span>South East Asia</span>
                        </div>
                    </div>
                </div>
            </div>
            <br>
        </div>
    </a>
    @endforeach