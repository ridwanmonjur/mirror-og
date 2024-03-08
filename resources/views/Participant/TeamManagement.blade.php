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
            <button class="tab-button" onclick="showTab('Overview')">Overview</button>
            <button class="tab-button" onclick="showTab('Members')">Members</button>
            <button class="tab-button" onclick="showTab('Active Rosters')">Active Rosters</button>
            <button class="tab-button" onclick="showTab('Roster History')">Roster History</button>
        </div>

        <div class="tab-content" id="Overview">
            <div style="padding-left: 200px;"><b>Recent Events</b></div>

            <div class="recent-events">
                <div class="event-carousel">
                    @if (empty($joinEvents))
                        <p>No events available</p>
                    @else
                        <button class="carousel-button" onclick="slideEvents(-1)" style="display: block;">
                            &lt;
                        </button>
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
                                                    $eventTierJson = $joinEvent->eventDetails->eventTier;

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
                        <button class="carousel-button" onclick="slideEvents(1)">
                            &gt;
                        </button>
                    @endif
                </div>
            </div>


            <div class="team-info">
                <div class="showcase">
                    <div><b>Showcase</b></div>
                    <br>
                    <div class="showcase-box">
                        <div class="showcase-column">
                            @php
                                $eventCounts = $joinEvents->groupBy('eventDetails.id')->map->count();
                                $totalEvents = $eventCounts->sum();
                            @endphp
                            <p>Events Joined: {{ $totalEvents }}</p>
                            <p>Wins: 0</p>
                            <p>Win Streak: 0</p>
                        </div>
                        <div class="showcase-column">
                            <!-- Trophy image in the second column -->
                            <img src="{{ asset('/assets/images/trophy.jpg') }}" alt="Trophy" class="trophy">
                        </div>
                    </div>
                </div>

                <div class="achievements">
                    <div><b>Achievements</b></div>
                    <br>
                    <ul class="achievement-list">
                        <li>
                            <span class="additional-text">First Place - Online Tournament (2023)</span>
                            <br>
                            <span class="achievement-complete"></span>
                            <br>
                            <span class="additional-text">Get a girlfriend</span>
                        </li>
                        <li>
                            <span class="additional-text">Best Team Collaboration - LAN Event (2022)</span>
                            <br>
                            <span class="achievement-complete"></span>
                            <br>
                            <span class="additional-text">Get a girlfriend</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="tab-content" id="Members" style="display: 'none'">

            <div style="text-align: center;">
                <p style="text-align: center;">Team {{ $selectTeam->teamName }} has 1 members</p>
                <div class="cont">
                    <div class="leftC">
                        <span class="icon2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-filter">
                                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                            </svg>
                            <span> Filter </span>
                        </span>
                        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                        <span class="icon2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M20.2 7.8l-7.7 7.7-4-4-5.7 5.7" />
                                <path d="M15 7h6v6" />
                            </svg>
                            <span>
                                Sort
                            </span>
                        </span>
                    </div>
                    <div class="rightC">
                        <div class="search_box">
                            <i class="fa fa-search"></i>
                            <input class="nav__input" type="text" placeholder="Search for player name">
                        </div>
                        <div style="padding-right: 200px; transform: translateY(-95%);">
                            @if (auth()->user()->id == $selectTeam->creator_id)
                                <img src="/assets/images/add.png" height="40px" width="40px">
                            @endif
                        </div>
                    </div>
                </div>
                <table class="member-table">
                    <tbody>
                        @foreach ($teamMembersProcessed['accepted']['members'] as $teamMemberProcessed)
                            <tr class="st">
                                <td>
                                    <div class="player-info">
                                        <div class="player-image"
                                            style="background-image: url('https://www.vhv.rs/dpng/d/511-5111355_register-super-admin-icon-png-transparent-png.png')">
                                            <span class="crown">&#x1F451;</span> <!-- Crown emoji -->
                                        </div>
                                        <span>{{ $teamMemberProcessed->user->name }}</span>

                                    </div>
                                </td>
                                <td class="flag-cell">
                                    <img class="nationality-flag" src="{{ asset('/assets/images/china.png') }}"
                                        alt="User's flag">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            <div class="tab-content" id="Active Rosters" style="display: center;">

                <p style="text-align: center;">Team {{ $selectTeam->teamName }} has no active rosters</p>
                <div id="activeRostersForm" style="display: center; text-align: center;">

                    <div class="event">
                        <div style="background-color:rgb(185, 182, 182); text-align: left; height: 200px;">
                            <br>
                            <div class="player-info">
                                <div class="player-image"
                                    style="background-image: url('https://www.svgrepo.com/download/347916/radio-button.svg')">
                                </div>
                                <span>Dota</span>
                            </div>
                            <div class="player-info">
                                <div class="player-image"
                                    style="background-image: url('https://www.svgrepo.com/download/347916/radio-button.svg')">
                                </div>
                                <span>Fifa</span>
                            </div>
                            <div class="player-info">
                                <div class="player-image"
                                    style="background-image: url('https://www.svgrepo.com/download/347916/radio-button.svg')">
                                </div>
                                <span>GTA V</span>
                            </div>
                        </div>
                        <div class="frame1">
                            <div class="container">
                                <div class="left-col">
                                    <p><img src="https://logos-world.net/wp-content/uploads/2020/12/Dota-2-Logo.png"
                                            class="logo2">
                                    <p style="font-size: 10px; text-align: left;">The Super Duper Extreme Dota
                                        Challenge
                                        League Season 1</p>
                                    </p>
                                </div>
                                <div class="right-col">
                                    <p><img src="https://logos-world.net/wp-content/uploads/2020/12/Dota-2-Logo.png"
                                            class="logo2">
                                    <p style="font-size: 12px; text-align: left;">Media Prima</p>
                                    <br>
                                    <p style="font-size: 12px; text-align: left;">1K Followers</p>
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

            <div class="tab-content" id="Roster History" style="display: none;">
                <p style="text-align: center;">Team {{ $selectTeam->teamName }} has no roster history</p>
                <div id="activeRostersForm" style="display: center; text-align: center;">

                    <div class="event">
                        <div style="background-color:rgb(185, 182, 182); text-align: left; height: 200px;">
                            <br>
                            <div class="player-info">
                                <div class="player-image"
                                    style="background-image: url('https://www.svgrepo.com/download/347916/radio-button.svg')">
                                </div>
                                <span>Dota</span>
                            </div>
                            <div class="player-info">
                                <div class="player-image"
                                    style="background-image: url('https://www.svgrepo.com/download/347916/radio-button.svg')">
                                </div>
                                <span>Fifa</span>
                            </div>
                            <div class="player-info">
                                <div class="player-image"
                                    style="background-image: url('https://www.svgrepo.com/download/347916/radio-button.svg')">
                                </div>
                                <span>GTA V</span>
                            </div>
                        </div>
                        <div class="frame1">
                            <div class="container">
                                <div class="left-col">
                                    <p><img src="https://logos-world.net/wp-content/uploads/2020/12/Dota-2-Logo.png"
                                            class="logo2">
                                    <p style="font-size: 10px; text-align: left;">The Super Duper Extreme Dota
                                        Challenge
                                        League Season 1</p>
                                    </p>
                                </div>
                                <div class="right-col">
                                    <p><img src="https://logos-world.net/wp-content/uploads/2020/12/Dota-2-Logo.png"
                                            class="logo2">
                                    <p style="font-size: 12px; text-align: left;">Media Prima</p>
                                    <br>
                                    <p style="font-size: 12px; text-align: left;">1K Followers</p>
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

    </main>

    @include('CommonLayout.BootstrapV5Js')

    <script>
        document.addEventListener("DOMContentLoaded", function() {
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
        });

        function showTab(tabName) {
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => {
                content.style.display = 'none';
            });

            const selectedTab = document.getElementById(tabName);
            if (selectedTab) {
                selectedTab.style.display = 'block';

                if (tabName === 'Active Rosters') {
                    const activeRostersForm = document.getElementById('activeRostersForm');
                    activeRostersForm.style.display = 'block';
                }
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            showTab('Overview');
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

        function showTab(tabName) {
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => {
                content.style.display = 'none';
            });

            const selectedTab = document.getElementById(tabName);
            if (selectedTab) {
                selectedTab.style.display = 'block';

                if (tabName === 'Active Rosters') {
                    const activeRostersForm = document.getElementById('activeRostersForm');
                    activeRostersForm.style.display = 'block';
                }
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
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const currentMembersTab = document.getElementById('CurrentMembers');
            const pendingMembersTab = document.getElementById('PendingMembers');

            currentMembersTab.addEventListener('click', function() {
                showMemberTab('CurrentMembers');
            });

            pendingMembersTab.addEventListener('click', function() {
                showMemberTab('PendingMembers');
            });
        });

        function showMemberTab(tabName) {
            const memberTabs = document.querySelectorAll('.tab-content[data-type="member"]');
            memberTabs.forEach(tab => {
                tab.style.display = 'none';
            });

            const selectedTab = document.getElementById(tabName);
            if (selectedTab) {
                selectedTab.style.display = 'block';

                if (tabName === 'CurrentMembers') {} else if (tabName === 'PendingMembers') {}
            }
        }

        function approveMember(button) {
            const memberId = button.getAttribute('data-member-id');


            const url = "{{ route('participant.member.approve', ['id' => ':id']) }}".replace(':id', memberId);


            fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const memberRow = button.closest('tr');
                        memberRow.remove();
                    } else {
                        console.error('Error updating member status:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error approving member:', error);
                });
        }
    </script>

    <script>
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
