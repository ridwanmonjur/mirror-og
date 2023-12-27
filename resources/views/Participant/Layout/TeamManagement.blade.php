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
</head>

<body>

    <nav class="navbar">
        <div class="logo">
            <img width="160px" height="60px" src="{{ asset('/assets/images/logo-default.png') }}" alt="">
        </div>
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu menu-toggle" onclick="toggleNavbar()">
            <line x1="3" y1="12" x2="21" y2="12"></line>
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <line x1="3" y1="18" x2="21" y2="18"></line>
        </svg>
        <div class="search-bar d-none-at-mobile">
            <input type="text" name="search" id="search" placeholder="Search for events">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
        </div>
        <div class="nav-buttons">
            <button class="oceans-gaming-default-button oceans-gaming-gray-button"> Where is moop? </button>
            <img width="50px" height="40px" src="{{ asset('/assets/images/navbar-account.png') }}" alt="">
            <img width="70px" height="40px" src="{{ asset('/assets/images/navbar-crown.png') }}" alt="">
        </div>
    </nav>
    <nav class="mobile-navbar d-centered-at-mobile d-none">
        <div class="search-bar search-bar-mobile ">
            <input type="text" name="search" id="search" placeholder="Search for events">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search" style="left: 40px;">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
        </div>
        <div class="nav-buttons search-bar-mobile d-centered-at-mobile">
            <img width="50px" height="40px" src="css/images/navbar-account.png" alt="">
            <img width="70px" height="40px" src="css/images/navbar-crown.png" alt="">
        </div>
    </nav>

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
            <h3 class="team-name" id="team-name">{{ $manage->teamName }}</h3>
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
                @if($joinEvents->isEmpty())
                     <p>No events available</p>
                    @else
                    @foreach($joinEvents as $joinEvent)
                <div class="event-carousel">
                    <button class="carousel-button" onclick="slideEvents(-1)" style="display: block;"><</button>&nbsp;&nbsp;&nbsp;
                    <div class="event-box" id="event1">
                        <!-- Event 1 content -->
                        <h3>{{ $joinEvent->eventDetails->eventName }}</h3>
                        <p>Start Date: {{ $joinEvent->eventDetails->startDate }}</p>
                        <p>Location: Virtual</p>
                    </div>
                    <button class="carousel-button" onclick="slideEvents(1)">></button>
                </div>
                

            </div>

            <div class="team-info">
                <div class="showcase">
                    <div><b>Showcase</b></div>
                    <br>
                    <div class="showcase-box">
                        <div class="showcase-column">
                            <p>Events Joined: 10</p>
                            <p>Wins: 5</p>
                            <p>Win Streak: 3</p>
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
                        <li>First Place - Online Tournament (2023) <span class="achievement-complete">✔️</span></li>
                        <li>Best Team Collaboration - LAN Event (2022) <span class="achievement-complete">✔️</span></li>
                        <!-- Add more achievements as needed -->
                    </ul>
                </div>
            </div>
        </div>

        <div class="tab-content" id="Members" style="display: none;">
            <p style="text-align: center;">Team {{ $manage->teamName }} has 4 members</p>
            <table class="member-table">
                <tbody>
                    @foreach($joinEvent->user->teams as $team)
                    @foreach($team->members as $member)
                    <tr class="st">
                        <td>
                            <div class="player-info">
                                <div class="player-image" style="background-image: url('css/images/dota.png')"></div>
                                <span>{{ $member->user->name }}</span>
                            </div>
                        </td>
                        <td class="flag-cell">
                            <img class="nationality-flag" src="{{ asset('/assets/images/china.png') }}" alt="USA flag">
                        </td>
                    </tr>
                    @endforeach
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

