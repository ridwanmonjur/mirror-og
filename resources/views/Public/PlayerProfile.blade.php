<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/organizer/player_profile.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamAdmin.css') }}">
    @include('includes.HeadIcon')
    @vite([
        'resources/sass/app.scss', 
        'resources/js/app.js', 
        'resources/js/alpine/participant.js'
    ])
</head>
@php
    $isUserSame = false;
    $loggedUserId = $loggedUserRole = null;

    [   
        'backgroundStyles' => $backgroundStyles, 
        'fontStyles' => $fontStyles, 
        'frameStyles' => $frameStyles
    ] = $userProfile->profile?->generateStyles();
    if (!$backgroundStyles) {
        $backgroundStyles = "background-color: #fffdfb;"; // Default gray
    }

    $activityNames = ['new', 'recent', 'older'];
@endphp
@auth
    @php
        if (!isset($user)) {
            $user = auth()->user();
        }
        
        $loggedUserId = $user->id;
        $loggedUserRole = $user->role;
        $isUserSame = $user?->id == $userProfile->id;
    @endphp
@endauth
<body>
    @include('googletagmanager::body')
    @include('includes.__Navbar.NavbarGoToSearchPage')
    <div
        data-user-profile-id="{{ $userProfile->id }}"
        data-user-profile-birthday="{{ $userProfile->participant->birthday }}"
        data-background-api-url="{{ route('user.userBackgroundApi.action', ['id' => $userProfile->id]) }}"
        data-signin-url="{{ route('participant.signin.view') }}"
        data-public-profile-url="{{ route('public.participant.view', ['id' => ':id']) }}"
        data-background-styles="{{ $backgroundStyles }}"
        data-font-styles="{{ $fontStyles }}"
        data-is-user-same="{{ $isUserSame }}"
        data-logged-user-id="{{ $loggedUserId }}"
        data-logged-user-role="{{ $loggedUserRole }}"
        class="d-none laravel-data-storage"
    ></div>
    <main id="app" >
        @include('includes.__Profile.BackgroundModal')
        @include('includes.__Profile.FriendFollowForms')
        @include('includes.__Profile.PlayerHead')

        <div class="tabs ">
            <button class="tab-button  outer-tab tab-button-active"
                onclick="showTab(event, 'Overview', 'outer-tab')">Overview</button>
            <button class="tab-button outer-tab" onclick="showTab(event, 'Activity', 'outer-tab')">Activity</button>
            <button class="tab-button outer-tab" onclick="showTab(event, 'Events', 'outer-tab')">Events</button>
            <button class="tab-button outer-tab" onclick="showTab(event, 'Teams', 'outer-tab')">Teams</button>
        </div>
        
        <div class="tab-content pb-4  outer-tab" id="Overview">
            <div class="d-none d-lg-block"><br></div>
            
            <div class="d-flex d-none d-lg-flex  justify-content-center"><b>Recent Events</b></div>
            <div class="d-none d-lg-block"><br><br></div>
            <div class="position-relative d-none d-lg-flex justify-content-center">
                @if (!isset($joinEvents[0]))
                    <p>No events available!</p>
                @else
                    <button class="carousel-button position-absolute " style=" left: 20px;"
                        onclick="carouselWork(-2)">
                        &lt;
                    </button>
                    <button class="carousel-button position-absolute " style="right: 30px;"
                        onclick="carouselWork(2)">
                        &gt;
                    </button>
                    <div @class(["event-carousel-works animation-container ", 
                        "event-carousel-styles" => isset($joinEvents[1]),
                        "d-flex justify-content-center " => !isset($joinEvents[1])
                    ])
                    >
                        @foreach ($joinEvents as $key => $joinEvent)
                            @include('includes.__Team.RosterView')
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="row px-4 mt-4">
               <div class="row px-4 ">
                <div class="showcase col-12 col-lg-6">
                    <div class="text-center"><b>Showcase</b></div>
                    <br>
                      
                    <div class="card border-2 py-0 my-0 mx-auto py-4" style=" width: 90%;">
                        <div class="card-body row py-2 d-flex justify-content-center flex-wrap text-center mx-auto py-0 px-0 " 
                            style="padding-top: 30px;padding-bottom: 30px;width: 90%;"
                        >
                            <div class="col-12 col-xl-4"> 
                                <h3 class="py-0 my-0"> {{ $totalEventsCount }} </h3>
                                <p class="mx-2 py-2 my-0"> Event{{ bladePluralPrefix($totalEventsCount) }} Joined By Player </p>
                            </div>
                            <div class="col-12 col-xl-4"> 
                                <h3 class="py-0 my-0"> {{$wins}} </h3>
                                <p class="mx-2 py-2 my-0"> Tournament Win{{ bladePluralPrefix($wins) }} By Player </p>
                            </div>
                            <div class="col-12 col-xl-4"> 
                                <h3 class="py-0 my-0"> {{$streak}} </h3>
                                <p class="mx-2 py-2 my-0"> Player Current Win Streak </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="achievements col-12 col-lg-6">
                    <div class="ms-2 text-center"><b>Positions</b></div><br>
                    @include('includes.__Public.PositionBadge')
                </div>
            </div>
        </div>
        </div>
        


        <div class="tab-content pb-4 d-none outer-tab " id="Activity">
            <br>
            <div class="tab-size"><b>New</b></div>
            @include('includes.__Profile.ActivityLogs', ['duration' => $activityNames[0]])
    
            <div class="tab-size"><b>Recent</b></div>
            @include('includes.__Profile.ActivityLogs', ['duration' => $activityNames[1]])
            
            <div class="tab-size"><b>Older</b></div>
            @include('includes.__Profile.ActivityLogs', ['duration' => $activityNames[2]])
            
        </div>

        <div class="tab-content pb-4  outer-tab d-none" id="Events">
             <br>
            <div class="tab-size"><b>Active Events</b></div>
            <br>
            @if (!isset($joinEventsActive[0]))
                <p class="tab-size">
                    This profile has no active events
                </p>
            @else
                <div id="activeRostersForm" class="animation-container text-center mx-auto">
                    <br>
                    @foreach ($joinEventsActive as $key => $joinEvent)
                        @include('includes.__Team.RosterView')
                        <br><br>
                    @endforeach
                </div>
            @endif
            <br>
            <div class="tab-size"><b>Past Events</b></div>
            <br>
            @if (!isset($joinEventsHistory[0]))
                <p class="tab-size">
                    This profile has no past events
                </p>
            @else
                <div id="activeRostersForm" class="animation-container text-center mx-auto">
                    <br>
                    @foreach ($joinEventsHistory as $key => $joinEvent)
                        @include('includes.__Team.RosterView')
                        <br><br>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="tab-content pb-4  outer-tab d-none" id="Teams">
             <br>
            <div class="tab-size"><b>Current Teams</b></div>
            <div class="tab-size pt-4">
            @if (isset($teamList[0]))
                <div class="row row-cols-1 row-cols-lg-2 g-4">
                    @foreach($teamList as $team)
                        <div class="col">
                            <div class="card h-100 border-0 " style="transition: transform 0.2s; cursor: pointer;" 
                                onmouseover="this.style.transform='translateY(-2px)'" 
                                onmouseout="this.style.transform='translateY(0)'">
                                <div class="card-body border-2">
                                    <div class="d-flex align-items-center justify-content-between my-2">
                                        <div class="d-flex align-items-center flex-grow-1">
                                            <img 
                                                src="{{ '/storage' . '/'. $team->teamBanner }}"
                                                {!! trustedBladeHandleImageFailure() !!}
                                                class="border border-secondary rounded-circle me-3"
                                                style="object-fit: cover;"
                                                width="50"
                                                height="50"
                                                alt="{{ $team->teamName }}"
                                            >
                                            <div>
                                                <h5 class="card-title mb-0 text-wrap">{{ $team->teamName }}  <span class="ps-2 fs-5">{{ $team->country_flag }}</span></h5>
                                                <div class="text-muted">
                                                    <span class="me-3">{{ $team->members_count }}/5 members</span>
                                                   

                                                </div>
                                            </div>
                                        </div>
                                        <a href="{{ route('public.team.view', ['id' => $team->id]) }}" 
                                        class="btn gear-icon-btn border-secondary btn-sm rounded-circle position-relative" 
                                        style="z-index: 3;">
                                           <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9 6L15 12L9 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                        </svg>
                                        </a>
                                    </div>
                                </div>
                                <a href="{{ route('public.team.view', ['id' => $team->id]) }}" 
                                class="position-absolute top-0 start-0 w-100 h-100"></a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="tab-size">No current teams</div>
            @endif
            </div>
            <br> 
            <div class="tab-size"><b>Past Teams</b></div>
            <div class="tab-size pt-4">
            @if (isset($pastTeam[0]))
                <div class="row row-cols-1 row-cols-lg-2 g-4">
                    @foreach($pastTeam as $team)
                        <div class="col">
                            <div class="card h-100 border-0 " style="transition: transform 0.2s; cursor: pointer;" 
                                onmouseover="this.style.transform='translateY(-2px)'" 
                                onmouseout="this.style.transform='translateY(0)'">
                                <div class="card-body border-2">
                                    <div class="d-flex align-items-center justify-content-between my-2">
                                        <div class="d-flex align-items-center flex-grow-1">
                                            <img 
                                                src="{{ '/storage' . '/'. $team->teamBanner }}"
                                                {!! trustedBladeHandleImageFailure() !!}
                                                class="border border-secondary rounded-circle me-3"
                                                style="object-fit: cover;"
                                                width="50"
                                                height="50"
                                                alt="{{ $team->teamName }}"
                                            >
                                            <div>
                                                <h5 class="card-title mb-0">{{ $team->teamName }}</h5>
                                                <div class="text-muted">
                                                    <span class="me-3">{{ $team->members_count }}/5 members</span>
                                                    <span class="me-2 fs-5">{{ $team->country_flag }}</span>

                                                </div>
                                            </div>
                                        </div>
                                        <a href="{{ route('public.team.view', ['id' => $team->id]) }}" 
                                        class="btn gear-icon-btn border-secondary btn-sm rounded-circle position-relative" 
                                        style="z-index: 3;">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9 6L15 12L9 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                        </svg>
                                        </a>
                                    </div>
                                </div>
                                <a href="{{ route('public.team.view', ['id' => $team->id]) }}" 
                                class="position-absolute top-0 start-0 w-100 h-100"></a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="tab-size">No past teams</div>
            @endif
            </div>
        </div>
        <script src="{{ asset('/assets/js/participant/Profile.js') }}"></script>
    </main>
</body>
@include('includes.__Profile.Cropper')

</html>
