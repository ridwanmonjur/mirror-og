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
    @include('__CommonPartials.__Navbar.NavbarGoToSearchPage')
    @include('Participant.__Partials.TeamHead')
    <main class="main2">
        <input type="hidden" id="signin_url" name="url" value="{{ route('participant.signin.view') }}">
        <input type="hidden" id="profile_route" value="{{ route('public.participant.view', ['id' => ':id']) }}">
        <div class="tabs">
            <button class="tab-button outer-tab py-2 tab-button-active "
                onclick="showTab(event, 'Overview')">Overview</button>
            <button class="tab-button outer-tab  py-2" onclick="showTab(event, 'Members', 'outer-tab')">Members</button>
            <button class="tab-button outer-tab  py-2" onclick="showTab(event, 'Active Rosters', 'outer-tab')"><span class="d-none d-lg-inline">Active</span>
                Rosters
            </button>
            <button class="tab-button outer-tab  py-2" onclick="showTab(event, 'Roster History', 'outer-tab')"><span class="d-none d-lg-inline">Roster </span>
                History
            </button>
        </div>

        <div class="tab-content pb-4 outer-tab" id="Overview">
            <div class="d-none d-lg-block"><br><br></div>
            <div class="tab-size d-none d-lg-flex "><b>Recent Events</b></div>
            <div class="d-none d-lg-block"><br><br></div>
            <div class="position-relative d-none d-lg-flex justify-content-center">
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
                    ]) style="right: 20px;"
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
            <br class="d-none d-lg-block" > 

            <div class="row px-4">
                <div class="showcase col-12 col-lg-6">
                    <div class="text-center"><b>Showcase</b></div>
                    <br>
                      
                    <div class="card border-2 py-0 my-0 mx-auto py-4" style=" width: 90%;">
                        <div class="card-body row py-2 d-flex justify-content-center flex-wrap text-center mx-auto py-0 px-0 " 
                            style="padding-top: 30px;padding-bottom: 30px;width: 90%;"
                        >
                            <div class="col-12 col-xl-4"> 
                                <h3 class="py-0 my-0"> {{ $totalEventsCount }} </h3>
                                <p class="mx-2 py-2 my-0"> Event{{ bladePluralPrefix($totalEventsCount) }} Joined By Team </p>
                            </div>
                            <div class="col-12 col-xl-4"> 
                                <h3 class="py-0 my-0"> {{$wins}} </h3>
                                <p class="mx-2 py-2 my-0"> Tournament Win{{ bladePluralPrefix($wins) }} By Team </p>
                            </div>
                            <div class="col-12 col-xl-4"> 
                                <h3 class="py-0 my-0"> {{$streak}} </h3>
                                <p class="mx-2 py-2 my-0"> Team Current Win Streak </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="achievements col-12 col-lg-6" id="Positions">
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
                    Team {{ $selectTeam->teamName }} has no active rosters.
                </p>
            @else
                <div id="activeRostersForm" class="animation-container text-center mx-auto">
                    <p class="text-center">Team {{ $selectTeam->teamName }} has {{ $joinCount }} roster member{{ bladePluralPrefix($joinCount) }}.</p>
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
            @else
                <div id="activeRostersForm" class="animation-container text-center mx-auto">
                    <p class="text-center">Team {{ $selectTeam->teamName }} has {{ $historyCount }} roster{{ bladePluralPrefix($historyCount) }} member{{ bladePluralPrefix($joinCount) }}.</p>
                    <br>
                    @foreach ($joinEventsHistory as $key => $joinEvent)
                        <div class="d-flex justify-content-center align-items-center   animation-container ">
                            @include('Participant.__Partials.RosterView')
                        </div>
                        <br><br>
                    @endforeach
                </div>
            @endif
        </div>
    </main>
                

    <script src="{{ asset('/assets/js/participant/TeamManagementMemberView.js') }}"></script>
  
</body>
