<div class="event">
    <div style="background-color:rgb(185, 182, 182); text-align: left; height: 200px;">
        <br>
        <div class="player-info">
            <div class="player-image"
                style="background-image: url('https://www.svgrepo.com/download/347916/radio-button.svg')">
            </div>
            <span>{{ $joinEvent->game->gameTitle }}</span>
        </div>
    </div>
    <div class="frame1">
        <div class="container">
            <div class="left-col">
                <p><img {{!! trustedBladeHandleImageFailureBanner(); !!}} src="{{ bladeImageNull($joinEvent->eventBanner) }}" class="logo2">
                <p> {{ $joinEvent->eventDetails->eventName }} </p>
            </div>
            <div class="right-col">
                <p><img {{!! trustedBladeHandleImageFailureBanner(); !!}} src="{{ bladeImageNull($joinEvent->game ? $joinEvent->game->gameIcon : null) }}" class="logo2">
                <p>{{ $joinEvent->game->gameTitle }}</p>
                <br>
                <p>1K Followers</p>
                </p>
            </div>
        </div>
    </div>
</div>
