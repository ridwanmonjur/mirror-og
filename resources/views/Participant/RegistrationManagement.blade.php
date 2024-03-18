<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Management</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamAdmin.css') }}">
        <link rel="stylesheet" href="{{ asset('/assets/css/common/pie-chart.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.3.0/tagify.css">
    <link rel="stylesheet" href="{{ asset('/assets/css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

</head>

<body>
    @include('CommonLayout.NavbarforParticipant')
    @include('Participant.Layout.TeamHead')
   
    <main class="main2">

        <div class="" id="Overview">
            <br><br>
            <div class="d-flex justify-content-center"><b>Recent Events</b></div>
            <br> <br>
            <div class="position-relative d-flex justify-content-center">
                @if (empty($joinEvents))
                    <p>No events available</p>
                @else
                    <div class="event-carousel">
                        @foreach ($joinEvents as $key => $joinEvent)
                           <div class="event mx-auto">
                                <div style="background-color:rgb(185, 182, 182); text-align: left; height: 200px;">
                                    <br>
                                    @if (!isset($joinEvent->roster[0]))
                                        <div class="player-info mt-1 ms-4">
                                            <span>Empty roster</span>
                                        </div>
                                    @else
                                        <ul class="player-info mt-1 ms-4">
                                            @foreach ($joinEvent->roster as $roster)
                                                <li>
                                                    <span>{{ $roster->user->name }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                                <div class="frame1">
                                    <div class="container d-flex justify-content-between pt-2">
                                        <div>
                                            <img {!! trustedBladeHandleImageFailureBanner() !!} src="{{ bladeImageNull($joinEvent->eventBanner) }}" class="logo2">
                                            <span> {{ $joinEvent->eventDetails->eventName }} </span>
                                        </div>
                                        <div>
                                            <img {!! trustedBladeHandleImageFailureBanner() !!}
                                                src="{{ bladeImageNull($joinEvent->game ? $joinEvent->game->gameIcon : null) }}" class="logo2 me-2">
                                            <span>{{ $joinEvent->game->gameTitle }}</span>
                                            <span>1K Followers</span>
                                        </div>
                                    </div>
                                </div>
                               
                                {{-- <div class="d-flex mt-2 mb-3 justify-content-center">
                                    <div>
                                        <form method="GET"
                                            action="{{ route('participant.roster.manage', ['id' => $joinEvent->eventDetails->id, 'teamId' => $selectTeam->id]) }}">
                                            <button class="oceans-gaming-default-button oceans-gaming-default-button-link me-2" type="submit">
                                                Manage Roster
                                            </button>
                                        </form>
                                    </div>
                                </div> --}}
                            </div>
                            <div class="pie-chart">
                                <div class="pie" style="--p:20"> 20%</div>
                                <p> Total Entry Fee : <u>RM {{$joinEvent->tier->tierEntryFee * $joinEvent->tier->tierTeamSlot}} </u></p>
                                <p> Paid: <u class="text-success">RM  </u> Pending: <u class="text-danger">RM </u> </p>
                            </div>
                        @endforeach
                    </div>
                   
                @endif
            </div>

            <div class="team-info">
                <div class="showcase">
                    <div><b>Showcase</b></div>
                    <br>
                    <div class="showcase-box">
                        <div class="showcase-column">
                            @if (count($selectTeam->awards) == 0)
                                <p>No events available</p>
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
                            <!-- Trophy image in the second column -->
                            <img src="{{ asset('/assets/images/trophy.jpg') }}" alt="Trophy" class="trophy">
                        </div>
                    </div>
                </div>

                <div class="achievements">
                    <div><b>Achievements</b></div>
                    @if (count($selectTeam->awards) == 0)
                        <p>No awards available</p>
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

        
        function slideEvents(direction) {
            const eventBoxes = document.querySelectorAll('.event-box');

            const visibleEvents = Array.from(eventBoxes).filter(eventBox => eventBox.style.display !== 'none');

            eventBoxes.forEach(eventBox => (eventBox.style.display = 'none'));

            let startIndex = 0;

            if (visibleEvents.length > 0) {
                startIndex = (Array.from(eventBoxes).indexOf(visibleEvents[0]) + direction + eventBoxes.length) % eventBoxes
                    .length;
            }

            for (let i = 0; i < Math.min(2, eventBoxes.length); i++) {
                const index = (startIndex + i + eventBoxes.length) % eventBoxes.length;
                eventBoxes[index].style.display = 'block';
            }
        }

        function initializeEventsDisplay() {
            const eventBoxes = document.querySelectorAll('.event-box');

            eventBoxes.forEach(eventBox => (eventBox.style.display = 'none'));

            for (let i = 0; i < Math.min(2, eventBoxes.length); i++) {
                eventBoxes[i].style.display = 'block';
            }
        }
        document.addEventListener("DOMContentLoaded", function() {
            initializeEventsDisplay();
        });


        async function approveMember(memberId) {
            const url = "{{ route('participant.member.approve', ['id' => ':id']) }}".replace(':id', memberId);
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                });
                
                const data = await response.json();
                
                if (data.success) {
                    const memberRow = button.closest('tr');
                    memberRow.remove();
                } else {
                    console.error('Error updating member status:', data.message);
                }
            } catch (error) {
                console.error('Error approving member:', error);
            }
        }
   
        document.addEventListener("DOMContentLoaded", function() {
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
        });
    </script>

    {{-- End Javascript for Search Member  --}}


</body>
