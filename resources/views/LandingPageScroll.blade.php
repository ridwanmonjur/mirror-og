@foreach($events as $event)
@php
$stylesEventStatus = bladeEventStatusStyleMapping($event->status);
$stylesEventStatus .= 'padding-top: -150px; ';
$eventTierLowerImg = bladeEventTierImage($event->eventTier);
$eventBannerImg = bladeImageNull($event->eventBanner);
$bladeEventGameImage = bladeImageNull($event->game->gameIcon);
@endphp
<div class="event">
    <div class="event_head_container">
        <img id='turtle' src="{{ $eventTierLowerImg }}" class="event_head">
    </div>
    <img src="{{ $eventBannerImg }}" {!! trustedBladeHandleImageFailure() !!} class="cover">
    <div class="frame1" >
        <img  src="{{ $bladeEventGameImage }}" style="padding-left: 20px;" class="logo2">
        <a class="event_status_1" style="@php echo $stylesEventStatus; @endphp">{{ $event->status ?? 'DRAFT' }}</a>
    </div><br>
    <div class="league_name">
        <b>{{ $event->eventName }}</b><br>
        <a><small>{{ $event->region ?? 'South East Asia' }}</small></a>
    </div><br>
    <div class="trophy_caption">
        <img src="{{ asset('/assets/images/trophy.png') }}" class="trophy"><br>
        <a class="league_caption">
            <b>Soon</b>
        </a>
    </div>
</div>
@endforeach