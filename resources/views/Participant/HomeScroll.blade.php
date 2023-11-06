@foreach($events as $event)
<div class="event">
    <div class="event_head_container">
        <img id='turtle' src="{{ asset('/assets/images/logo/3.png') }}" class="event_head">
    </div>
    <img src="{{ asset('/assets/images/event_bg.jpg') }}" class="cover">
    <div class="frame1">
        <img src="{{ asset('/assets/images/dota.png') }}" class="logo2">
        <a class="event_status_1">{{ $event->status }}</a>
    </div><br>
    <div class="league_name">
        <b>{{ $event->name }}</b><br>
        <a><small>{{ $event->venue }}</small></a>
    </div><br>
    <div class="trophy_caption">
        <img src="{{ asset('/assets/images/trophy.png') }}" class="trophy"><br>
        <a class="league_caption">
            <b>{{ $event->caption }}</b>
        </a>
    </div>
</div>
@endforeach