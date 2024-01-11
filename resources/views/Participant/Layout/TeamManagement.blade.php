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
    @include('CommonLayout.NavbarGoToSearchPage')
    

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
                    <button class="gear-icon-btn"><i class="fas fa-cog"></i></button>
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
                        <div style="background-image: url('{{ $joinEvent->eventDetails->eventBanner ? $joinEvent->eventDetails->eventBanner : 'data:image/png;base64,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' }}'); background-size: cover; background-position: center; text-align: left; height: 200px;">
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
                    <p style="font-size: 10px; text-align: left; margin-top: 10px; margin-left: 10px;"> {{ $joinEvent->eventDetails->eventName }}</p>
                                            
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
        @endphp
        <div class="tab-content" id="Members" style="display: none;">
            <p style="text-align: center;">Team {{ $manage->teamName }} has {{ $usernamesCount }} members</p>
            <table class="member-table">
                <tbody>
                    
                    @foreach($users as $user)
                    <tr class="st">
                        <td>
                            <div class="player-info">
                                <div class="player-image" style="background-image: url('css/images/dota.png')"></div>
                                <span>{{ $user['user']->name }}</span>
                            </div>
                        </td>
                        <td class="flag-cell">
                            <img class="nationality-flag" src="{{ asset('/assets/images/china.png') }}" alt="USA flag">
                        </td>
                    </tr>
                    @endforeach
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>

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
</script>
</body>

