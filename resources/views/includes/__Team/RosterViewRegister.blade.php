@php
    $joinEvent->regStatus = $joinEvent->eventDetails->getRegistrationStatus();
    $random_int = rand(0, 999);
    $joinEvent->isUserPartOfRoster = false;
    $currentUser = ['memberId' => null, 'vote_to_quit' => null, 'rosterId' => null];
    $rosterUserIds = [];
    $votes = ['totalCount' => 0, 'stayCount' => 0, 'leaveCount' => 0];
@endphp
<div class="col-12 col-lg-6 position-relative opacity-parent-until-hover d-block mb-3" 
    id="reg-member-id-{{$joinEvent->id}}"
    data-members-value="{{json_encode($joinEvent->members)}}"
    data-event-details="{{json_encode(
        $joinEvent->eventDetails->only(['id', 'eventBanner', 'eventName', 'user', 'tier', 'game', 'startDate', 'startTime'])
    )}}"
    data-roster-captain-id="{{$joinEvent->captain?->team_member_id}}"
    data-follow-counts="{{$followCounts[$joinEvent->eventDetails->user_id]}}"
>
    <div class="position-absolute d-flex w-100 justify-content-center " style="top: -30px; ">
        <a href="{{ route('public.event.view', ['id' => $joinEvent->eventDetails->id]) }}">
            @if (in_array($joinEvent->status, ['ONGOING', 'UPCOMING']))
                <ul class="achievement-list my-0 py-2 px-4 z-99">
                    <li class="py-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" class="me-2" fill="green"
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
        </a>
    </div>

    <div @class([
        'event  mx-auto event-width cursor-pointer visible-until-hover-parent position-relative ',
        'rounded-box-' . strtoLower($joinEvent->tier?->eventTier),
    ]) >
        <a href="{{ route('public.event.view', ['id' => $joinEvent->eventDetails->id]) }}">
            <img 
                id="eventBanner"
                {!! trustedBladeHandleImageFailureBanner() !!} @class([
                'opacity-until-hover object-fit-cover border-0 w-100 h-100 ',
            ])
                style="border-radius: 20px; border-bottom-width: 2px; border-bottom-style: solid; height: 270px;"
                src="{{ '/storage' . '/' . $joinEvent->eventDetails->eventBanner }}" width="100%" height="80%;"
                >
            <div class="pt-3 mt-2 position-absolute custom-scrollbar w-100" 
                id="eventView"
                >

               
                    <div class="row d-flex justify-content-center" >
                        <div class="col-12 col-lg-6 px-0">
                            <ul class="">
                                @foreach ($joinEvent->roster as $roster)
                                    @php
                                        $rosterUserIds[ $roster?->user?->id] = true;
                                        $votes['totalCount'] ++;
                                        if ($roster->user->id == $user->id) {
                                            $joinEvent->isUserPartOfRoster = true;
                                            $currentUser['memberId'] = $roster->team_member_id;
                                            $currentUser['vote_to_quit'] = $roster->vote_to_quit;
                                            $currentUser['rosterId'] = $roster->id;
                                            $joinEvent->currentUser = $currentUser;
                                        }

                                        if ( $roster->vote_to_quit === 0 ) {
                                            $votes['stayCount']++;
                                        }

                                        if ( $roster->vote_to_quit === 1 ) {
                                            $votes['leaveCount']++;
                                        }

                                    @endphp
                                    <li onclick="goToUrl(event, this)"
                                        data-url="{{ route('public.participant.view', ['id' => $roster->user->id]) }}"
                                        class="d-none-until-hover-parent my-1  list-unstyled members-hover"
                                    >
                                       
                                        <img class="rounded-circle object-fit-cover random-color-circle me-2" width="25"
                                            height="25" 
                                            src="{{ $roster->user->userBanner ? asset('storage/' . $roster->user->userBanner) : '/assets/images/404.png' }}" 
                                            onerror="this.src='{{ asset('assets/images/404q.png') }}'; this.onerror=null;"
                                            {{-- {!! trustedBladeHandleImageFailureBanner() !!} --}}
                                        >
                                         @if ($joinEvent->roster_captain_id == $roster->id)
                                            <img 
                                                onclick="capatainMemberAction(event);"
                                                class="z-99 rounded-pill me-0  captain-crown"
                                                data-join-event-id="{{ $joinEvent->id }}"
                                                data-roster-captain-id="0"
                                                data-roster-user-id="{{$roster->user_id}}"
                                                data-roster-user-name="{{$roster->user->name}}"
                                                height="20" 
                                                width="20" 
                                                src="{{asset('assets/images/participants/crown-straight.png')}}"
                                            >

                                        @endif
                                        <small class="me-1">{{ $roster->user->name }}</small>
                                        @if ($joinEvent->join_status == "pending")
                                            <span class="d-none-until-hover">
                                                <svg 
                                                    onclick="disapproveMemberAction(event);"
                                                    class="text-red rounded-pill me-1 gear-icon-btn z-99 border-red"
                                                    data-join-event-id="{{ $joinEvent->id }}"
                                                    data-user-id="{{ $roster->user->id}}"
                                                    data-team-id="{{ $selectTeam->id }}"
                                                    xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                                    <path stroke="red" stroke-width="1.5"  d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                                                </svg>
                                                @if ($joinEvent->roster_captain_id != $roster->id)
                                                    <img 
                                                        onclick="capatainMemberAction(event);"
                                                        class="z-99 rounded-pill me-1 gear-icon-btn"
                                                        data-join-event-id="{{ $joinEvent->id }}"
                                                        data-roster-captain-id="{{ $roster->id}}"
                                                        data-roster-user-id="{{$roster->user_id}}"
                                                        data-roster-user-name="{{$roster->user->name}}"
                                                        height="20" 
                                                        width="20" 
                                                        src="{{asset('assets/images/participants/crown-straight.png')}}"
                                                    >
                                                @endif
                                            </span>
                                        @endif

                                    </li>
                                @endforeach
                                @if($joinEvent->join_status == "pending")
                                    @for ($i = 0; $i < $maxRosterSize - $votes['totalCount'] ; $i++)
                                        <li 
                                            data-join-event-id="{{ $joinEvent->id }}"
                                            onclick="addRosterMembers(event);"
                                            class="members-hover my-1 z-99 list-unstyled d-flex flex-column justify-content-center"
                                        >
                                        {{-- Plus icon --}}
                                            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"  viewBox="0 0 16 16"
                                                class="rounded-circle position-relative random-color-circle gear-icon-btn py-0 my-0 me-2"
                                                style="top: 0.5px;"
                                            >
                                            <path  stroke="gray" stroke-width="0.65" d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                                            </svg>
                                        </li>
                                    @endfor
                                @endif
                            </ul>
                        </div>
                        <div class="col-12 col-lg-6 ps-2 pe-3" onclick="event.stopPropagation();">
                            <div class="px-0">
                                @if ($joinEvent->isUserPartOfRoster) 
                                    <div class="text-end">
                                        <span>
                                            @if ($joinEvent->join_status == "confirmed" && !$joinEvent->vote_ongoing)
                                                <form action="{{route('participant.confirmOrCancel.action')}}" id="cancelRegistration" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="join_event_id" value="{{$joinEvent->id}}">
                                                    <input type="hidden" name="join_status" value="canceled">
                                                    <button 
                                                        data-join-event-id="{{$joinEvent->id}}"
                                                        data-form="{{'cancel2form' . $joinEvent->id  }}" 
                                                        type="button"
                                                        data-cancel="1"
                                                        data-join-status="{{$joinEvent->join_status}}"
                                                        data-registration-status="{{$joinEvent->regStatus}}"
                                                        onclick="submitConfirmCancelForm(event)" 
                                                        class="btn btn-sm text-light bg-red me-2 rounded-pill"
                                                    >
                                                        Leave Event
                                                    </button> 
                                                </form>
                                            @elseif ($joinEvent->join_status == "pending" && !$joinEvent->vote_ongoing) 
                                                <button 
                                                    onclick="disapproveMemberAction(event);"
                                                    class="btn btn-sm rounded-pill bg-red text-light me-3 z-99"
                                                    data-join-event-id="{{ $joinEvent->id }}"
                                                    data-user-id="{{$user->id}}"
                                                    data-team-id="{{ $selectTeam->id }}"
                                                    data-roster-id="{{ $currentUser['rosterId'] }}"
                                                >
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-door-open-fill" viewBox="0 0 16 16">
                                                <path d="M1.5 15a.5.5 0 0 0 0 1h13a.5.5 0 0 0 0-1H13V2.5A1.5 1.5 0 0 0 11.5 1H11V.5a.5.5 0 0 0-.57-.495l-7 1A.5.5 0 0 0 3 1.5V15zM11 2h.5a.5.5 0 0 1 .5.5V15h-1zm-2.5 8c-.276 0-.5-.448-.5-1s.224-1 .5-1 .5.448.5 1-.224 1-.5 1"/>
                                                </svg>
                                                    Leave Roster
                                                    {{-- {{$joinEvent?->voteStarter?->name}} --}}
                                                </button>
                                                
                                                <br>
                                            @endif
                                        </span>
                                    </div>
                                    @if ($joinEvent->vote_ongoing)
                                        <div class="border py-2 px-2 border-2 ms-3 ms-lg-0 border-primary bg-translucent">
                                            <small class="d-inline-block mt-0 mb-1 py-0"><small class="text-red">{{$joinEvent?->voteStarter?->name}}</small> has called a vote.</small>
                                            @if ($joinEvent->isUserPartOfRoster && isset($currentUser['vote_to_quit'])) 
                                                @if ($currentUser['vote_to_quit'])
                                                    <small class="d-inline-block text-red mb-1"> You voted to quit this event.</small>
                                                @else 
                                                    <small class="d-inline-block text-success mb-1"> You voted to stay in this event.</small>
                                                @endif
                                            @endif
                                           
                                            @if ($joinEvent->isUserPartOfRoster && !isset($currentUser['vote_to_quit']))
                                                <div class="d-flex justify-content-between">
                                                    <button 
                                                        class="btn btn-sm btn-success text-dark px-2 rounded-pill z-99"
                                                        data-vote-to-quit="0"
                                                        data-join-event-id="{{$joinEvent->id}}"
                                                        data-roster-id="{{ $currentUser['rosterId'] }}"
                                                        onclick="voteForEvent(event);"
                                                    > Stay
                                                    </button>
                                                    <button 
                                                        class="btn btn-sm bg-red text-white px-2 rounded-pill z-99"
                                                        data-vote-to-quit="1"
                                                        data-roster-id="{{ $currentUser['rosterId'] }}"
                                                        data-join-event-id="{{$joinEvent->id}}"
                                                        onclick="voteForEvent(event);"
                                                    > Leave
                                                    </button>
                                                </div>
                                            @else
                                                <div class="d-flex justify-content-between px-2">
                                                    <small class="text-success">Stay</small>
                                                    <small class="text-red">Leave</small>
                                                </div>
                                            @endif
                                             
                                            <div class="d-flex justify-content-between px-2">
                                                <span>{{$votes['stayCount']}}</span>
                                                </span>{{$votes['leaveCount']}}</span>
                                            </div>
                                            <div class="px-0 mx-0">
                                                <div class="progress d-flex justify-content-between" style="height: 5px; background-color: #f0f0f0;">
                                                    @foreach ($joinEvent->roster as $roster)
                                                        <div 
                                                            class="progress-segment {{ $roster->vote_to_quit === 1 ? 'bg-red' : ($roster->vote_to_quit === 0 ? 'bg-primary' : '') }}"
                                                            style="
                                                                width: {{ 100 / $votes['totalCount'] }}%; 
                                                                position: relative;
                                                                height: 5px;
                                                                cursor: pointer;
                                                                order: {{ $roster->vote_to_quit === 1 ? 1 : ($roster->vote_to_quit === 0 ? -1 : 0) }};
                                                            "
                                                        >
                                                        </div>
                                                    @endforeach

                                                </div>
                                                <div class="d-flex justify-content-between my-1">
                                                   @foreach ($joinEvent->roster as $roster)
                                                        @if ($roster->vote_to_quit !== null)
                                                            <img 
                                                                class="rounded-circle random-color-circle object-fit-cover"
                                                                width="25" 
                                                                height="25" 
                                                                src="{{ $roster->user->userBanner ? asset('storage/' . $roster->user->userBanner) : '/assets/images/404.png' }}" 
                                                                {!! trustedBladeHandleImageFailureBanner() !!}
                                                                style="
                                                                    order: {{ $roster->vote_to_quit === 1 ? 1 : -1 }};
                                                                    cursor: pointer;
                                                                "
                                                                onclick="goToUrl(event, this)"
                                                                data-url="{{ route('public.participant.view', ['id' => $roster->user->id]) }}"
                                                            >  
                                                        @else
                                                              <div 
                                                                class="progress--empty"
                                                            >  
                                                            </div>
                                                        @endif
                                                    @endforeach

                                                </div>
                                                
                                              
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    @if ($joinEvent->status != "confirmed")
                                        <div class="text-end">
                                            <span class="">
                                                <button 
                                                    onclick="approveMemberAction(event);"
                                                    class="z-99 btn btn-sm rounded-pill btn-success mb-2 text-dark me-3"
                                                    data-join-event-id="{{ $joinEvent->id }}"
                                                    data-user-id="{{$user->id}}"

                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                                                    <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
                                                    <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
                                                    </svg>
                                                    Join Roster
                                                </button>
                                            </span>
                                        </div>
                                    @endif
                                    <div class="text-start border border-primary bg-translucent text-dark px-2">
                                        <small class="text-dark">You can freely join/leave events until registration is locked!</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
            </div>
        </a>
        <div class="frame1 p-0 mx-0 mb-0">
            <div class="row mx-0 w-100" style="padding: 5px 10px;">
                <div class="col-6 col-xl-5  my-1 px-0">
                    <a class="d-flex w-100 h-100 justify-content-start align-items-center"
                        href="{{ route('public.event.view', ['id' => $joinEvent->eventDetails->id]) }}">
                        <img {!! trustedBladeHandleImageFailureBanner() !!}
                            src="{{ bladeImageNull($joinEvent->game ? $joinEvent->game?->gameIcon : null) }}"
                            class="object-fit-cover me-2 rounded-2" width="30px" height="30px"
                             style="object-position:center;"    
                        >
                        <span class="text-wrap  d-inline-block  pe-2 text-start"> {{ $joinEvent->eventDetails->eventName }}
                        </span>
                    </a>
                </div>
               <div onclick="goToUrl(event, this)"
                    data-url="{{ route('public.organizer.view', ['id' => $joinEvent->eventDetails->user->id]) }}"
                    class="col-6 col-xl-5 d-flex justify-content-start align-items-center px-0 mx-0 mt-1">
                    <img 
                        {!! trustedBladeHandleImageFailureBanner() !!}
                        src="{{ $joinEvent->eventDetails->user->userBanner ? asset('storage/' . $joinEvent->eventDetails->user->userBanner) : '/assets/images/404.png' }}" 
                        class="object-fit-cover me-2 rounded-circle rounded-circle2" >
                    <div class="text-start d-inline-flex flex-column justify-content-center  ">
                        <small class="d-inline-block my-0 text-wrap ">{{ $joinEvent->eventDetails->user->name }}</small>
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
                    method="POST" 
                    class="col-6 d-none d-xl-flex followFormProfile col-xl-2 my-2 justify-content-end text-end px-0"
                    action="{{ route('participant.organizer.follow') }}">
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
                            class=" {{ 'followButton' . $joinEvent->eventDetails?->user_id }} roster-button ">
                            Follow
                        </button>
                    @endguest
                    @auth
                        @if ($user->role == 'PARTICIPANT')
                            <button type="submit" class="{{ 'followButton' . $joinEvent->eventDetails?->user_id }} roster-button "
                                style="background-color: {{ $joinEvent?->isFollowing ? '#8CCD39' : '#43A4D7' }}; color: {{ $joinEvent?->isFollowing ? 'black' : 'white' }};  ">
                                {{ $joinEvent?->isFollowing ? 'Following' : 'Follow' }}
                            </button>
                        @else
                            <button type="button" 
                                onclick="event.preventDefault(); event.stopPropagation(); toastWarningAboutRole(this, 'Participants can follow only!');"
                                class="roster-button {{ 'followButton' . $joinEvent->user_id }}"
                            >
                                Follow
                            </button>
                        @endif
                    @endauth
                </form>
            </div>
        </div>
        <div class="d-none" 
            id="roster-id-list-{{$joinEvent->id}}"
            data-roster-map="{{json_encode($rosterUserIds)}}"
        >
        </div>
        @php
            $joinEvent->totalRosterCount = $votes['totalCount'];
        @endphp
    </div>
</div>


