<div class="position-relative">
    <div class="position-absolute d-flex w-100 justify-content-center" style="top: -20px; ">
        @if (in_array($joinEvent->status, ['ONGOING', 'UPCOMING']))
            <ul class="achievement-list px-4">
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" class="me-2" fill="green"
                        class="bi bi-broadcast" viewBox="0 0 16 16">
                        <path
                            d="M3.05 3.05a7 7 0 0 0 0 9.9.5.5 0 0 1-.707.707 8 8 0 0 1 0-11.314.5.5 0 0 1 .707.707m2.122 2.122a4 4 0 0 0 0 5.656.5.5 0 1 1-.708.708 5 5 0 0 1 0-7.072.5.5 0 0 1 .708.708m5.656-.708a.5.5 0 0 1 .708 0 5 5 0 0 1 0 7.072.5.5 0 1 1-.708-.708 4 4 0 0 0 0-5.656.5.5 0 0 1 0-.708m2.122-2.12a.5.5 0 0 1 .707 0 8 8 0 0 1 0 11.313.5.5 0 0 1-.707-.707 7 7 0 0 0 0-9.9.5.5 0 0 1 0-.707zM10 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0" />
                    </svg>
                    {{ $joinEvent->status }}
                </li>
            </ul>
        @else
            <ul class="achievement-list">
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor"
                        class="bi bi-trophy" viewBox="0 0 16 16">
                        <path
                            d="M2.5.5A.5.5 0 0 1 3 0h10a.5.5 0 0 1 .5.5q0 .807-.034 1.536a3 3 0 1 1-1.133 5.89c-.79 1.865-1.878 2.777-2.833 3.011v2.173l1.425.356c.194.048.377.135.537.255L13.3 15.1a.5.5 0 0 1-.3.9H3a.5.5 0 0 1-.3-.9l1.838-1.379c.16-.12.343-.207.537-.255L6.5 13.11v-2.173c-.955-.234-2.043-1.146-2.833-3.012a3 3 0 1 1-1.132-5.89A33 33 0 0 1 2.5.5m.099 2.54a2 2 0 0 0 .72 3.935c-.333-1.05-.588-2.346-.72-3.935m10.083 3.935a2 2 0 0 0 .72-3.935c-.133 1.59-.388 2.885-.72 3.935M3.504 1q.01.775.056 1.469c.13 2.028.457 3.546.87 4.667C5.294 9.48 6.484 10 7 10a.5.5 0 0 1 .5.5v2.61a1 1 0 0 1-.757.97l-1.426.356a.5.5 0 0 0-.179.085L4.5 15h7l-.638-.479a.5.5 0 0 0-.18-.085l-1.425-.356a1 1 0 0 1-.757-.97V10.5A.5.5 0 0 1 9 10c.516 0 1.706-.52 2.57-2.864.413-1.12.74-2.64.87-4.667q.045-.694.056-1.469z" />
                    </svg>
                </li>
            </ul>
        @endif
    </div>
    <div class="event mx-auto" style="margin-bottom : 0;">
        <div class="background-event flex-column d-flex justify-content-between"
            style="background: url({{ bladeImageNull($joinEvent->eventBanner) }});">
            @if (!isset($joinEvent->roster[0]))
                <div class="player-info mt-4 ms-4">
                    <span>Empty roster</span>
                </div>
            @else
                <ul class="player-info mt-4 ms-4 invisible-until-hover">
                    @foreach ($joinEvent->roster as $roster)
                        <li>
                            <span>{{ $roster->user->name }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
            <div class="d-flex mt-2 mb-3 justify-content-center">
                @if ($selectTeam->creator_id == $user->id && !$isRegistrationView)
                    <div class="d-flex w-100 justify-content-center mt-2">
                        <form method="GET"
                            action="{{ route('participant.roster.manage', ['id' => $joinEvent->eventDetails->id, 'teamId' => $selectTeam->id]) }}">
                            <button class="btn btn-link me-2" type="submit">
                               <u> Manage Roster </u>
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
        <div class="frame1" style="margin-bottom: 0;">
            <div class="container d-flex justify-content-between pt-2">
                <div>
                    <img {!! trustedBladeHandleImageFailureBanner() !!} src="{{ bladeImageNull($joinEvent->user->eventBanner) }}"
                        class="me-1 logo2">
                    <span> {{ $joinEvent->eventDetails->eventName }} </span>
                </div>
                <div>
                    <img {!! trustedBladeHandleImageFailureBanner() !!}
                        src="{{ bladeImageNull($joinEvent->game ? $joinEvent->game->gameIcon : null) }}"
                        class="logo2 me-1">
                    <span class="me-1">{{ $joinEvent->game->gameTitle }}</span>
                </div>
                <div>
                    <span>
                        @if ($followCounts[$joinEvent->eventDetails->user_id] == 1)
                            1 follower 
                        @else
                            {{ $followCounts[$joinEvent->eventDetails->user_id] }} followers
                        @endif    
                    </span>
                </div>
            </div>
        </div>
        <br>
    </div>
</div>
