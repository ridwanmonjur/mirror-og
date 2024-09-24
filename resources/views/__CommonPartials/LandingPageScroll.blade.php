@foreach ($events as $event)
    @php
        $stylesEventStatus = bladeEventStatusStyleMapping($event->status);
        $stylesEventStatus .= 'padding-top: -150px; ';
        $eventTierLowerImg = bladeEventTierImage($event->tier ? $event->tier?->eventTier: null);
        $eventBannerImg = bladeImageNull($event->eventBanner);
        $bladeEventGameImage = bladeImageNull($event->game ? $event->game?->gameIcon : null);
    @endphp
    <div class="event">
        <a class="d-block" href="/event/{{ $event['id'] }}">
            <div style="display: flex; justify-content: center;">
                <img style="position: absolute !important; top: -35px !important; z-index: 111; border-radius: 60px !important; object-fit: cover;"
                    width="100" height="100" src="{{ $eventTierLowerImg }}"
                >
            </div>
            <img src="{{ $eventBannerImg }}" {!! trustedBladeHandleImageFailure() !!} class="cover" style="min-height: 150px !important; ">
            <div class="frame1 d-flex justify-content-around flex-wrap">
                <img src="{{ $bladeEventGameImage }}" class="logo2 mt-2"
                  onerror="this.onerror=null;this.src='/assets/images/404.png';"   
                >
                <button class="event_status_1" style="@php echo $stylesEventStatus; @endphp">{{ $event->statusResolved() }}
                </button>
            </div>
            <div class="league_name mt-4">
                <p class="ms-0 mb-0 p-0"><b>{{ $event->eventName }}</b></p>
                <small>{{ $event->venue ?? 'South East Asia' }}</small>
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
