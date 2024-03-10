<div class="event mx-auto">
    <div style="background-color:rgb(185, 182, 182); text-align: left; height: 200px;">
        <br>
        @if (!isset($joinEvent->roster[0]))
            <div class="player-info mt-1 ms-4">
                <span>Empty roster</span>
            </div>
        @else
            <ul class="player-info mt-1 ms-4">
                @foreach ($joinEvent->roster as $roster)
                    <li>
                        <span>{{ $roster->user->name }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
    <div class="frame1">
        <div class="container d-flex justify-content-between pt-2">
            <div>
                <img {!! trustedBladeHandleImageFailureBanner() !!} src="{{ bladeImageNull($joinEvent->eventBanner) }}" class="logo2">
                <span> {{ $joinEvent->eventDetails->eventName }} </span>
            </div>
            <div>
                <img {!! trustedBladeHandleImageFailureBanner() !!}
                    src="{{ bladeImageNull($joinEvent->game ? $joinEvent->game->gameIcon : null) }}" class="logo2 me-2">
                <span>{{ $joinEvent->game->gameTitle }}</span>
                <span>1K Followers</span>
            </div>

        </div>
    </div>
    <div class="d-flex mt-2 mb-3 justify-content-center">
        @if ($selectTeam->creator_id == $user->id)
            <div class="me-3">
                <form method="GET"
                    action="{{ route('participant.roster.manage', ['id' => $joinEvent->eventDetails->id, 'teamId' => $selectTeam->id]) }}">
                    <button class="oceans-gaming-default-button oceans-gaming-default-button-link me-2" type="submit">
                        Manage Roster
                    </button>
                </form>
            </div>
        @endif
        <div>
            <form method="GET"
                action="{{ route('participant.roster.manage', ['id' => $joinEvent->eventDetails->id, 'teamId' => $selectTeam->id]) }}">
                <button class="oceans-gaming-default-button oceans-gaming-default-button-link me-2" type="submit">
                    Manage Registration
                </button>
            </form>
        </div>
    </div>
</div>
