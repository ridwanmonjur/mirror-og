<!DOCTYPE html>
<html lang="en">

@php
    $status = $event->statusResolved();
    $stylesEventRatio = bldRtMap($event->registeredParticipants, $event->totalParticipants);
    $isEarly = false;
    $entryFee = 0;
    $regStatus = $event->getRegistrationStatus();
    if ($event->tier) {
        $tier = $event->tier->eventTier;
        $icon = $event->tier->tierIcon;
        $entryFee = $event->tier->tierEntryFee;
        if ($regStatus == config('constants.SIGNUP_STATUS.EARLY')) {
            $entryFee = $event->tier->earlyEntryFee ;
            $isEarly = true;
        }
    } else {
        $tier = $icon = null;
        $entryFee = 0;
    }
    $type = $event->type ? $event->type->eventType : null;
    $eventTierLower = bldLowerTIer($tier);
    $dateArray = $event->startDatesStr($event->startDate, $event->startTime);
    extract($dateArray);
    $eventTierLowerImg = bldImg($icon);
    $eventBannerImg = bldImg($event->eventBanner);
    $userId = isset($user) ? $user->id : null;
    $regStatus = $event->getRegistrationStatus();
    $entryFee = $regStatus == config('constants.SIGNUP_STATUS.EARLY') ? $event->tier?->earlyEntryFee : $event->tier?->tierEntryFee;

    $eventDataAttributes = trim(implode(' ', array_filter([
        $event->tier?->eventTier ? 'data-event-tier="' . $event->tier->eventTier . '"' : '',
        $event->type?->eventType ? 'data-event-type="' . $event->type->eventType . '"' : '',
        $event->game?->gameTitle ? 'data-esport-title="' . $event->game->gameTitle . '"' : '',
        $event->venue ? 'data-location="' . $event->venue . '"' : '',
        $event->tier?->id ? 'data-tier-id="' . $event->tier->id . '"' : '',
        $event->type?->id ? 'data-type-id="' . $event->type->id . '"' : '',
        $event->game?->id ? 'data-game-id="' . $event->game->id . '"' : '',
        $event->user?->id ? 'data-user-id="' . $event->user->id . '"' : ''
    ])));
    // dd($bracketList, $pagination, $roundNames);
    $showBracketFirst = isset($pagination) && isset($pagination['current_page']) && $pagination['current_page'] > 1;

@endphp 
<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @if (!in_array($event->status, ['DRAFT', 'PREVIEW', 'PENDING'])) 
        <meta name="analytics" content="enabled">
    @endif
    <title>{{ $event->eventName }} | {{ $tier ?? 'Tournament' }} {{ $type ?? 'Event' }} - RM {{ $event->tier?->tierPrizePool ?? '0' }} Prize Pool | Driftwood GG</title>
        <!-- Essential Meta Tags -->
    <meta name="description" content="{{ Str::limit($event->eventDescription, 155) ?? 'Join ' . $event->eventName . ' - ' . ($event->tier?->tierPrizePool ? 'RM ' . $event->tier?->tierPrizePool . ' Prize Pool' : '') . ' | ' . $type . ' event starting ' . $combinedStr }}">
    <meta name="keywords" content="{{ $event->eventName }}, {{ $event->game?->name ?? 'gaming' }}, esports tournament, {{ $tier ?? 'tournament' }}, {{ $event->venue ?? 'SEA' }}, gaming event">

    <!-- Open Graph Tags for Social Sharing -->
    <meta property="og:title" content="{{ $event->eventName }} - {{ $tier ?? 'Event' }} Tournament">
    <meta property="og:description" content="{{ Str::limit($event->eventDescription, 155) }}">
    <meta property="og:image" content="{{ asset($eventBannerImg) }}">
    <link rel="canonical" href="{{ route('public.event.view', $event->id) }}">
    <meta property="og:url" content="{{ route('public.event.view', $event->id) }}">
    <meta name="title" content="{{ $event->eventName }}">
    <meta property="og:type" content="event">
    
    <!-- Analytics Data -->
    <meta name="event-id" content="{{ $event->id }}">
    <meta name="event-name" content="{{ $event->eventName }}">
    <meta property="og:site_name" content="Driftwood GG">

    <!-- Twitter Card Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $event->eventName }}">
    <meta name="twitter:description" content="{{ Str::limit($event->eventDescription, 155) }}">
    <meta name="twitter:image" content="{{ asset($eventBannerImg) }}">

    <!-- Additional SEO Tags -->
    <meta name="author" content="{{ $event->user->name }}">
        <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('/assets/css/common/viewEvent.css') }}">
        @include('includes.HeadIcon')
        @vite([ 'resources/sass/app.scss',
            'resources/js/app.js',
            'resources/js/alpine/bracket.js',
            'resources/js/custom/share.js'
        ])
    </head>

    <link rel="alternate" type="application/atom+xml" title="Latest Esports Events" href="{{ route('feeds.events') }}" />
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Event",
            "name": "{{ $event->eventName }}",
            "description": "{{ $event->eventDescription }}",
            "image": "{{ asset($eventBannerImg) }}",
            "startDate": "{{ $event->startDate }}T{{ $event->startTime }}",
            "endDate": "{{ $event->endDate ?? $event->startDate }}",
            "eventStatus": "https://schema.org/EventScheduled",
            "eventAttendanceMode": "https://schema.org/{{ $event->venue == 'Online' ? 'OnlineEventAttendanceMode' : 'Global' }}",
            "location": {
                "@type": "{{ $event->venue == 'Online' ? 'VirtualLocation' : 'Global' }}",
                "name": "{{ $event->venue }}",
                "address": "{{ $event->venue }}"
            },
            "organizer": {
                "@type": "Person",
                "name": "{{ $event->user->name }}",
                "url": "{{ route('public.organizer.view', ['id'=> $event->user->id, 'title' => $event->user->slug]) }}"
            },
            "offers": {
                "@type": "Offer",
                "price": "{{ $event->tier?->tierEntryFee ?? '0' }}",
                "priceCurrency": "RM",
                "availability": "https://schema.org/InStock",
                "validFrom": "{{ now()->toIso8601String() }}"
            },
            "performer": {
                "@type": "SportsTeam",
                "name": "Multiple Teams"
            }
        }
    </script>
<body>
    @include('googletagmanager::body')
    <div class="scroll-indicator"></div>
    @include('includes.Navbar')
    <div class="d-none" id="analytics-data" 
        data-event-id="{{$event->id}}"
        data-event-name="{{ $event->eventName }}"
        
        {!! $eventDataAttributes !!}
    >
    </div>
    <div class="d-none" id="bracket-report-data"
        data-event-id="{{$event->id}}"
        data-event-type="{{ $event->type->eventType }}"
        data-round-names="{{ json_encode($roundNames) }}"
        data-previous-values="{{ json_encode($previousValues) }}"
        data-join-event-team-id="{{$existingJoint?->team_id }}"
        data-user-level-enums="{{json_encode($USER_ACCESS)}}"
        data-dispute-level-enums="{{json_encode($DISPUTE_ACCESS)}}"
        data-hidden-user-id="{{ $userId }}"
        data-user-role="{{ $user?->role ?? 'PARTICIPANT' }}"
        data-games-per-match="{{$event?->game?->games_per_match ?? 3}}"
    >
    </div>
    @if ($isEarly)
    <div class="text-center discount-announceMent bg-primary mx-auto text-white fw-bold  ">
        <small>Save with our early bird pricing! Discount ends {{
            (new DateTime($event->signup->normal_signup_start_advanced_close))
            ->setTimezone(new DateTimeZone('Asia/Kuala_Lumpur'))
            ->format('d M y g:i A')
        }}</small>
    </div>
    @endif
    <input type="hidden" id="signin_url" name="url" value="{{ route('participant.signin.view') }}">
    <input type="hidden" id="create_url" value="{{ route('event.create') }}">
    <input type="hidden" id="edit_url" value="{{ route('event.edit', $event->id) }}">
    <input type="hidden" id="hidden_user_id" value="{{ $userId }}">
    <main >
        <br class="d-none-at-desktop">
        
        <div>
            @if ($tier)
                <div class="{{ 'side-image side-image-' . $eventTierLower }} ">
                    <img   alt="{{ $eventTierLowerImg }}" class="side-image-absolute-top " src="{{ $eventTierLowerImg }}" width="80" height="80">
                </div>
                <div class="{{ 'side-image side-image-' . $eventTierLower }}">
                    <img   alt="{{ $eventTierLowerImg }}" class="side-image-absolute-bottom slideInRight" src="{{ $eventTierLowerImg }}" width="80" height="80">
                </div>
            @else
                <div>
                </div>
            @endif
        </div>
        <div class="grid-container">
            <div> </div>
            <div>
                <div class="py-3">
            <header>
                    <h5 class=" py-0 my-0">
                     <u>
                        View your events
                        </u>
                    </h5>
                </u>
            </header>
        </div>
                <div>
                    <div class="position-relative rounded-banner-parent">
                        <div class="d-flex justify-content-center d-lg-none">
                            <img   alt="{{ $eventTierLowerImg }}" class="image-at-top" src="{{ $eventTierLowerImg }}"
                                onerror="this.onerror=null;this.width='500px';this.height='50px';this.src='{{asset('assets/images/404.png')}}';"
                                width="120" height="90"
                            >
                        </div>
                        <a data-fslightbox="lightbox" data-href="{{ $eventBannerImg }}">
                            <img   
                                id="eventBannerImg"
                                alt="{{ $event->eventName }}"  
                                width="500"
                                height="500"
                                @class([' event-image rounded-banner  ms-0 cursor-pointer ', ' rounded-box-' . $eventTierLower]) 
                                onerror="this.onerror=null;this.src='{{asset('assets/images/404.png')}}';" 
                                src="{{ $eventBannerImg }}"
                            >
                        </a>

                    </div>
                </div>
                <div>
                    <div class="grid-container-two-columns-at-desktop ">
                        <div class="card-text ">
                            <div>
                                <br class="d-none d-lg-block">
                                <div class="d-flex pt-2 justify-content-between flex-wrap align-items-start pb-3">
                                    <h1 class="text-wrap w-75 fs-5 my-0 py-0 text-start">
                                        {{ $event->eventName ?? 'No name yet' }}
                                    </h1>
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
                                                                onclick="submitLikesForm(event)"
                                                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#43A4D7" class="bi bi-hand-thumbs-up-fill" viewBox="0 0 16 16">
                                                                <path d="M6.956 1.745C7.021.81 7.908.087 8.864.325l.261.066c.463.116.874.456 1.012.965.22.816.533 2.511.062 4.51a10 10 0 0 1 .443-.051c.713-.065 1.669-.072 2.516.21.518.173.994.681 1.2 1.273.184.532.16 1.162-.234 1.733q.086.18.138.363c.077.27.113.567.113.856s-.036.586-.113.856c-.039.135-.09.273-.16.404.169.387.107.819-.003 1.148a3.2 3.2 0 0 1-.488.901c.054.152.076.312.076.465 0 .305-.089.625-.253.912C13.1 15.522 12.437 16 11.5 16H8c-.605 0-1.07-.081-1.466-.218a4.8 4.8 0 0 1-.97-.484l-.048-.03c-.504-.307-.999-.609-2.068-.722C2.682 14.464 2 13.846 2 13V9c0-.85.685-1.432 1.357-1.615.849-.232 1.574-.787 2.132-1.41.56-.627.914-1.28 1.039-1.639.199-.575.356-1.539.428-2.59z"/>
                                                            </svg>
                                                        </span>
                                                        <span class="me-2 text-primary" id="likesCount" data-count="{{ $likesCount }}">{{ $likesCount }}</span>
                                                    @else
                                                        {{-- Thumbs up icon willLike--}}
                                                        <span id="likesButton">
                                                            <svg
                                                                onclick="submitLikesForm(event)"
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
                                                data-event-id="{{$event->id}}"
                                                data-event-name="{{ $event->eventName }}"
                                                {!! $eventDataAttributes !!}
                                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-link-45deg share-button" viewBox="0 0 16 16">
                                                <path d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1 1 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4 4 0 0 1-.128-1.287z"/>
                                                <path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 1 0-4.243-4.243z"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div class="flexbox-centered-space card-subtitle me-2">
                                    <div class="flexbox-centered-space">
                                        @if (isset($event->user) && isset($event->user->userBanner))
                                            <img   alt="User Banner"  src="{{ asset('storage/'.$event->user->userBanner) }}"
                                                onerror="this.src='/assets/images/404.png';"
                                                class="{{ 'rounded-image d-inline rounded-box-' . $eventTierLower }}" alt="menu"
                                            >
                                        @else
                                            <div class="{{ 'rounded-image rounded-box-' . $eventTierLower }}" alt="menu"> </div>
                                        @endif
                                        &nbsp;
                                        <div class="card-organizer d-flex ms-2 justify-content-center flex-col">
                                            <a href="{{route('public.organizer.view', ['id'=> $event->user->id, 'title' => $event->user->slug ])}}">
                                                <p style="display: inline;"><u>{{ $event->user->name ?? 'Add' }} </u> </p>
                                            </a>
                                            <p class="small-text m-0" id="followCount" data-count="{{ $followersCount }}">
                                                <i> {{ $followersCount }}
                                                    {{ $followersCount == 1 ? 'follower' : 'followers' }}
                                                </i>
                                            </p>
                                        </div>
                                    </div>

                                    @if ($livePreview)
                                        <button type="button" onclick="goToEditScreen();" class="btn btn-link">
                                            <u>Resume changing this event....</u>
                                        </button>
                                    @else
                                        <form id="followForm" method="POST"
                                            action="{{ route('participant.organizer.follow') }}"
                                            data-event-id="{{ $event->id }}"
                                            data-event-name="{{ $event->eventName }}"
                                            {!! $eventDataAttributes !!}
                                        >
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
                                    @endif
                                </div>
                                <br>
                                <div>
                                    <h5> <u> {{ $combinedStr }} </u> </h5>
                                    <h5> <u> {{ $timePart }} </u> </h5>
                                </div>

                                <br class="d-none d-lg-block">

                            </div>
                        </div>
                        <div class="card-heading">
                            <br class="d-none d-lg-block">
                            @if (session('errorMessage'))
                                <div class="error-message mt-0">
                                    {{ session('errorMessage') }}
                                </div>
                            @endif

                            <form method="POST" name="joinForm" action="{{ route('participant.event.selectOrCreateTeam.redirect', ['id' => $event->id]) }}"
                                data-event-id="{{ $event->id }}"
                                data-event-name="{{ $event->eventName }}"
                                {!! $eventDataAttributes !!}
                            >
                                @csrf
                                @if ($existingJoint)
                                    <a href="{{route('participant.register.manage', ['id' => $existingJoint->team_id, 'scroll' => $existingJoint->id])}}"
                                        class="oceans-gaming-default-button bg-success",
                                    >
                                            <span>Joined</span>
                                    </a>
                                    @if ($existingJoint->join_status == "pending")

                                        <a class="text-success d-block mt-3 fw-bold w-100" href="{{route('participant.register.manage',
                                        ['id' => $existingJoint->team_id, 'scroll' => $existingJoint->id]
                                            )}}">
                                           
                                            <span class="fs-7"> Click to confirm your registration.</span>
                                        </a>
                                    @elseif  ($existingJoint->join_status == "confirmed")
                                        <a class="mt-2 fs-7 d-block text-success fw-bold" href="{{route('participant.register.manage',
                                        ['id' => $existingJoint->team_id, 'scroll' => $existingJoint->id]
                                            )}}"><span class="fs-7">Click to manage your registration.</span>
                                        </a>

                                    @elseif  ($existingJoint->join_status == "canceled")
                                        <a class="mt-2 fw-bold d-block" href="{{route('participant.register.manage',
                                        ['id' => $existingJoint->team_id, 'scroll' => $existingJoint->id]
                                            )}}"><span class="fs-7">Your registration is canceled. Click to view.</span>
                                        </a>
                                    @endif

                                @else
                                    @guest
                                        <button
                                            type="button"
                                            onclick="reddirectToLoginWithIntened('{{route('public.event.view', ['id'=> $event->id])}}')"
                                            class="oceans-gaming-default-button glow-effect">
                                            <span>Join</span>
                                        </button>
                                    @endguest
                                    @auth
                                        @if ($user->role == 'PARTICIPANT')
                                            <button type="submit" class="oceans-gaming-default-button glow-effect">
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
                                <div class="pt-2 pb-1 d-flex justify-content-start align-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-controller me-2" viewBox="0 0 16 16">
                                    <path d="M11.5 6.027a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0m-1.5 1.5a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1m2.5-.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0m-1.5 1.5a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1m-6.5-3h1v1h1v1h-1v1h-1v-1h-1v-1h1z"/>
                                    <path d="M3.051 3.26a.5.5 0 0 1 .354-.613l1.932-.518a.5.5 0 0 1 .62.39c.655-.079 1.35-.117 2.043-.117.72 0 1.443.041 2.12.126a.5.5 0 0 1 .622-.399l1.932.518a.5.5 0 0 1 .306.729q.211.136.373.297c.408.408.78 1.05 1.095 1.772.32.733.599 1.591.805 2.466s.34 1.78.364 2.606c.024.816-.059 1.602-.328 2.21a1.42 1.42 0 0 1-1.445.83c-.636-.067-1.115-.394-1.513-.773-.245-.232-.496-.526-.739-.808-.126-.148-.25-.292-.368-.423-.728-.804-1.597-1.527-3.224-1.527s-2.496.723-3.224 1.527c-.119.131-.242.275-.368.423-.243.282-.494.575-.739.808-.398.38-.877.706-1.513.773a1.42 1.42 0 0 1-1.445-.83c-.27-.608-.352-1.395-.329-2.21.024-.826.16-1.73.365-2.606.206-.875.486-1.733.805-2.466.315-.722.687-1.364 1.094-1.772a2.3 2.3 0 0 1 .433-.335l-.028-.079zm2.036.412c-.877.185-1.469.443-1.733.708-.276.276-.587.783-.885 1.465a14 14 0 0 0-.748 2.295 12.4 12.4 0 0 0-.339 2.406c-.022.755.062 1.368.243 1.776a.42.42 0 0 0 .426.24c.327-.034.61-.199.929-.502.212-.202.4-.423.615-.674.133-.156.276-.323.44-.504C4.861 9.969 5.978 9.027 8 9.027s3.139.942 3.965 1.855c.164.181.307.348.44.504.214.251.403.472.615.674.318.303.601.468.929.503a.42.42 0 0 0 .426-.241c.18-.408.265-1.02.243-1.776a12.4 12.4 0 0 0-.339-2.406 14 14 0 0 0-.748-2.295c-.298-.682-.61-1.19-.885-1.465-.264-.265-.856-.523-1.733-.708-.85-.179-1.877-.27-2.913-.27s-2.063.091-2.913.27"/>
                                    </svg>

                                    @if ($event->game)
                                        <span  class=" text-wrap"> 
                                            {{ $event->game?->gameTitle . " (" . $event->game?->player_per_team .'v' . $event->game?->player_per_team . ")" ?? 'No Title' }}
                                        </span>
                                    @else
                                        <span>Not available</span>
                                    @endif
                                </div>
                                <div class="d-flex justify-content-start align-items-center pb-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-trophy me-2" viewBox="0 0 16 16">
                                    <path d="M2.5.5A.5.5 0 0 1 3 0h10a.5.5 0 0 1 .5.5q0 .807-.034 1.536a3 3 0 1 1-1.133 5.89c-.79 1.865-1.878 2.777-2.833 3.011v2.173l1.425.356c.194.048.377.135.537.255L13.3 15.1a.5.5 0 0 1-.3.9H3a.5.5 0 0 1-.3-.9l1.838-1.379c.16-.12.343-.207.537-.255L6.5 13.11v-2.173c-.955-.234-2.043-1.146-2.833-3.012a3 3 0 1 1-1.132-5.89A33 33 0 0 1 2.5.5m.099 2.54a2 2 0 0 0 .72 3.935c-.333-1.05-.588-2.346-.72-3.935m10.083 3.935a2 2 0 0 0 .72-3.935c-.133 1.59-.388 2.885-.72 3.935M3.504 1q.01.775.056 1.469c.13 2.028.457 3.546.87 4.667C5.294 9.48 6.484 10 7 10a.5.5 0 0 1 .5.5v2.61a1 1 0 0 1-.757.97l-1.426.356a.5.5 0 0 0-.179.085L4.5 15h7l-.638-.479a.5.5 0 0 0-.18-.085l-1.425-.356a1 1 0 0 1-.757-.97V10.5A.5.5 0 0 1 9 10c.516 0 1.706-.52 2.57-2.864.413-1.12.74-2.64.87-4.667q.045-.694.056-1.469z"/>
                                    </svg>

                                    @if ($event->tier)
                                        <span > RM
                                            {{ $event->tier?->tierPrizePool . ' Prize Pool' ?? 'No Prize' }} 
                                        </span>
                                    @else
                                        <span>Not available</span>
                                    @endif
                                </div>
                                <div class="d-flex justify-content-start align-items-center pb-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="me-2 feather feather-user">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>

                                    @if ($event->tier)
                                        @if ($isEarly )
                                            <div class="d-inline cursor-pointer" data-bs-toggle="tooltip" title="Early Bird Discount! Ends {{
                                                date('d M y g:i A', strtotime($event->signup->normal_signup_start_advanced_close) + (8 * 3600))
                                            }}.">
                                            <span class="text-decoration-line-through me-1"> RM {{ $event->tier->tierEntryFee }}</span>
                                            <span class="text-primary has-discount">RM {{ $entryFee }}</span>
                                            </div>
                                            <svg data-bs-toggle="tooltip" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#43a4d7" class="bi ms-2 bi-question-circle" viewBox="0 0 16 16"
                                                title="Early Bird Discount! Ends {{
                                                    date('d M y g:i A', strtotime($event->signup->normal_signup_start_advanced_close) + (8 * 3600))
                                                }}."
                                            >
                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                            <path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286m1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94"/>
                                            </svg>
                                        @else
                                            <span class="fw-bold">RM {{ $entryFee }} </span>
                                        @endif
                                    @else
                                        <span>RM 0</span>

                                    @endif
                                </div>
                                <div class="d-flex justify-content-start align-items-center pb-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="me-2 feather feather-map-pin">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                        <circle cx="12" cy="10" r="3"></circle>
                                    </svg>
                                    <span >{{ $event->venue ?? 'SEA' }}</span>
                                </div>
                                <div class="d-flex justify-content-start align-items-center pb-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="me-2 feather feather-info">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="12" y1="16" x2="12" y2="12"></line>
                                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                    </svg>
                                    <span >{{ $type ?? 'Not available' }} 
                                        @if ($event?->game->games_per_match)
                                            (Best of {{$event->game->games_per_match}})
                                        @endif
                                    </span>
                                </div>
                                <div class="d-flex justify-content-start align-items-center pb-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class=" feather feather-bar-chart-2 me-2">
                                        <line x1="18" y1="20" x2="18" y2="10"></line>
                                        <line x1="12" y1="20" x2="12" y2="4"></line>
                                        <line x1="6" y1="20" x2="6" y2="14"></line>
                                    </svg>
                                    <span >{{ $event->join_events_count }}/{{ $event->tier?->tierTeamSlot ?? '0' }}</span>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div>
                <div class="tab ms-0 position-relative tab-viewEvent" >
                    <button class="{{ 'side-image-' . $eventTierLower . ' tablinks ' . ($showBracketFirst ? '' : 'active') }}"
                        onclick=" openTab(event, 'Overview', 'current-title'); closeAllTippy();  ">Overview</button>
                    <button class=" loading {{ 'side-image-' . $eventTierLower . ' tablinks ' . ($showBracketFirst ? 'active' : '') }}"
                        id="tabLoading"
                        onclick="
                            if (checkIfLoading(event)) { openTab(event, 'Bracket', 'bracket-list'); openAllTippy(); }
                        ">Bracket</button>
                    <button class="{{ 'side-image-' . $eventTierLower . ' tablinks ' }}"
                        onclick=" openTab(event, 'Teams', 'current-teams'); closeAllTippy(); ">Teams</button>
                    <button class="{{ 'side-image-' . $eventTierLower . ' tablinks ' }}"
                        onclick=" openTab(event, 'Result', 'current-positions'); closeAllTippy();  ">Result</button>
                </div>
                <br>
                <div id="Overview" class="tabcontent" style="display: {{ $showBracketFirst ? 'none' : 'block' }};">
                    <h2 id="current-title" class="fs-5 text-start"><u>About this event</u></h2>
                    <p style="white-space: pre-wrap">{{ $event->eventDescription ?? 'Not added description yet' }} </p>
                </div>

                <div id="Bracket" v-scope="BracketData()"
                    @vue:mounted="init()"
                    class="tabcontent" 
                    style="display: {{ $showBracketFirst ? 'block' : 'none' }};"
                >
                    @if ($event->type && $event->type->eventType == 'Tournament')
                        @include('includes.Public.BracketReport')
                    @elseif ($event->type && $event->type->eventType == 'League')
                        @include('includes.Public.LeagueReport')
                    @else
                        @include('includes.Public.BracketReport')
                    @endif
                </div>

                <div id="Teams" class="tabcontent" >
                    @include('includes.Public.TeamJoined')
                </div>

                <div id="Result" class="tabcontent" >
                    @include('includes.Public.TeamResults')
                </div>
            </div>
                </div>
                <div> </div>

        </div>

   
    </div>
    </main>
    <script src="{{ asset('/assets/js/participant/ViewEvent.js') }}"></script>
   <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Event",
            "name": "{{ $event->eventName }}",
            "description": "{{ $event->eventDescription }}",
            "image": "{{ asset($eventBannerImg) }}",
            "startDate": "{{ $event->startDate }}T{{ $event->startTime }}",
            "endDate": "{{ $event->endDate ?? $event->startDate }}",
            "eventStatus": "https://schema.org/EventScheduled",
            "eventAttendanceMode": "https://schema.org/{{ $event->venue == 'Online' ? 'OnlineEventAttendanceMode' : 'Global' }}",
            "location": {
                "@type": "{{ $event->venue == 'Online' ? 'VirtualLocation' : 'Global' }}",
                "name": "{{ $event->venue }}",
                "address": "{{ $event->venue }}"
            },
            "organizer": {
                "@type": "Person",
                "name": "{{ $event->user->name }}",
                "url": "{{ route('public.organizer.view', ['id'=> $event->user->id, 'title' => $event->user->slug]) }}"
            },
            "offers": {
                "@type": "Offer",
                "price": "{{ $event->tier?->tierEntryFee ?? '0' }}",
                "priceCurrency": "RM",
                "availability": "https://schema.org/InStock",
                "validFrom": "{{ now()->toIso8601String() }}"
            },
            "performer": {
                "@type": "SportsTeam",
                "name": "Multiple Teams"
            }
        }
    </script>
</html>
