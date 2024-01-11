@foreach ($events as $event)
    @php
        $stylesEventStatus = bladeEventStatusStyleMapping($event->status);
        $stylesEventStatus .= 'padding-top: -150px; ';
        $eventTierLowerImg = bladeEventTierImage($event->eventTier);
        $eventBannerImg = bladeImageNull($event->eventBanner);
        $bladeEventGameImage = bladeImageNull($event->game ? $event->game->gameIcon : null);
    @endphp
    <div class="event">
        <a href="/event/{{ $event['id'] }}" style="z-index: 999">
            <div style="display: flex; justify-content: center;">
                <img style="position: absolute !important; top: -35px !important; z-index: 111; border-radius: 60px !important; object-fit: cover;"
                    width="100" height="100" src="{{ $eventTierLowerImg }}">
            </div>
            <img src="{{ $eventBannerImg }}" {!! trustedBladeHandleImageFailure() !!} class="cover">
            <div class="frame1">
                <img src="{{ $bladeEventGameImage }}" style="padding-left: 20px;" class="logo2">
                <span class="event_status_1" style="@php echo $stylesEventStatus; @endphp">{{ $event->statusResolved() }}
                </span>
            </div>
            
            <div class="league_name mt-4">
                <b>{{ $event->eventName }}</b> <br>
                <small>{{ $event->region ?? 'South East Asia' }}</small>
            </div>
            <div class="trophy_caption mt-4">
                <img src="{{ asset('/assets/images/trophy.png') }}" class="trophy"><br>
            </div>
            <div style="text-align: center;">
                <img src="{{ asset('/assets/images/eye.png') }}" alt="" height="20px" width="20px"
                    style="padding: 10px;">
                <img src="{{ asset('/assets/images/chart.png') }}" alt="" height="20px" width="20px"
                    style="padding: 10px;">
                <img src="{{ asset('/assets/images/share.png') }}" alt="" height="20px" width="20px"
                    style="padding: 10px;">
            </div>
        </a>
    </div>
@endforeach
