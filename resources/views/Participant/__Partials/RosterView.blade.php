@php
    $random_int = rand(0, 999);
@endphp
<div class="position-relative opacity-parent-until-hover d-block">
    <div class="position-absolute d-flex w-100 justify-content-center" style="top: -20px; ">
        <a href="{{ route('public.event.view', ['id' => $joinEvent->eventDetails->id]) }}">
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
        </a>
    </div>

    <div @class([
        'event mx-auto event-width cursor-pointer visible-until-hover-parent',
        'rounded-box-' . strtoLower($joinEvent->tier?->eventTier),
    ]) style="margin-bottom : 0;">
        <a href="{{ route('public.event.view', ['id' => $joinEvent->eventDetails->id]) }}">
            <img {!! trustedBladeHandleImageFailureBanner() !!}
                @class([
                'opacity-until-hover',
                'rounded-box-' . strtoLower($joinEvent->tier?->eventTier),
            ])
                style="object-fit: cover; border-radius: 20px; border-bottom-width: 2px; border-bottom-style: solid; max-height: 200px;"
                src="{{ '/storage' . '/' . $joinEvent->eventDetails->eventBanner }}" width="100%" height="80%;">
            <div class="invisible-until-hover mt-4 ms-4 position-absolute" style="top: 20px;"
                style="width: 100%; background-color: red;">

                @if (!isset($joinEvent->roster[0]))
                    <span>Empty roster</span>
                @else
                    <ul class="d-flex flex-column flex-start ">
                        @foreach ($joinEvent->roster as $roster)
                            <li onclick="goToUrl(event, this)"
                                data-url="{{ route('public.participant.view', ['id' => $roster->user->id]) }}"
                                style="list-style: none;"    
                            >
                                 <img class="rounded-circle object-fit-cover random-color-circle me-2 mb-1" width="25" height="25" 
                                    src="{{ $roster->user->userBanner ? asset('storage/' . $roster->user->userBanner) : '/assets/images/404.png' }}" 

                                    {!!trustedBladeHandleImageFailureBanner()!!}>
                                <small>{{ $roster->user->name }}</small>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </a>
        <div class="frame1 p-0 mx-0 mb-0">
            <div class="row mx-0 w-100" style="padding: 5px 10px;">
                <div class="col-12 col-xl-6  my-1 px-0">
                    <a class="d-flex justify-content-start" href="{{ route('public.event.view', ['id' => $joinEvent->eventDetails->id]) }}">
                        <img {!! trustedBladeHandleImageFailureBanner() !!}
                            src="{{ bladeImageNull($joinEvent->game ? $joinEvent->game?->gameIcon : null) }}"
                            class="object-fit-cover me-1" width="60px" height="40px"
                        >
                        <span class="text-truncate-2-lines text-start"> {{ $joinEvent->eventDetails->eventName }}
                        </span>
                    </a>
                </div>
                <div onclick="goToUrl(event, this)"
                    data-url="{{ route('public.organizer.view', ['id' => $joinEvent->eventDetails->user->id]) }}"
                    class="col-6 col-xl-4 d-flex justify-content-center mx-0 mt-1 px-0">
                    <img {!! trustedBladeHandleImageFailureBanner() !!} 
                        src="{{ $roster->user->userBanner ? asset('storage/' . $roster->user->userBanner) : '/assets/images/404.png' }}" 
                        width="45"
                        height="45" class="me-1 object-fit-cover random-color-circle"
                    >
                    <div class="d-inline-block text-start me-1">
                        <span
                            class="text-truncate-2-lines h-auto ">{{ $joinEvent->eventDetails->user->name }}</span>
                        <small
                            data-count="{{ array_key_exists($joinEvent->eventDetails->user_id, $followCounts) ? $followCounts[$joinEvent->eventDetails->user_id] : 0 }} "
                            class="d-block p-0 {{ 'followCounts' . $joinEvent->eventDetails?->user_id }}">
                            {{ $followCounts[$joinEvent->eventDetails->user_id] }}
                            follower{{ bladePluralPrefix($followCounts[$joinEvent->eventDetails->user_id]) }}
                        </small>
                    </div>
                </div>
                <form onclick="event.stopPropagation(); " 
                    onsubmit="onFollowSubmit(event)"
                    id="{{ 'followForm' . $joinEvent->id . $random_int }}"
                    data-join-event-user ="{{ $joinEvent->eventDetails?->user_id }}"
                    method="POST" class="col-6 col-xl-2 px-0" action="{{ route('participant.organizer.follow') }}">
                    @csrf
                    @guest
                        <input type="hidden" name="user_id" value="">
                        <input type="hidden" name="organizer_id" value="">
                    @endguest
                    @auth
                        <input type="hidden" name="user_id" value="{{ $user && $user->id ? $user->id : '' }}">
                        <input type="hidden" name="organizer_id" value="{{ $joinEvent->eventDetails?->user_id }}">
                    @endauth

                    @guest
                        <button type="button"
                            onclick="event.preventDefault(); event.stopPropagation(); reddirectToLoginWithIntened('{{ route('public.organizer.view', ['id' => $joinEvent->eventDetails?->user_id]) }}')"
                            class="mx-auto mt-2 mb-4 {{ 'followButton' . $joinEvent->eventDetails?->user_id }}"
                            style="background-color: #43A4D7; color: white;  padding: 5px 10px; font-size: 0.875rem; border-radius: 10px; border: none;">
                            Follow
                        </button>
                    @endguest
                    @auth
                        @if ($user->role == 'PARTICIPANT')
                            <button type="submit"
                                class="mx-auto mt-2 mb-4 {{ 'followButton' . $joinEvent->eventDetails?->user_id }}"
                                style="background-color: {{ $joinEvent->isFollowing ? '#8CCD39' : '#43A4D7' }}; color: {{ $joinEvent->isFollowing ? 'black' : 'white' }};  padding: 5px 10px; font-size: 0.875rem; border-radius: 10px; border: none;">
                                {{ $joinEvent->isFollowing ? 'Following' : 'Follow' }}
                            </button>
                        @else
                            <button type="button"
                                onclick="event.preventDefault(); event.stopPropagation(); toastWarningAboutRole(this, 'Participants can follow only!');"
                                class="mx-auto mt-2 mb-4 {{ 'followButton' . $joinEvent->eventDetails?->user_id }}"
                                style="background-color: #43A4D7; color: white;  padding: 5px 10px; font-size: 0.875rem; border-radius: 10px; border: none;">
                                Follow
                            </button>
                        @endif
                    @endauth
                </form>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('/assets/js/shared/RosterView.js') }}"></script>

    
