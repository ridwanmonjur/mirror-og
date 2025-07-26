@php
    $random_int = rand(0, 999);
    
    if (!function_exists('truncateText')) {
        function truncateText($text, $maxLength = 40) {
            return strlen($text) > $maxLength ? substr($text, 0, $maxLength) . '...' : $text;
        }
    }
@endphp
<div class="position-relative d-block ">

    <div class="position-absolute d-flex w-100 justify-content-center" style="top: -20px; ">
        @if (in_array($joinEvent->status, ['ONGOING', 'UPCOMING']))
            <ul class="achievement-list px-4">
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" class="me-2" fill="#179317"
                        class="bi bi-broadcast" viewBox="0 0 16 16">
                        <path
                            d="M3.05 3.05a7 7 0 0 0 0 9.9.5.5 0 0 1-.707.707 8 8 0 0 1 0-11.314.5.5 0 0 1 .707.707m2.122 2.122a4 4 0 0 0 0 5.656.5.5 0 1 1-.708.708 5 5 0 0 1 0-7.072.5.5 0 0 1 .708.708m5.656-.708a.5.5 0 0 1 .708 0 5 5 0 0 1 0 7.072.5.5 0 1 1-.708-.708 4 4 0 0 0 0-5.656.5.5 0 0 1 0-.708m2.122-2.12a.5.5 0 0 1 .707 0 8 8 0 0 1 0 11.313.5.5 0 0 1-.707-.707 7 7 0 0 0 0-9.9.5.5 0 0 1 0-.707zM10 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0" />
                    </svg>
                    {{ $joinEvent->status }}
                </li>
            </ul>
        @else
            <ul class="achievement-list z-99">
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

    <div @class([
        'event  mx-auto event-width cursor-pointer',
        'rounded-box-' . strtoLower($joinEvent->tier?->eventTier),
    ]) style="margin-bottom : 0;">
        <a href="{{ route('public.event.view', ['id' => $joinEvent->id, 'title' => $joinEvent->slug ]) }}">
            <img 
                id="eventBanner"
                onerror="this.onerror=null;this.src='{{asset('assets/images/404.png')}}';"
                @class([
                'opacity-until-hover w-100 h-100 object-fit-cover me-1 border-0',
            ])
                style="object-fit: cover; border-radius: 20px; border-bottom-width: 2px; border-bottom-style: solid; max-height: 200px;"
                src="{{ '/storage' . '/' . $joinEvent->eventBanner }}" >
           
        </a>
        <div class="frame1 p-0 mx-0 mb-0">
            <div class="row mx-0 w-100" style="padding: 5px 10px;">
                <div class="col-6 col-xl-5 d-flex justify-content-start d my-1 px-0">
                    <a class="d-flex w-100 justify-content-start align-items-center" 
                        onclick="window.trackEventCardClick(this)" 
                        data-event-id="{{ $joinEvent->id }}" 
                        data-event-name="{{ $joinEvent->eventName }}"
                        @if($joinEvent->tier?->eventTier) data-event-tier="{{ $joinEvent->tier->eventTier }}" @endif
                        @if($joinEvent->type?->eventType) data-event-type="{{ $joinEvent->type->eventType }}" @endif
                        @if($joinEvent->game?->gameTitle) data-esport-title="{{ $joinEvent->game->gameTitle }}" @endif
                        @if($joinEvent->venue) data-location="{{ $joinEvent->venue }}" @endif
                        @if($joinEvent->tier?->id) data-tier-id="{{ $joinEvent->tier->id }}" @endif
                        @if($joinEvent->type?->id) data-type-id="{{ $joinEvent->type->id }}" @endif
                        @if($joinEvent->game?->id) data-game-id="{{ $joinEvent->game->id }}" @endif
                        @if($joinEvent->user?->id) data-user-id="{{ $joinEvent->user->id }}" @endif
                        title="Event {{$joinEvent->slug}}"
                        href="{{ route('public.event.view', ['id' => $joinEvent->id, 'title' => $joinEvent->slug]) }}">

                        <img 
                            onerror="this.onerror=null;this.src='{{asset('assets/images/404.png')}}';" style="max-width: 50px; "
                            src="{{ bldImg($joinEvent->game ? $joinEvent->game?->gameIcon : null) }}"
                            class="object-fit-cover me-1 rounded-2 " width="30px" height="30px"
                            style="object-position: center;"    
                        >
                        <span class="text-wrap d-inline-block  text-start pe-2"> {{ truncateText($joinEvent->eventName) }} </span>
                    </a>
                </div>
                <div onclick="goToUrl(event, this)"
                    data-url="{{ route('public.organizer.view', ['id' => $joinEvent->user->id, 'title' => $joinEvent->user->slug ]) }}"
                    class="col-6 col-xl-5 d-flex justify-content-start align-items-center px-0 mx-0 mt-1">
                    <img 
                        onerror="this.onerror=null;this.src='{{asset('assets/images/404.png')}}';"
                        src="{{ bldImg($joinEvent->user->userBanner) }}" width="35" height="35"
                        class="me-2 object-fit-cover rounded-circle rounded-circle2" >
                    <div class="text-start d-inline-flex flex-column justify-content-center ">
                        <small class="d-inline-block my-0 text-wrap ">{{ truncateText($joinEvent->user->name) }}</small>
                        <small
                            data-count="{{ array_key_exists($joinEvent->user_id, $followCounts) ? $followCounts[$joinEvent->user_id] : 0 }} "
                            class="d-inline-block {{ 'followCounts' . $joinEvent->user_id }}">
                            {{ $followCounts[$joinEvent->user_id] }}
                            follower{{ bldPlural($followCounts[$joinEvent->user_id]) }}
                        </small>
                    </div>
                </div>
                <form id="{{ 'followForm' . $joinEvent->id . $random_int }}" method="POST" class="d-none d-xl-flex col-6 followFormProfile col-xl-2 my-2 justify-content-end text-end px-0"
                    action="{{ route('participant.organizer.follow') }}">
                    @csrf
                    @guest
                        <input type="hidden" name="user_id" value="">
                        <input type="hidden" name="organizer_id" value="">
                    @endguest
                    @auth
                        <input type="hidden" name="user_id" value="{{ $user && $user->id ? $user->id : '' }}">
                        <input type="hidden" name="organizer_id" value="{{ $joinEvent->user_id }}">
                    @endauth

                    @guest
                        <button type="button"
                            onclick="reddirectToLoginWithIntened('{{ route('public.organizer.view', ['id' => $joinEvent->user_id, 'title'=> $joinEvent?->user?->slug]) }}')"
                            class="  roster-button  {{ 'followButton' . $joinEvent->user_id }}"
                        >
                            Follow
                        </button>
                    @endguest
                    @auth
                        @if ($user->role == 'PARTICIPANT')
                            <button type="submit" class="{{ 'followButton' . $joinEvent->user_id }}   roster-button  "
                                style="background-color: {{ $userProfile?->isFollowing ? '#8CCD39' : '#43A4D7' }}; color: {{ $userProfile?->isFollowing ? 'black' : 'white' }};  ">
                                {{ $userProfile?->isFollowing ? 'Following' : 'Follow' }}
                            </button>
                        @else
                            <button type="button" onclick="toastWarningAboutRole(this, 'Participants can follow only!');"
                                class="  {{ 'followButton' . $joinEvent->user_id }} roster-button  "
                            >
                                Follow
                            </button>
                        @endif
                    @endauth
                </form>
            </div>
        </div>
    </div>
</div>


