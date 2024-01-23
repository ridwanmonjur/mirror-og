<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Management</title>
    <!-- Existing CSS links -->
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamAdmin.css') }}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.3.0/tagify.css">
    <link rel="stylesheet" href="{{ asset('/assets/css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="... (the integrity hash) ..." crossorigin="anonymous">

</head>

<body>
    @include('CommonLayout.NavbarforParticipant')
    

    <main>

        <div class="team-section">
            <div class="upload-container">
                <label for="image-upload" class="upload-label">
                    <div class="circle-container">
                        <div id="uploaded-image" class="uploaded-image"></div>
                        <button id="upload-button" class="upload-button" aria-hidden="true">Upload</button>
                    </div>
                </label>
                <input type="file" id="image-upload" accept="image/*" style="display: none;">
            </div>
            @foreach ($teamManage as $manage)
            <div class="team-names">
                <div class="team-info">
                    <h3 class="team-name" id="team-name">{{ $manage->teamName }}</h3>
                    <button class="gear-icon-btn">
                        <a href="/participant/registration-manage/{{ $manage['id'] }}">
                          <i class="fas fa-cog"></i>
                        </a>
                      </button>
                </div>
               
            </div>
            
            <p>We are an awesome team with awesome members! Come be awesome together! Play some games and win some prizes GGEZ!</p>
            @endforeach
        </div>

        <div class="tabs">
            <button class="tab-button" onclick="showTab('Overview')">Overview</button>
            <button class="tab-button" onclick="showTab('Members')">Members</button>
            <button class="tab-button" onclick="showTab('Active Rosters')">Active Rosters</button>
            <button class="tab-button" onclick="showTab('Roster History')">Roster History</button>
        </div>

        <div class="tab-content" id="Overview">
            <div style="padding-left: 200px;"><b>Recent Events</b></div>
            {{-- <div class="recent-events">
                <!-- Update the event-carousel section in the Overview tab content -->
                <div class="event-carousel">
                    <p style="text-align: center;">Team {{ $manage->teamName }} has no event history</p>
                    <button class="carousel-button" onclick="slideEvents(-1)" style="display: block;"><</button>&nbsp;&nbsp;&nbsp;
                    @foreach ($eventDetail as $event)
                    <div class="event-box" id="event1">
                    </div>
                    @endforeach
                    <button class="carousel-button" onclick="slideEvents(1)">></button>
                </div>
            </div> --}}

            <div class="recent-events">
                <!-- Update the event-carousel section in the Overview tab content -->
                <div class="event-carousel">
                    @if($joinEvents->isEmpty())
                     <p>No events available</p>
                    @else
                    <button class="carousel-button" onclick="slideEvents(-1)" style="display: block;"><</button>&nbsp;&nbsp;&nbsp;
                    @foreach($joinEvents as $key => $joinEvent)
                    <div class="event-box" id="event{{ $key + 1 }}" style="display: {{ $key === 0 ? 'block' : 'none' }};">
                    <div style="position: relative; height: 200px;">
    <div style="background-image: url('{{ $joinEvent->eventDetails->eventBanner ? 'https://driftwood.gg/storage/' . $joinEvent->eventDetails->eventBanner : 'https://driftwood.gg/storage/placeholder.jpg' }}'); background-size: cover; background-position: center; text-align: left; height: 200px;">
        <!-- Banner image goes here -->
    </div>
    <div style="position: absolute; top: 2%; left: 50%; transform: translate(-50%, -50%); z-index: 1; width: 50px; height: 50px; overflow: hidden; border-radius: 50%;">
        <!-- Circle image goes here -->
        @php
        $imagePathWithoutExtension = 'https://driftwood.gg/storage/images/event_details/' . strtolower($joinEvent->eventDetails->eventTier);
        $imageExtension = pathinfo($imagePathWithoutExtension, PATHINFO_EXTENSION);
        
        // Supported image extensions
        $supportedExtensions = ['jpg', 'jpeg', 'png'];
    
        // If the extension is not in the supported list, default to '.png'
        $imagePath = $imagePathWithoutExtension . (in_array(strtolower($imageExtension), $supportedExtensions) ? '' : '.png');
        @endphp
    
        <img src="{{ $imagePath }}" alt="Circle Image" style="width: 100%; height: 100%; object-fit: cover;">
    
        </div>
    
        </div>

                    <div class="frame1">    
                    <div class="container">
                    <div class="left-col">
                    <p>
                    <img src="https://i.pinimg.com/originals/8a/8b/50/8a8b50da2bc4afa933718061fe291520.jpg" class="logo2">
                    <p style="font-size: 10px; text-align: left; margin-top: 10px; margin-left: 10px;"> {{ $joinEvent->eventDetails->eventName }}</p>
                    </p>
                    </div>
                    <div class="right-col">
                    <p>
                    <img src="https://i.pinimg.com/originals/8a/8b/50/8a8b50da2bc4afa933718061fe291520.jpg" class="logo2">
                    <p style="font-size: 10px; text-align: left; margin-top: 10px; margin-left: 10px;"> {{ $joinEvent->eventDetails->user->organizer->companyName ?? 'Add' }}</p>
                                            
                    </p>
                    </div>
                    </div>
                    </div>

                    </div>
                    @endforeach
                    <button class="carousel-button" onclick="slideEvents(1)">></button>
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
        @foreach($eventsByTeam as $teamId => $users)
    @php
    $uniqueUsernames = collect($users)->unique('user.id');
    $usernamesCount = $uniqueUsernames->count();
    $creatorId = $manage->user->id;
    @endphp
    <div class="tab-content" id="Members" style="display: none; text-align: center;">
        <div class="member-tabs" style="display: flex; justify-content: center;">
            <button class="tab-button" onclick="showMemberTab('CurrentMembers')">Current Members</button>
            <button class="tab-button" onclick="showMemberTab('PendingMembers')">Pending Members</button>
        </div>
        <div class="tab-content" id="CurrentMembers" data-type="member" style="display: none; text-align: center;">
        <p style="text-align: center;">Team {{ $manage->teamName }} has {{ $usernamesCount }} members</p>
        <table class="member-table">
            <tbody>
                {{-- Display the creator's name --}}
                <tr class="st">
                    <td>
                        <div class="player-info">
                            <div class="player-image" style="background-image: url('https://www.vhv.rs/dpng/d/511-5111355_register-super-admin-icon-png-transparent-png.png')"></div>
                            <span>{{ $manage->user->name }}</span>
                        </div>
                    </td>
                    <td class="flag-cell">
                        <img class="nationality-flag" src="{{ asset('/assets/images/china.png') }}" alt="User's flag">
                    </td>
                </tr>

                {{-- Display unique member names excluding the creator --}}
                @foreach($uniqueUsernames as $user)
                    @if($user['user']->id !== $creatorId)
                        <tr class="st">
                            <td>
                                <div class="player-info">
                                    <div class="player-image" style="background-image: url('https://cdn-icons-png.flaticon.com/512/149/149071.png')"></div>
                                    <span>{{ $user['user']->name }}</span>
                                </div>
                            </td>
                            <td class="flag-cell">
                                <img class="nationality-flag" src="{{ asset('/assets/images/china.png') }}" alt="User's flag">
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="tab-content" id="PendingMembers" data-type="member" style="display: none; text-align: center;">
        <p style="text-align: center;">Pending Members</p>
        <table class="member-table">
            <tbody>
                @foreach($pendingMembers as $pendingMember)
                    <tr class="st">
                        <td>
                            <div class="player-info">
                                <div class="player-image" style="background-image: url('{{ $pendingMember->user->profile_image_url }}')"></div>
                                <span>{{ $pendingMember->user->name }}</span>
                            </div>
                        </td>
                        <td class="flag-cell">
                            <img class="nationality-flag" src="{{ asset('/assets/images/china.png') }}" alt="User's flag">
                        </td>
                        <td>
                            @foreach($teamManage as $team)
                            @if(auth()->user()->id == $team->user_id) <!-- Check if the current user is the team creator -->
                            <button onclick="approveMember('{{ $pendingMember->id }}')">✔</button>
                            @endif
                            @endforeach
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    </div>
    @endforeach
                    @endif
                

        <div class="tab-content" id="Active Rosters" style="display: center;">

            <p style="text-align: center;">Team {{ $manage->teamName }} has no active rosters</p>
            {{-- <div id="activeRostersForm" style="display: center; text-align: center;">

                <div class="event">
                    <div style="background-color:rgb(185, 182, 182); text-align: left; height: 200px;">
                        <br>
                        <div class="player-info">
                            <div class="player-image" style="background-image: url('https://www.svgrepo.com/download/347916/radio-button.svg')"></div>
                            <span>Dota</span>
                        </div>
                        <div class="player-info">
                            <div class="player-image" style="background-image: url('https://www.svgrepo.com/download/347916/radio-button.svg')"></div>
                            <span>Fifa</span>
                        </div>
                        <div class="player-info">
                            <div class="player-image" style="background-image: url('https://www.svgrepo.com/download/347916/radio-button.svg')"></div>
                            <span>GTA V</span>
                        </div>
                    </div>
                    <div class="frame1">
                        <div class="container">
                            <div class="left-col">
                                <p><img src="https://logos-world.net/wp-content/uploads/2020/12/Dota-2-Logo.png" class="logo2">
                                    <p style="font-size: 10px; text-align: left;">The Super Duper Extreme Dota Challenge League Season 1</p>
                                </p>
                            </div>
                            <div class="right-col">
                                <p><img src="https://logos-world.net/wp-content/uploads/2020/12/Dota-2-Logo.png" class="logo2">
                                    <p style="font-size: 12px; text-align: left;">Media Prima</p>
                                    <br>
                                    <p style="font-size: 12px; text-align: left;">1K Followers</p>
                                </p>
                            </div>
                        </div>

                    </div>
                </div>

            </div> --}}
        </div>

        <div class="tab-content" id="Roster History" style="display: none;">
            <p style="text-align: center;">Team {{ $manage->teamName }} has no roster history</p>
            {{-- <div id="activeRostersForm" style="display: center; text-align: center;">

                <div class="event">
                    <div style="background-color:rgb(185, 182, 182); text-align: left; height: 200px;">
                        <br>
                        <div class="player-info">
                            <div class="player-image" style="background-image: url('https://www.svgrepo.com/download/347916/radio-button.svg')"></div>
                            <span>Dota</span>
                        </div>
                        <div class="player-info">
                            <div class="player-image" style="background-image: url('https://www.svgrepo.com/download/347916/radio-button.svg')"></div>
                            <span>Fifa</span>
                        </div>
                        <div class="player-info">
                            <div class="player-image" style="background-image: url('https://www.svgrepo.com/download/347916/radio-button.svg')"></div>
                            <span>GTA V</span>
                        </div>
                    </div>
                    <div class="frame1">
                        <div class="container">
                            <div class="left-col">
                                <p><img src="https://logos-world.net/wp-content/uploads/2020/12/Dota-2-Logo.png" class="logo2">
                                    <p style="font-size: 10px; text-align: left;">The Super Duper Extreme Dota Challenge League Season 1</p>
                                </p>
                            </div>
                            <div class="right-col">
                                <p><img src="https://logos-world.net/wp-content/uploads/2020/12/Dota-2-Logo.png" class="logo2">
                                    <p style="font-size: 12px; text-align: left;">Media Prima</p>
                                    <br>
                                    <p style="font-size: 12px; text-align: left;">1K Followers</p>
                                </p>
                            </div>
                        </div>

                    </div>
                </div>

            </div> --}}
        </div>

    </main>



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
                    uploadedImage.style.backgroundImage = url("https://www.creativefabrica.com/wp-content/uploads/2022/07/10/tiger-logo-design-Graphics-33936667-1-580x387.jpg");
                };

                reader.readAsDataURL(file);
            }
        });
    });
    
    function showTab(tabName) {
        // Hide all tab contents
        const tabContents = document.querySelectorAll('.tab-content');
        tabContents.forEach(content => {
            content.style.display = 'none';
        });

        // Show the selected tab content
        const selectedTab = document.getElementById(tabName);
        if (selectedTab) {
            selectedTab.style.display = 'block';

            // Show the form if the "Active Rosters" tab is selected
            if (tabName === 'Active Rosters') {
                const activeRostersForm = document.getElementById('activeRostersForm');
                activeRostersForm.style.display = 'block';
            }
        }
    }

    // Show the default tab content (Overview) on page load
    document.addEventListener("DOMContentLoaded", function() {
        showTab('Overview');
    });

    // Update the slideEvents function to toggle visibility of events dynamically
    function slideEvents(direction) {
        const eventBoxes = document.querySelectorAll('.event-box');

        // Find the currently visible events
        const visibleEvents = Array.from(eventBoxes).filter(eventBox => eventBox.style.display !== 'none');

        // Hide all events
        eventBoxes.forEach(eventBox => (eventBox.style.display = 'none'));

        let startIndex = 0;

        if (visibleEvents.length > 0) {
            // If there are visible events, calculate the starting index based on the direction
            startIndex = (Array.from(eventBoxes).indexOf(visibleEvents[0]) + direction + eventBoxes.length) % eventBoxes.length;
        }

        // Show at most 2 events based on the starting index
        for (let i = 0; i < Math.min(2, eventBoxes.length); i++) {
            const index = (startIndex + i + eventBoxes.length) % eventBoxes.length;
            eventBoxes[index].style.display = 'block';
        }
    }

    function showTab(tabName) {
        // Hide all tab contents
        const tabContents = document.querySelectorAll('.tab-content');
        tabContents.forEach(content => {
            content.style.display = 'none';
        });

        // Show the selected tab content
        const selectedTab = document.getElementById(tabName);
        if (selectedTab) {
            selectedTab.style.display = 'block';

            // Show the form if the "Active Rosters" tab is selected
            if (tabName === 'Active Rosters') {
                const activeRostersForm = document.getElementById('activeRostersForm');
                activeRostersForm.style.display = 'block';
            }
        }
    }

    // i added this for a recentl bugs 

    function initializeEventsDisplay() {
    const eventBoxes = document.querySelectorAll('.event-box');

    // Hide all events
    eventBoxes.forEach(eventBox => (eventBox.style.display = 'none'));

    // Show the first two events
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
        // ... (your existing code)

        // Additional code to handle member tabs
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
        // Hide all member tabs
        const memberTabs = document.querySelectorAll('.tab-content[data-type="member"]');
        memberTabs.forEach(tab => {
            tab.style.display = 'none';
        });

        // Show the selected member tab
        const selectedTab = document.getElementById(tabName);
        if (selectedTab) {
            selectedTab.style.display = 'block';

            // Load relevant data based on the tab
            if (tabName === 'CurrentMembers') {
                // Code to load data for current members
            } else if (tabName === 'PendingMembers') {
                // Code to load data for pending members
            }
        }
    }
</script>

</body>

