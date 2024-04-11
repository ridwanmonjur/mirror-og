<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Management</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamAdmin.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body>
    @include('CommonLayout.NavbarGoToSearchPage')
    @include('Participant.Layout.TeamHead')

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

        <div class="tab-content outer-tab" id="Overview">
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
                    <div class="event-carousel-styles event-carousel-works" style="{{isset($joinEvents[1]) ? '--grid-size:1fr 1fr;': '--grid-size:1fr;'}}">
                        @foreach ($joinEvents as $key => $joinEvent)
                            @include('Participant.Layout.RosterView',  ['isRegistrationView' => false])
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="team-info">
                <div class="showcase">
                    <div><b>Showcase</b></div>
                    <br>
                    <div class="showcase-box showcase-column-2">
                        <div class="showcase-column ">
                            @if (count($selectTeam->awards) == 0)
                                <p>Events Joined: 0</p>
                                <p>Wins: 0</p>
                                <p>Win Streak: 0</p>
                            @else
                                @php
                                    $eventCounts = $joinEvents->groupBy('eventDetails.id')->map->count();
                                    $totalEvents = $eventCounts->sum();
                                @endphp
                                <p>Events Joined: {{ $totalEvents }}</p>
                                <p>Wins: 0</p>
                                <p>Win Streak: 0</p>
                            @endif
                        </div>
                        <div class="showcase-column">
                            <div class="invisible-until-hover">
                                <img src="{{ asset('/assets/images/trophy.jpg') }}" alt="Trophy" class="trophy">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="achievements">
                    <div><b>Achievements</b></div>
                    @if (count($selectTeam->awards) == 0)
                        <ul class="achievement-list">
                            <p>No awards available</p>
                        </ul>
                    @else
                        <ul class="achievement-list">
                            @foreach ($selectTeam->awards as $award)
                                <li>
                                    <span class="additional-text">{{ $award->name }} (2023)</span><br>
                                    <span class="achievement-complete"></span><br>
                                    <span class="additional-text">{{ $award->description }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    </ul>
                </div>
            </div>
        </div>

        <div class="tab-content outer-tab d-none" id="Members">
            @include('Participant.Layout.MemberView')
        </div>

        <div class="tab-content outer-tab d-none" id="Active Rosters">
            <br><br>
            @if (!isset($joinEventsActive[0]))
                <p class="text-center">
                    Team {{ $selectTeam->teamName }} has no active rosters
                </p>
            @else
                <div id="activeRostersForm" class="tex-center mx-auto">
                    <p class="text-center">Team {{ $selectTeam->teamName }} has {{ count($joinEventsActive) }} roster(s)</p>
                    <br>
                    @foreach ($joinEventsActive as $key => $joinEvent)
                        @include('Participant.Layout.RosterView', ['isRegistrationView' => false])
                    @endforeach
                </div>
            @endif
        </div>

        <div class="tab-content outer-tab d-none" id="Roster History">
            <br><br>
            @if (!isset($joinEventsHistory[0]))
                <p style="text-align: center;">Team {{ $selectTeam->teamName }} has no roster history</p>
            @else
                <div id="activeRostersForm" class="tex-center mx-auto">
                    <p class="text-center">Team {{ $selectTeam->teamName }} has {{ count($joinEventsHistory) }} roster(s)</p>
                    <br>
                    @foreach ($joinEventsHistory as $key => $joinEvent)
                        @include('Participant.Layout.RosterView',  ['isRegistrationView' => false])
                    @endforeach
                </div>
            @endif
        </div>
    </main>

    @include('CommonLayout.BootstrapV5Js')

    <script>
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
                eventBoxes[i].classList.add('d-none');
            }

            for (let i = currentIndex; i < currentIndex + 2; i++) {
                eventBoxes[i].classList.remove('d-none');
            }

            for (let i = currentIndex + 2; i < boxLength; i++) {
                eventBoxes[i].classList.add('d-none');
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
            window.location.href = "{{route('participant.profile.view', ['id' => ':id']) }}"
                .replace(':id', userId);
        }

    </script>

</body>
