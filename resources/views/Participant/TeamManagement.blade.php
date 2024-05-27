<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Management</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamAdmin.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
@auth
    @php
        if (!isset($user)) {
            $user = auth()->user();
        }
    @endphp
@endauth
<body>
    @include('CommonPartials.NavbarGoToSearchPage')
    @include('Participant.Partials.TeamHead')

    <main class="main2">
        <div class="tabs">
            <button class="tab-button outer-tab tab-button-active"
                onclick="showTab(event, 'Overview')">Overview</button>
            <button class="tab-button outer-tab" onclick="showTab(event, 'Members', 'outer-tab')">Members</button>
            <button class="tab-button outer-tab" onclick="showTab(event, 'Active Rosters', 'outer-tab')">Active
                Rosters
            </button>
            <button class="tab-button outer-tab" onclick="showTab(event, 'Roster History', 'outer-tab')">Roster
                History
            </button>
        </div>

        <div class="tab-content pb-4  outer-tab" id="Overview">
            <br><br>
            <div class="d-flex justify-content-center"><b>Recent Events</b></div>
            <br> <br>
            <div class="position-relative d-flex justify-content-center">
                @if (!isset($joinEvents[0]))
                    <p>No events available!</p>
                @else
                    <button class="carousel-button position-absolute" style="top: 100px; left: 20px;"
                        onclick="carouselWork(-2)">
                        &lt;
                    </button>
                    <button class="carousel-button position-absolute" style="top: 100px; right: 20px;"
                        onclick="carouselWork(2)">
                        &gt;
                    </button>
                    @if (!isset($joinEvents[1]))
                        <div class="d-flex justify-content-center event-carousel-works">
                            @foreach ($joinEvents as $key => $joinEvent)
                                @include('Participant.Partials.RosterView',  ['isRegistrationView' => false])
                            @endforeach
                        </div>
                    @else
                        <div class="event-carousel-styles event-carousel-works">
                            @foreach ($joinEvents as $key => $joinEvent)
                                @include('Participant.Partials.RosterView',  ['isRegistrationView' => false])
                            @endforeach
                        </div>
                    @endif
                 
                @endif
            </div>

            <div class="team-info">
                <div class="showcase">
                    <div><b>Showcase</b></div>
                    <br>
                    <div @class(["showcase-box d-none-until-hover-parent" , 
                            "d-flex justify-content-between flex-wrap" => !isset($awardList[2])
                    ])>
                        <div>
                            <p>Events Joined: {{ $totalEventsCount }}</p>
                            <p>Wins: {{ $wins }}</p>
                            <p>Win Streak: {{ $streak }}</p>
                        </div>
                        <div class="d-none-until-hover">
                            <div class="d-flex justify-content-between w-100 h-100">
                                @foreach ($awardList as $award)
                                    <div>
                                        <img src="{{ '/' . 'storage/' . $award->awards_image }} " alt="Trophy" class="trophy me-2">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="achievements">
                    <div><b>Achievements</b></div>
                    @if (!isset($achievementList[0]))
                        <ul class="achievement-list mt-4">
                            <p>No achievements available</p>
                        </ul>
                    @else
                        <ul class="achievement-list">
                            @foreach ($achievementList as $achievement)
                                <li>
                                    <span class="additional-text d-flex justify-content-between">
                                        <span>
                                        {{ $achievement->title }} ({{ \Carbon\Carbon::parse($achievement->created_at)->format('Y') }})
                                        </span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                        <path d="m10.97 4.97-.02.022-3.473 4.425-2.093-2.094a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05"/>
                                        </svg>
                                    </span><br>
                                    <span class="ps-2"> {{ $achievement->description }} </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    </ul>
                </div>
            </div>
        </div>

        <div class="tab-content pb-4  outer-tab d-none" id="Members">
            @include('Participant.Partials.MemberView')
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
            @else
                <div id="activeRostersForm" class="tex-center mx-auto">
                    <p class="text-center">Team {{ $selectTeam->teamName }} has {{ $joinCount }} roster{{ bladePluralPrefix($joinCount) }}</p>
                    <br>
                    @foreach ($joinEventsActive as $key => $joinEvent)
                        @include('Participant.Partials.RosterView', ['isRegistrationView' => false])
                        <br><br>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="tab-content pb-4  outer-tab d-none" id="Roster History">
            <br><br>
            @if (!isset($joinEventsHistory[0]))
                <p style="text-align: center;">Team {{ $selectTeam->teamName }} has no roster history</p>
            @else
                <div id="activeRostersForm" class="tex-center mx-auto">
                    <p class="text-center">Team {{ $selectTeam->teamName }} has {{ $historyCount }} roster{{ bladePluralPrefix($historyCount) }}</p>
                    <br>
                    @foreach ($joinEventsHistory as $key => $joinEvent)
                        @include('Participant.Partials.RosterView',  ['isRegistrationView' => false])
                        <br><br>
                    @endforeach
                </div>
            @endif
        </div>
    </main>
    
    

    <script>
        function reddirectToLoginWithIntened(route) {
            route = encodeURIComponent(route);
            let url = "{{ route('participant.signin.view') }}";
            url+= `?url=${route}`;
            window.location.href = url;
        }
        
        function showTab(event, tabName, extraClassNameToFilter = "outer-tab") {
            const tabContents = document.querySelectorAll(`.tab-content.${extraClassNameToFilter}`);
            tabContents.forEach(content => {
                content.classList.add("d-none");
            });

            const selectedTab = document.getElementById(tabName);
            if (selectedTab) {
                selectedTab.classList.remove('d-none');
                selectedTab.classList.add('tab-button-active');
            }
            const tabButtons = document.querySelectorAll(`.tab-button-active.${extraClassNameToFilter}`);
            tabButtons.forEach(button => {
                button.classList.remove("tab-button-active");
            });

            let target = event.currentTarget;
            target.classList.add('tab-button-active');
        }

        let currentIndex = 0;

        function carouselWork(increment = 0) {
            const eventBoxes = document.querySelectorAll('.event-carousel-works > div');
            let boxLength = eventBoxes.length;
            let newSum = currentIndex + increment;
            if (newSum >= boxLength || newSum < 0) {
                return;
            } else {
                currentIndex = newSum;
            }

            // carousel top button working
            const button1 = document.querySelector('.carousel-button:nth-child(1)');
            const button2 = document.querySelector('.carousel-button:nth-child(2)');
            button1.style.opacity = (currentIndex <= 2) ? '0.4' : '1';
            button2.style.opacity = (currentIndex >= boxLength - 2) ? '0.4' : '1';
            
            // carousel swing
            for (let i = 0; i < currentIndex; i++) {
                eventBoxes[i]?.classList.add('d-none');
            }

            for (let i = currentIndex; i < currentIndex + 2; i++) {
                eventBoxes[i]?.classList.remove('d-none');
            }

            for (let i = currentIndex + 2; i < boxLength; i++) {
                eventBoxes[i]?.classList.add('d-none');
            }
        }

        carouselWork();

        const searchInputs = document.querySelectorAll('.search_box input');
        const memberTables = document.querySelectorAll('.member-table');

        searchInputs.forEach((searchInput, index) => {
            searchInput.addEventListener("input", function() {
                const searchTerm = searchInput.value.toLowerCase();
                const memberRows = memberTables[index].querySelectorAll('tbody tr');

                memberRows.forEach(row => {
                    const playerName = row.querySelector('.player-info span')
                        .textContent.toLowerCase();

                    if (playerName.includes(searchTerm)) {
                        row.style.display = 'table-row';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });

        window.onbeforeunload = function(){window.location.reload();}

        function redirectToProfilePage(userId) {
            window.location.href = "{{route('public.participant.view', ['id' => ':id']) }}"
                .replace(':id', userId);
        }

    </script>

</body>
