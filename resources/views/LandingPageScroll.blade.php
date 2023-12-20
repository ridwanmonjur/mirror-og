@foreach($events as $event)
@php
$stylesEventStatus = bladeEventStatusStyleMapping($event->status);
$stylesEventStatus .= 'padding-top: -150px; ';
$eventTierLowerImg = bladeEventTierImage($event->eventTier);
$eventBannerImg = bladeImageNull($event->eventBanner);
$bladeEventGameImage = bladeImageNull($event->game->gameIcon);
@endphp
<div class="event">
    <div style="display: flex; justify-content: center;">
        <img style="position: absolute !important; top: -35px !important; z-index: 999; border-radius: 60px !important; object-fit: cover;" width="100" height="100" src="{{ $eventTierLowerImg }}">
    </div>
    <img src="{{ $eventBannerImg }}" {!! trustedBladeHandleImageFailure() !!} class="cover">
    <div class="frame1">
        <img src="{{ $bladeEventGameImage }}" style="padding-left: 20px;" class="logo2">
        <a class="event_status_1" style="@php echo $stylesEventStatus; @endphp">{{ $event->statusResolved() }}</a>
    </div><br>
    <div class="league_name">
        <a href="/participant/event/{{ $event['id'] }}"> <b>{{ $event->eventName }}</b><br> </a>
        <a><small>{{ $event->region ?? 'South East Asia' }}</small></a>
    </div><br>
    <div class="trophy_caption">
        <img src="{{ asset('/assets/images/trophy.png') }}" class="trophy"><br>
        {{-- <a class="league_caption">
            <b>Soon</b>
        </a> --}}
    </div>
    <div style="text-align: center;">
        <img src="{{ asset('/assets/images/eye.png') }}" alt="" height="20px" width="20px" style="padding: 10px;">
        <img src="{{ asset('/assets/images/chart.png') }}" alt="" height="20px" width="20px" style="padding: 10px;">
        <img src="{{ asset('/assets/images/share.png') }}" alt="" height="20px" width="20px" style="padding: 10px;">
    </div>
</div>
@endforeach
