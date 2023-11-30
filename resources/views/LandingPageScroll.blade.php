@foreach($events as $event)
        @php
            $stylesEventStatus = '';
            $stylesEventStatus .= 'padding-top: -150px; ';
            $stylesEventStatus .= 'background-color: ' . $mappingEventState[$event->action]['buttonBackgroundColor'] .' ;' ;
            $stylesEventStatus .= 'color: ' . $mappingEventState[$event->action]['buttonTextColor'] .' ; ' ;
            $stylesEventStatus .= 'border: 1px solid ' . $mappingEventState[$event->action]['borderColor'] .' ; ';
            @endphp
        <div class="event">
            <div class="event_head_container">
                <img id='turtle' src="{{ asset('/assets/images/logo/3.png') }}" class="event_head">
            </div>
            <img src="{{ asset('storage/'. $event->eventBanner) }}" class="cover">
            <div class="frame1" >
                <img src="{{ asset('/assets/images/dota.png') }}" class="logo2">
                <a class="event_status_1" style="@php echo $stylesEventStatus; @endphp">{{ $event->action }}</a>
            </div><br>
            <div class="league_name">
                <b>{{ $event->eventName }}</b><br>
                <a><small>South East Asia</small></a>
            </div><br>
            <div class="trophy_caption">
                <img src="{{ asset('/assets/images/trophy.png') }}" class="trophy"><br>
                <a class="league_caption">
                    <b>Soon</b>
                </a>
            </div>
        </div>
        @endforeach