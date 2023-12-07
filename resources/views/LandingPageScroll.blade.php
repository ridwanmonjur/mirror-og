@foreach($events as $event)
@php
$stylesEventStatus = bladeEventStatusStyleMapping($event->status);
$stylesEventStatus .= 'padding-top: -150px; ';
$eventTierLowerImg = bladeEventTierImage($event->eventTier);
$eventBannerImg = bladeEventBannerImage($event->eventBanner);
@endphp
<div class="event">
    <div class="event_head_container">
        <img id='turtle' src="{{ $eventTierLowerImg }}" class="event_head">
    </div>
    <img src="{{ $eventBannerImg }}" class="cover">
    <div class="frame1">
        <img src="{{ asset('/assets/images/dota.png') }}" class="logo2">
        <a class="event_status_1" style="@php echo $stylesEventStatus; @endphp">{{ $event->status ?? 'DRAFT' }}</a>
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