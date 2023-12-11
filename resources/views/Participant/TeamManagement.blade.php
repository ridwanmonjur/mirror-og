@extends('Layout.HeadTag')

@section('content')
    <nav class="navbar">
        <div class="logo">
            <img width="160px" height="60px" src="css/images/logo-default.png" alt="">
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
            <img width="50px" height="40px" src="css/images/navbar-account.png" alt="">
            <img width="70px" height="40px" src="css/images/navbar-crown.png" alt="">
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
            <h3 class="team-name" id="team-name">Team Fnatic</h3>
            <p>We are an awesome team with awesome members! Come be awesome together! Play some games and win some prizes GGEZ!</p>
        </div>

        <div class="tabs">
            <button class="tab-button" onclick="showTab('Overview')">Overview</button>
            <button class="tab-button" onclick="showTab('Members')">Members</button>
            <button class="tab-button" onclick="showTab('Active Rosters')">Active Rosters</button>
            <button class="tab-button" onclick="showTab('Roster History')">Roster History</button>
        </div>

        <div class="tab-content" id="Overview">
            <div style="padding-left: 200px;"><b>Recent Events</b></div>
            <div class="recent-events">
                <!-- Update the event-carousel section in the Overview tab content -->
                <div class="event-carousel">
                    <button class="carousel-button" onclick="slideEvents(-1)" style="display: block;"><</button>&nbsp;&nbsp;&nbsp;
                    <div class="event-box" id="event1">
                        <!-- Event 1 content -->
                        <h3>Event 1</h3>
                        <p>Date: October 15, 2023</p>
                        <p>Location: Virtual</p>
                    </div>
                    <div class="event-box" id="event2">
                        <!-- Event 2 content -->
                        <h3>Event 2</h3>
                        <p>Date: November 5, 2023</p>
                        <p>Location: Online</p>
                    </div>
                    <div class="event-box" id="event3" style="display: none;">
                        <!-- Event 3 content (hidden by default) -->
                        <h3>Event 3</h3>
                        <p>Date: November 10, 2023</p>
                        <p>Location: Onsite</p>
                    </div>
                    <div class="event-box" id="event4" style="display: none;">
                        <!-- Event 3 content (hidden by default) -->
                        <h3>Event 4</h3>
                        <p>Date: November 12, 2023</p>
                        <p>Location: Onsite</p>
                    </div>
                    <div class="event-box" id="event5" style="display: none;">
                        <!-- Event 3 content (hidden by default) -->
                        <h3>Event 5</h3>
                        <p>Date: February 20, 2023</p>
                        <p>Location: Online</p>
                    </div>
                    <div class="event-box" id="event6" style="display: none;">
                        <!-- Event 3 content (hidden by default) -->
                        <h3>Event 6</h3>
                        <p>Date: December 23, 2023</p>
                        <p>Location: Onsite</p>
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
                            <img src="css/images/fnatic.jpg" alt="Trophy" class="trophy">
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
            <p style="text-align: center;">Team Fnatic has 4 members</p>
            <table class="member-table">
                <tbody>
                    <tr class="st">
                        <td>
                            <div class="player-info">
                                <div class="player-image" style="background-image: url('css/images/dota.png')"></div>
                                <span>John Doe</span>
                            </div>
                        </td>
                        <td class="flag-cell">
                            <img class="nationality-flag" src="css/images/fnatic.jpg" alt="USA flag">
                        </td>
                    </tr>
                    <tr class="nd">
                        <td>
                            <div class="player-info">
                                <div class="player-image" style="background-image: url('css/images/dota.png')"></div>
                                <span>Jane Smith</span>
                            </div>
                        </td>
                        <td class="flag-cell">
                            <img class="nationality-flag" src="css/images/fnatic.jpg" alt="Canada flag">
                        </td>
                    </tr>
                    <!-- Add more rows as needed -->
                    <tr class="st">
                        <td>
                            <div class="player-info">
                                <div class="player-image" style="background-image: url('css/images/fnatic.jpg')"></div>
                                <span>John Doe</span>
                            </div>
                        </td>
                        <td class="flag-cell">
                            <img class="nationality-flag" src="css/images/fnatic.jpg" alt="USA flag">
                        </td>
                    </tr>
                    <tr class="nd">
                        <td>
                            <div class="player-info">
                                <div class="player-image" style="background-image: url('css/images/fnatic.jpg')"></div>
                                <span>Jane Smith</span>
                            </div>
                        </td>
                        <td class="flag-cell">
                            <img class="nationality-flag" src="css/images/fnatic.jpg" alt="Canada flag">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="tab-content" id="Active Rosters" style="display: center;">

            <p style="text-align: center;">Team Fnatic has no active rosters</p>
            <div id="activeRostersForm" style="display: center; text-align: center;">

                <div class="event">
                    <div style="background-color:rgb(185, 182, 182); text-align: left; height: 200px;">
                        <br>
                        <div class="player-info">
                            <div class="player-image" style="background-image: url('css/images/dota.png')"></div>
                            <span>John Doe</span>
                        </div>
                        <div class="player-info">
                            <div class="player-image" style="background-image: url('css/images/dota.png')"></div>
                            <span>John Doe</span>
                        </div>
                        <div class="player-info">
                            <div class="player-image" style="background-image: url('css/images/dota.png')"></div>
                            <span>John Doe</span>
                        </div>
                    </div>
                    <div class="frame1">
                        <div class="container">
                            <div class="left-col">
                                <p><img src="css/images/dota.png" class="logo2">
                                    <p style="font-size: 10px; text-align: left;">The Super Duper Extreme Dota Challenge League Season 1</p>
                                </p>
                            </div>
                            <div class="right-col">
                                <p><img src="css/images/dota.png" class="logo2">
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
            <p style="text-align: center;">Team Fnatic has no roster history</p>
            <div id="activeRostersForm" style="display: center; text-align: center;">

                <div class="event">
                    <div style="background-color:rgb(185, 182, 182); text-align: left; height: 200px;">
                        <br>
                        <div class="player-info">
                            <div class="player-image" style="background-image: url('css/images/dota.png')"></div>
                            <span>John Doe</span>
                        </div>
                        <div class="player-info">
                            <div class="player-image" style="background-image: url('css/images/dota.png')"></div>
                            <span>John Doe</span>
                        </div>
                        <div class="player-info">
                            <div class="player-image" style="background-image: url('css/images/dota.png')"></div>
                            <span>John Doe</span>
                        </div>
                    </div>
                    <div class="frame1">
                        <div class="container">
                            <div class="left-col">
                                <p><img src="css/images/dota.png" class="logo2">
                                    <p style="font-size: 10px; text-align: left;">The Super Duper Extreme Dota Challenge League Season 1</p>
                                </p>
                            </div>
                            <div class="right-col">
                                <p><img src="css/images/dota.png" class="logo2">
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

@endsection