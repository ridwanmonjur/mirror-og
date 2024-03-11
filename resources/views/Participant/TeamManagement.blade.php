<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Management</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamAdmin.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.3.0/tagify.css">
    <link rel="stylesheet" href="{{ asset('/assets/css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

</head>

<body>
    @include('CommonLayout.NavbarforParticipant')
    @include('Participant.Layout.TeamHead')
   
    <main class="main2">
        <div class="tabs">
            <button class="tab-button outer-tab tab-button-active"
                onclick="showTab(event, 'Overview')">Overview</button>
            <button class="tab-button outer-tab" onclick="showTab(event, 'Members', 'outer-tab')">Members</button>
            <button class="tab-button outer-tab" onclick="showTab(event, 'Active Rosters', 'outer-tab')">Active
                Rosters</button>
            <button class="tab-button outer-tab" onclick="showTab(event, 'Roster History', 'outer-tab')">Roster
                History</button>
        </div>

        <div class="tab-content outer-tab" id="Overview">
            <br><br>
            <div class="d-flex justify-content-center"><b>Recent Events</b></div>
            <div class="recent-events">

                @if (empty($joinEvents))
                    <p>No events available</p>
                @else
                    <button class="carousel-button" onclick="slideEvents(-1)">
                        &lt;
                    </button>
                    <div class="event-carousel">
                        @php
                            $uniqueEventDetailsIds = [];
                        @endphp
                        @foreach ($joinEvents as $key => $joinEvent)
                            @php
                                $eventDetailsId = $joinEvent->eventDetails->id;
                            @endphp
                            @if (!in_array($eventDetailsId, $uniqueEventDetailsIds))
                                <a class="d-block" href="/event/{{ $eventDetailsId }}">
                                    <div class="event-box" id="event{{ $key + 1 }}"
                                        style="display: {{ $key === 0 ? 'block' : 'none' }};">
                                        <div style="position: relative; height: 200px;">
                                            {{-- <div style="background-image: url('{{ $joinEvent->eventDetails->eventBanner ? 'https://driftwood.gg/storage/' . $joinEvent->eventDetails->eventBanner : 'https://driftwood.gg/storage/placeholder.jpg' }}'); background-size: cover; background-position: center; text-align: left; height: 200px;"> --}}
                                            <div
                                                style="background-image: url('{{ $joinEvent->eventDetails->eventBanner ? 'https://driftwood.gg/storage/' . $joinEvent->eventDetails->eventBanner : 'https://driftwood.gg/storage/placeholder.jpg' }}'); background-size: cover; background-position: center; text-align: left; height: 200px;">
                                                <!-- Banner image goes here -->
                                            </div>
                                            <div
                                                style="position: absolute; top: 2%; left: 50%; transform: translate(-50%, -50%); z-index: 1; width: 50px; height: 50px; overflow: hidden; border-radius: 50%;">
                                                @php
                                                    $eventTierJson = $joinEvent->tier;

                                                    $eventTierArray = json_decode($eventTierJson, true);

                                                    $tierIcon = $eventTierArray['tierIcon'];
                                                    $imagePathWithoutExtension =
                                                        'https://driftwood.gg/storage/' . strtolower($tierIcon);
                                                    $imageExtension = pathinfo(
                                                        $imagePathWithoutExtension,
                                                        PATHINFO_EXTENSION,
                                                    );

                                                    $supportedExtensions = ['jpg', 'jpeg', 'png'];

                                                    $imagePath =
                                                        $imagePathWithoutExtension .
                                                        (in_array(strtolower($imageExtension), $supportedExtensions)
                                                            ? ''
                                                            : '.png');
                                                @endphp

                                                <img src="{{ $imagePath }}" alt="Circle Image"
                                                    style="width: 100%; height: 100%; object-fit: cover;">
                                                <div class="frame1">
                                                    <div class="container">
                                                        <div class="left-col">
                                                            <p>
                                                                <img src="https://i.pinimg.com/originals/8a/8b/50/8a8b50da2bc4afa933718061fe291520.jpg"
                                                                    class="logo2">
                                                            </p>
                                                            <p class="eventName">
                                                                {{ $joinEvent->eventDetails->eventName }} </p>
                                                        </div>
                                                        <div class="right-col">
                                                            <p>
                                                                <img src="/assets/images/dota.png" class="logo2">
                                                            <p
                                                                style="font-size: 14px; text-align: left; align-items: center; justify-content: space-between;">
                                                                <span>{{ $joinEvent->eventDetails->user->organizer->companyName ?? 'Add' }}</span>
                                                                <br>
                                                                <span
                                                                    style="font-size: 12px;">{{ $followCounts[$joinEvent->eventDetails->user->organizer->id] ?? '0' }}
                                                                    Followers</span>
                                                            <div style="align-items: center;">
                                                                <button
                                                                    style="background-color: #43A4D7; color: #FFFFFF; padding: 5px 10px; font-size: 12px; border-radius: 10px; margin-left: 30px;"
                                                                    type="submit">Follow</button>
                                                            </div>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endif
                        @endforeach
                    </div>

                    <button class="carousel-button" onclick="slideEvents(1)">
                        &gt;
                    </button>

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

        <div class="tab-content outer-tab d-none" id="Members">
            @include('Participant.Layout.MemberView')
        </div>

        <div class="tab-content outer-tab d-none" id="Active Rosters">
            <br><br>
            <p class="text-center">
                Team {{ $selectTeam->teamName }} has no active rosters
            </p>
            <div id="activeRostersForm" class="tex-center mx-auto">
                @foreach ($joinEvents as $key => $joinEvent)
                    @if (in_array($joinEvent->status, ['ONGOING', 'UPCOMING']))
                        @include('Participant.Layout.RosterView')
                    @endif
                @endforeach
            </div>
        </div>

        <div class="tab-content outer-tab d-none" id="Roster History">
            <br><br>
            <p style="text-align: center;">Team {{ $selectTeam->teamName }} has no roster history</p>
            <div id="activeRostersForm" class="tex-center mx-auto">
                @foreach ($joinEvents as $key => $joinEvent)
                    @if (in_array($joinEvent->status, ['ENDED']))
                        @include('Participant.Layout.RosterView')
                    @endif
                @endforeach
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

        const uploadButton = document.getElementById("upload-button");
        const imageUpload = document.getElementById("image-upload");
        const uploadedImage = document.getElementById("uploaded-image");

        uploadButton.addEventListener("click", function() {
            imageUpload.click();
        });

        imageUpload.addEventListener("change", function(e) {
            const file = e.target.files[0];

            if (file) {
                const reader = new FileReader();

                reader.onload = function(readerEvent) {
                    uploadedImage.style.backgroundImage = url(
                        "https://www.creativefabrica.com/wp-content/uploads/2022/07/10/tiger-logo-design-Graphics-33936667-1-580x387.jpg"
                    );
                };

                reader.readAsDataURL(file);
            }
        });

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
