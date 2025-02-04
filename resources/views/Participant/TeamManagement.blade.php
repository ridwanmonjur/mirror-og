<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Management</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamAdmin.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/manage_team.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @include('__CommonPartials.HeadIcon')
</head>
@auth
    @php
        if (!isset($user)) {
            $user = auth()->user();
        }
    @endphp
@endauth
<body>
    @include('googletagmanager::body')
    @include('__CommonPartials.NavbarGoToSearchPage')
    @include('Participant.__Partials.TeamHead')
    <main class="main2">
        <input type="hidden" id="signin_url" name="url" value="{{ route('participant.signin.view') }}">
        <input type="hidden" id="profile_route" value="{{ route('public.participant.view', ['id' => ':id']) }}">
        <div class="tabs">
            <button class="tab-button outer-tab py-2 tab-button-active px-3"
                onclick="showTab(event, 'Overview')">Overview</button>
            <button class="tab-button outer-tab px-3 py-2" onclick="showTab(event, 'Members', 'outer-tab')">Members</button>
            <button class="tab-button outer-tab px-3 py-2" onclick="showTab(event, 'Active Rosters', 'outer-tab')">Active
                Rosters
            </button>
            <button class="tab-button outer-tab px-3 py-2" onclick="showTab(event, 'Roster History', 'outer-tab')">Roster
                History
            </button>
        </div>

        <div class="tab-content pb-4 outer-tab" id="Overview">
            <br><br>
            <div class="d-flex justify-content-center"><b>Recent Events</b></div>
            <br> <br>
            <div class="position-relative d-flex justify-content-center">
                @if (!isset($joinEvents[0]))
                    <p>No events available!</p>
                @else
                    <button @class(["carousel-button position-absolute ",
                       "carousel-button-disabled"  => empty($joinEvents[2])
                    ]) style="left: 20px;"
                        onclick="carouselWork(-2)">
                        &lt;
                    </button>
                    <button @class(["carousel-button position-absolute ",
                       "carousel-button-disabled"  => empty($joinEvents[2])
                    ]) style="right: 30px;"
                        onclick="carouselWork(2)">
                        &gt;
                    </button>
                     <div @class(["event-carousel-works animation-container", 
                        "event-carousel-styles" => isset($joinEvents[1]),
                        "d-flex justify-content-center " => !isset($joinEvents[1])
                    ])
                    >
                        @foreach ($joinEvents as $key => $joinEvent)
                            @include('Participant.__Partials.RosterView')
                        @endforeach
                    </div>
                 
                @endif
            </div>

            <div class="row px-4 mt-5">
                <div class="showcase col-12 col-lg-6">
                    <div class="text-center"><b>Showcase</b></div>
                    <br>
                      
                    <div class="card border-2 border-dark py-0 my-0 mx-auto py-4" style=" width: 90%;">
                        <div class="card-body row py-2 d-flex justify-content-center flex-wrap text-center mx-auto py-0 px-0 " 
                            style="padding-top: 30px;padding-bottom: 30px;width: 90%;"
                        >
                            <div class="col-12 col-xl-4"> 
                                <h3 class="py-0 my-0"> {{ $totalEventsCount }} </h3>
                                <p class="mx-2 py-2 my-0"> Events Joined By Team </p>
                            </div>
                            <div class="col-12 col-xl-4"> 
                                <h3 class="py-0 my-0"> {{$wins}} </h3>
                                <p class="mx-2 py-2 my-0"> Tournament Wins By Team </p>
                            </div>
                            <div class="col-12 col-xl-4"> 
                                <h3 class="py-0 my-0"> {{$streak}} </h3>
                                <p class="mx-2 py-2 my-0"> Team Current Win Streak </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="achievements col-12 col-lg-6">
                    <div class="ms-2 text-center"><b>Positions</b></div><br>
                    @include('__CommonPartials.PositionBadge')
                </div>
            </div>
        </div>

        <div class="tab-content pb-4 outer-tab d-none" id="Members">
            @include('Participant.__Partials.TeamManagementMemberView')
        </div>

        @php
            $joinCount = count($joinEventsActive);
            $historyCount = count($joinEventsHistory);
        @endphp

        <div class="tab-content pb-4  outer-tab d-none" id="Active Rosters">
            <br><br>
            @if (!isset($joinEventsActive[0]))
                <p class="text-center">
                    Team {{ $selectTeam->teamName }} has no active rosters
                </p>
                <br><br><br>
            @else
                <div id="activeRostersForm" class="animation-container text-center mx-auto">
                    <p class="text-center">Team {{ $selectTeam->teamName }} has {{ $joinCount }} roster{{ bladePluralPrefix($joinCount) }}</p>
                    <br>
                    @foreach ($joinEventsActive as $key => $joinEvent)
                        <div class="d-flex justify-content-center align-items-center   animation-container ">
                            @include('Participant.__Partials.RosterView')
                            
                        </div>
                        <br><br>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="tab-content pb-4  outer-tab d-none" id="Roster History">
            <br><br>
            @if (!isset($joinEventsHistory[0]))
                <p style="text-align: center;">Team {{ $selectTeam->teamName }} has no roster history </p>
                <br> <br><br> 
            @else
                <div id="activeRostersForm" class="animation-container text-center mx-auto">
                    <p class="text-center">Team {{ $selectTeam->teamName }} has {{ $historyCount }} roster{{ bladePluralPrefix($historyCount) }}</p>
                    <br>
                    @foreach ($joinEventsHistory as $key => $joinEvent)
                        <div class="d-flex justify-content-center align-items-center   animation-container ">
                            @include('Participant.__Partials.RosterView')
                            @if (isset($user) && $selectTeam->creator_id == $user->id)
                                <a
                                    href="{{ route('participant.register.manage', ['id' => $joinEvent->eventDetails->id, 'teamId' => $selectTeam->id]) }}"
                                >
                                    <button class="btn btn-link me-2 gear-icon-btn" type="submit">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-gear" viewBox="0 0 16 16">
                                        <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492M5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0"/>
                                        <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115z"/>
                                        </svg>
                                    </button>
                                </a>
                            @endif
                        </div>
                        <br><br>
                    @endforeach
                </div>
            @endif
        </div>
    </main>
    <script src="{{ asset('/assets/js/participant/TeamManagement.js') }}"></script>
  
</body>
