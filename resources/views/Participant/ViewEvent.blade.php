<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Event</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/viewEvent.css') }}">
    @include('__CommonPartials.HeadIcon')
    @vite(['resources/js/tippy.js', 'resources/js/pages/bracket.js', 'resources/sass/app.scss', 'resources/js/app.js', 'resources/js/lightgallery.js', 'resources/sass/lightgallery.scss'])
    <title>Tournament Matches </title>
    <link rel="stylesheet" href="{{ asset('/assets/css/common/tournament.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/common/dynamic-select.css') }}">
    <script src="{{ asset('/assets/js/dynamicSelect.js') }}"></script>
</head>

@php
    $status = $event->statusResolved();
    $stylesEventStatus = bladeEventStatusStyleMapping($status);
    $stylesEventRatio = bladeEventRatioStyleMapping($event->registeredParticipants, $event->totalParticipants);
    $tier = $event->tier ? $event->tier?->eventTier : null;
    $eventTierLower = bladeEventTowerLowerClass($tier);
    $dateArray = bladeGenerateEventStartEndDateStr($event->startDate, $event->startTime);
    extract($dateArray);
    $eventTierLowerImg = bladeEventTierImage($tier);
    $eventBannerImg = bladeImageNull($event->eventBanner);
@endphp

<body style="background: none; ">
    @include('__CommonPartials.NavbarGoToSearchPage')
    <input type="hidden" id="signin_url" name="url" value="{{ route('participant.signin.view') }}">
    <input type="hidden" id="create_url" value="{{ route('event.create') }}">
    <input type="hidden" id="edit_url" value="{{ route('event.edit', $event->id) }}">
    <main x-data="alpineDataComponent">
        <br class="d-none-at-desktop">
        <div class="pt-2">
            <header>
                <u>
                    <h5 class="text-start ms-5">
                        View your events
                    </h5>
                </u>
            </header>
        </div>
        <br>
        <div>
            @if ($tier)
                <div class="{{ 'side-image side-image-' . $eventTierLower }} ">
                    <img class="side-image-absolute-top" src="{{ $eventTierLowerImg }}" width="80" height="80">
                </div>
                <div class="{{ 'side-image side-image-' . $eventTierLower }}">
                    <img class="side-image-absolute-bottom" src="{{ $eventTierLowerImg }}" width="80" height="80">
                </div>
            @else
                <div>
                </div>
            @endif
        </div>
        <div class="grid-container">
            <div> </div>
            <div>
                <div>
                    <div class="mx-2 position-relative rounded-banner-parent">
                        <div class="d-flex justify-content-center d-lg-none">
                            <img class="image-at-top" src="{{ $eventTierLowerImg }}" {!! trustedBladeHandleImageFailureResize() !!}
                                width="120" height="90">
                        </div>
                        <a data-fslightbox="lightbox" href="{{ $eventBannerImg }}">

                        <img width="100%" height="auto" style="aspect-ratio: 7/3; object-fit: cover;"
                            @class(['rounded-banner height-image ms-0', 'rounded-box-' . $eventTierLower]) {!! trustedBladeHandleImageFailureBanner() !!} src="{{ $eventBannerImg }}"
                            alt="" data-fslightbox="lightbox">
                            </a>
                        @if ($event->eventBanner)
                        @else
                            <h5>
                                Please enter a banner image.
                            </h5>
                            <br><br>
                        @endif
                    </div>
                </div>
                <div class="grid-container-two-columns-at-desktop">
                    <div class="card-text">
                        <div>
                            <br>
                            <div class="d-flex justify-content-between flex-wrap align-items-start pb-3">
                                <h5 class="text-truncated w-75">
                                    {{ $event->eventName ?? 'No name yet' }}
                                </h5>
                                <div>
                                    <div>
                                        <form class="d-inline" method="POST"
                                            action="{{ route('participant.events.like') }}" id="likesForm">
                                            @auth
                                                <input type="hidden" name="event_id" value="{{$event->id}}">
                                                <input type="hidden" name="user_id" value="{{$user->id}}">
                                                @if ($user->isLiking)
                                                    {{-- Thumbs up icon isLiked --}}
                                                    <span id="likesButton">
                                                        <svg 
                                                            onclick="submitLikesForm()"
                                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#43A4D7" class="bi bi-hand-thumbs-up-fill" viewBox="0 0 16 16">
                                                            <path d="M6.956 1.745C7.021.81 7.908.087 8.864.325l.261.066c.463.116.874.456 1.012.965.22.816.533 2.511.062 4.51a10 10 0 0 1 .443-.051c.713-.065 1.669-.072 2.516.21.518.173.994.681 1.2 1.273.184.532.16 1.162-.234 1.733q.086.18.138.363c.077.27.113.567.113.856s-.036.586-.113.856c-.039.135-.09.273-.16.404.169.387.107.819-.003 1.148a3.2 3.2 0 0 1-.488.901c.054.152.076.312.076.465 0 .305-.089.625-.253.912C13.1 15.522 12.437 16 11.5 16H8c-.605 0-1.07-.081-1.466-.218a4.8 4.8 0 0 1-.97-.484l-.048-.03c-.504-.307-.999-.609-2.068-.722C2.682 14.464 2 13.846 2 13V9c0-.85.685-1.432 1.357-1.615.849-.232 1.574-.787 2.132-1.41.56-.627.914-1.28 1.039-1.639.199-.575.356-1.539.428-2.59z"/>
                                                        </svg>
                                                    </span>
                                                    <span class="me-2 text-primary" id="likesCount" data-count="{{ $likesCount }}">{{ $likesCount }}</span>
                                                @else
                                                    {{-- Thumbs up icon willLike--}}
                                                    <span id="likesButton">
                                                        <svg 
                                                            onclick="submitLikesForm()"
                                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" 
                                                            class="bi bi-hand-thumbs-up svg-hover cursor-pointer" viewBox="0 0 16 16" stroke-width="3">
                                                            <path d="M8.864.046C7.908-.193 7.02.53 6.956 1.466c-.072 1.051-.23 2.016-.428 2.59-.125.36-.479 1.013-1.04 1.639-.557.623-1.282 1.178-2.131 1.41C2.685 7.288 2 7.87 2 8.72v4.001c0 .845.682 1.464 1.448 1.545 1.07.114 1.564.415 2.068.723l.048.03c.272.165.578.348.97.484.397.136.861.217 1.466.217h3.5c.937 0 1.599-.477 1.934-1.064a1.86 1.86 0 0 0 .254-.912c0-.152-.023-.312-.077-.464.201-.263.38-.578.488-.901.11-.33.172-.762.004-1.149.069-.13.12-.269.159-.403.077-.27.113-.568.113-.857 0-.288-.036-.585-.113-.856a2 2 0 0 0-.138-.362 1.9 1.9 0 0 0 .234-1.734c-.206-.592-.682-1.1-1.2-1.272-.847-.282-1.803-.276-2.516-.211a10 10 0 0 0-.443.05 9.4 9.4 0 0 0-.062-4.509A1.38 1.38 0 0 0 9.125.111zM11.5 14.721H8c-.51 0-.863-.069-1.14-.164-.281-.097-.506-.228-.776-.393l-.04-.024c-.555-.339-1.198-.731-2.49-.868-.333-.036-.554-.29-.554-.55V8.72c0-.254.226-.543.62-.65 1.095-.3 1.977-.996 2.614-1.708.635-.71 1.064-1.475 1.238-1.978.243-.7.407-1.768.482-2.85.025-.362.36-.594.667-.518l.262.066c.16.04.258.143.288.255a8.34 8.34 0 0 1-.145 4.725.5.5 0 0 0 .595.644l.003-.001.014-.003.058-.014a9 9 0 0 1 1.036-.157c.663-.06 1.457-.054 2.11.164.175.058.45.3.57.65.107.308.087.67-.266 1.022l-.353.353.353.354c.043.043.105.141.154.315.048.167.075.37.075.581 0 .212-.027.414-.075.582-.05.174-.111.272-.154.315l-.353.353.353.354c.047.047.109.177.005.488a2.2 2.2 0 0 1-.505.805l-.353.353.353.354c.006.005.041.05.041.17a.9.9 0 0 1-.121.416c-.165.288-.503.56-1.066.56z"/>
                                                        </svg>
                                                    </span>
                                                    <span class="me-2" id="likesCount" data-count="{{ $likesCount }}">{{ $likesCount }}</span>
                                                @endif
                                            @endauth
                                            @guest
                                                <svg 
                                                    onclick="reddirectToLoginWithIntened('{{route('public.event.view', ['id'=> $event->id])}}')"
                                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" 
                                                    class="bi bi-hand-thumbs-up svg-hover cursor-pointer" viewBox="0 0 16 16" stroke-width="3">
                                                    <path d="M8.864.046C7.908-.193 7.02.53 6.956 1.466c-.072 1.051-.23 2.016-.428 2.59-.125.36-.479 1.013-1.04 1.639-.557.623-1.282 1.178-2.131 1.41C2.685 7.288 2 7.87 2 8.72v4.001c0 .845.682 1.464 1.448 1.545 1.07.114 1.564.415 2.068.723l.048.03c.272.165.578.348.97.484.397.136.861.217 1.466.217h3.5c.937 0 1.599-.477 1.934-1.064a1.86 1.86 0 0 0 .254-.912c0-.152-.023-.312-.077-.464.201-.263.38-.578.488-.901.11-.33.172-.762.004-1.149.069-.13.12-.269.159-.403.077-.27.113-.568.113-.857 0-.288-.036-.585-.113-.856a2 2 0 0 0-.138-.362 1.9 1.9 0 0 0 .234-1.734c-.206-.592-.682-1.1-1.2-1.272-.847-.282-1.803-.276-2.516-.211a10 10 0 0 0-.443.05 9.4 9.4 0 0 0-.062-4.509A1.38 1.38 0 0 0 9.125.111zM11.5 14.721H8c-.51 0-.863-.069-1.14-.164-.281-.097-.506-.228-.776-.393l-.04-.024c-.555-.339-1.198-.731-2.49-.868-.333-.036-.554-.29-.554-.55V8.72c0-.254.226-.543.62-.65 1.095-.3 1.977-.996 2.614-1.708.635-.71 1.064-1.475 1.238-1.978.243-.7.407-1.768.482-2.85.025-.362.36-.594.667-.518l.262.066c.16.04.258.143.288.255a8.34 8.34 0 0 1-.145 4.725.5.5 0 0 0 .595.644l.003-.001.014-.003.058-.014a9 9 0 0 1 1.036-.157c.663-.06 1.457-.054 2.11.164.175.058.45.3.57.65.107.308.087.67-.266 1.022l-.353.353.353.354c.043.043.105.141.154.315.048.167.075.37.075.581 0 .212-.027.414-.075.582-.05.174-.111.272-.154.315l-.353.353.353.354c.047.047.109.177.005.488a2.2 2.2 0 0 1-.505.805l-.353.353.353.354c.006.005.041.05.041.17a.9.9 0 0 1-.121.416c-.165.288-.503.56-1.066.56z"/>
                                                </svg>
                                                <span class="me-2" id="likesCount" data-count="{{ $likesCount }}">{{ $likesCount }}</span>
                                            @endguest
                                        </form>
                                        {{-- Share icon --}}
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" class="feather feather-share-2 svg-hover">
                                            <circle cx="18" cy="5" r="3"></circle>
                                            <circle cx="6" cy="12" r="3"></circle>
                                            <circle cx="18" cy="19" r="3"></circle>
                                            <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                                            <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="flexbox-centered-space card-subtitle">
                                <div class="flexbox-centered-space">
                                    <img style="display: inline;" src="{{ asset('/assets/images/menu.png') }}"
                                        class="{{ 'rounded-image rounded-box-' . $eventTierLower }}" alt="menu">
                                    &nbsp;
                                    <div class="card-organizer">
                                        <p style="display: inline;"><u>
                                                {{ $event?->user?->organizer?->companyName ?? 'Add' }} </u> </p>
                                        <p class="small-text" id="followCount" data-count="{{ $followersCount }}">
                                            <i> {{ $followersCount }}
                                                {{ $followersCount == 1 ? 'follower' : 'followers' }} </i> </p>
                                    </div>
                                </div>

                                <form id="followForm" method="POST"
                                    action="{{ route('participant.organizer.follow') }}">
                                    @csrf
                                    <input type="hidden" name="role"
                                        value="{{ $user && $user->id ? $user->role : '' }}">
                                    <input type="hidden" name="user_id"
                                        value="{{ $user && $user->id ? $user->id : '' }}">
                                    <input type="hidden" name="organizer_id"
                                        value="{{ $event?->user_id }}">
                                    @guest
                                        <button
                                            class=""
                                            type="button"
                                            onclick="reddirectToLoginWithIntened('{{route('public.event.view', ['id'=> $event->id])}}')"
                                            id="followButton"
                                            style="background-color: #43A4D7; color: white;  padding: 5px 10px; font-size: 0.875rem; border-radius: 10px; border: none;">
                                            Follow
                                        </button>
                                    @endguest
                                    @auth
                                        @if ($user->role == 'PARTICIPANT')
                                            <button class="" type="submit" id="followButton"
                                                style="background-color: {{ $user && $user->isFollowing ? '#8CCD39' : '#43A4D7' }}; color: {{ $user && $user->isFollowing ? 'black' : 'white' }};  padding: 5px 10px; font-size: 0.875rem; border-radius: 10px; border: none;">
                                                {{ $user && $user->isFollowing ? 'Following' : 'Follow' }}
                                            </button>
                                        @else
                                            <button class="" type="button"
                                                onclick="toastWarningAboutRole(this, 'Participants can follow only!');"
                                                id="followButton"
                                                style="background-color: #43A4D7; color: white;  padding: 5px 10px; font-size: 0.875rem; border-radius: 10px; border: none;">
                                                Follow
                                            </button>
                                        @endif
                                    @endauth
                                </form>
                            </div>
                            <br>
                            <div>
                                <h5> <u> {{ $combinedStr }} </u> </h5>
                                <h5> <u> {{ strtoupper($timePart) }} </u> </h5>
                            </div>

                            <br>

                        </div>
                    </div>
                    <div class="ps-3">
                        <br>
                        @if (session('errorMessage'))
                            <div class="error-message mt-0">
                                {{ session('errorMessage') }}
                            </div>
                        @endif

                        <form method="POST" name="joinForm" action="{{ route('participant.event.selectOrCreateTeam.redirect', ['id' => $event->id]) }}">
                            @csrf
                            @if ($existingJoint)
                                <button type="button" class="oceans-gaming-default-button " disabled>
                                    <span>Joined</span>
                                </button>
                                <br><br>
                                <a href="{{route('participant.register.manage', ['id' => $existingJoint->team_id])}}"><u>Manage registration</u></a>
                            @else
                                @guest
                                    <button 
                                        type="button"
                                        onclick="reddirectToLoginWithIntened('{{route('public.event.view', ['id'=> $event->id])}}')"
                                        class="oceans-gaming-default-button ">
                                        <span>Join</span>
                                    </button>
                                @endguest
                                @auth
                                    @if ($user->role == 'PARTICIPANT')
                                        <button type="submit" class="oceans-gaming-default-button ">
                                            <span>Join</span>
                                        </button>
                                    @else
                                        <button 

                                            onclick="toastWarningAboutRole(this, 'Participants can join only!');"
                                            type="button" class="oceans-gaming-default-button "
                                        >
                                            <span>Join</span>
                                        </button>
                                    @endif
                                @endauth
                            @endif
                        </form>

                            <div class="pt-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="svg-fix feather feather-user">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                &nbsp;
                                @if ($event->tier)
                                    <span style="position: relative; top: 5px;"> RM
                                        {{ $event->tier?->tierPrizePool ?? 'No Prize' }} Prize Pool</span>
                                @else
                                    <p>Tier PrizePool: Not available</p>
                                @endif
                            </div>
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="feather feather-dollar-sign svg-fix">
                                    <line x1="12" y1="1" x2="12" y2="23"></line>
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                </svg>
                                &nbsp;
                                @if ($event->tier)
                                    <span style="position: relative; top: 5px;">RM
                                        {{ $event->tier?->tierEntryFee ?? 'Free' }} Entry Fees</span>
                                @else
                                    <p>Tier Entry Fee: Not available</p>
                                @endif
                            </div>
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="svg-fix feather feather-map-pin">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                                &nbsp;
                                <span style="position: relative; top: 5px;">{{ $event->venue ?? 'SEA' }}</span>
                            </div>
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="svg-fix feather feather-bar-chart-2">
                                    <line x1="18" y1="20" x2="18" y2="10"></line>
                                    <line x1="12" y1="20" x2="12" y2="4"></line>
                                    <line x1="6" y1="20" x2="6" y2="14"></line>
                                </svg>
                                &nbsp;
                                <span style="position: relative; top: 5px;">{{ $event->join_events_count }}/{{ $event->tier?->tierTeamSlot ?? 'Not Available' }}</span>
                            </div>
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="svg-fix feather feather-info">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="16" x2="12" y2="12"></line>
                                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                </svg>
                                &nbsp;
                                <span style="position: relative; top: 5px;">{{ $tier ?? 'Choose event type' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div> </div>

        </div>
        
    </div>
    <div class="grid-container">
            <div></div>
            <div>
                <div class="tab ms-0 position-relative" style="width: max(60vw, 95%); left: min(2%, 10px); top:20px;">
                    <button class="{{ 'side-image-' . $eventTierLower . ' tablinks active ' }}"
                        onclick="openTab(event, 'Overview'); ">Overview</button>
                    <button class="{{ 'side-image-' . $eventTierLower . ' tablinks ' }}"
                        onclick="openTab(event, 'Bracket', 'bracket-list'); ">Bracket</button>
                    <button class="{{ 'side-image-' . $eventTierLower . ' tablinks ' }}"
                        onclick="openTab(event, 'Teams'); ">Teams</button>
                    <button class="{{ 'side-image-' . $eventTierLower . ' tablinks ' }}"
                        onclick="openTab(event, 'Result'); ">Result</button>
                </div>
                <br>
                <div id="Overview" class="tabcontent" style="display: block;">
                    <h5><u>About this event</u></h5>
                    <p>{{ $event->eventDescription ?? 'Not added description yet' }} </p>
                </div>

                <div id="Bracket" class="tabcontent">

                    @include('Participant.__Partials.BracketReport')

                </div>

                <div id="Teams" class="tabcontent">
                    <h5 class="my-0"><u>Teams</u></h5>
                     <div style="width: 90%;">
                        @if (isset($teamList[0]))
                            <br>
                            <table id="current_teams" class="member-table responsive ">
                                <thead>
                                    <tr>
                                        <th> </th>
                                        <th>Team name</th>
                                        <th>Description</th>
                                        <th>Region</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($teamList as $team)
                                        <tr class="st">
                                            <td style="width: 60px !important;" class="py-0 px-0 mx-0"> 
                                                <a href="{{route('public.team.view', ['id' => $team->id])}}"> 
                                                    <svg class="gear-icon-btn"
                                                        xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                                        class="bi bi-eye-fill" viewBox="0 0 16 16">
                                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                                        <path
                                                            d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                                    </svg>
                                                </a>
                                            </td>
                                            <td class="d-flex align-items-center colored-cell">
                                                <img
                                                    class="rounded-circle d-inline-block object-fit-cover me-3"
                                                    src="{{ '/storage' . '/'. $team->teamBanner }}"
                                                    {!! trustedBladeHandleImageFailure() !!} 
                                                    height="40"
                                                    width="40"
                                                > 
                                                <span>{{$team->teamName}}</span>
                                            </td>
                                            <td class="colored-cell text-start">{{$team->teamDescription}}</td>
                                            <td style="font-size: 1.5rem;" class="colored-cell">{{$team->country_flag}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="pt-3" style="width: 90%;">No current teams</div>
                        @endif
                    </div>
                </div>
                <div id="Result" class="tabcontent">
                    <h5><u>Result</u></h5>
                    <p>Result tab.</p>
                </div>
            </div>
            <div></div>
        </div>
    </main>
     @livewireScripts
    <script src="{{ asset('/assets/js/participant/ViewEvent.js') }}"></script>
    <script src="{{ asset('/assets/js/shared/tournament.js') }}"></script>
   
</html>
