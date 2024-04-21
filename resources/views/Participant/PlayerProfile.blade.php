
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event creation</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/player_profile.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body>
    @include('CommonLayout.NavbarGoToSearchPage')
    @include('CommonLayout.BootstrapV5Js')

    <main>
        <div class="member-section">
            <div class="member-info">
                <div class="member-image">
                    <div class="upload-container">
                        <label for="image-upload" class="upload-label">
                            <div class="circle-container">
                                <div id="uploaded-image" class="uploaded-image"></div>
                                <button id="upload-button" class="upload-button" aria-hidden="true">Upload</button>
                            </div>
                        </label>
                        <input type="file" id="image-upload" accept="image/*" style="display: none;">
                    </div>
                </div>
                <div class="member-details">
                    <h2>NickName <img src="css/images/edit-text.png" class="icons-game"></h2>
                    <h5>Jack Wills, 24</h5>
                    <p>This is the player bio. The character limit should be up to 150 words. This field should accept emojis.</p>
                    <img src="css/images/pin.png" class="icons"> USA&nbsp;&nbsp;&nbsp;&nbsp;
                    <img src="css/images/link.png" class="icons"> <a href="www.driftwood.gg" style="text-decoration: none; color: black;">driftwood.gg</a>&nbsp;&nbsp;&nbsp;&nbsp;
                    <img src="css/images/user.png" class="icons"> Joined 5 January 2024<br>
                    <br>
                    <img src="css/images/dota.png" class="icons-game"> Dota 2&nbsp;&nbsp;&nbsp;&nbsp;
                    <img src="css/images/lol.png" class="icons-game"> League Of Legends&nbsp;&nbsp;&nbsp;&nbsp;
                    <img src="css/images/valo.jpg" class="icons-game"> Valorant<br>
                    <br>
                </div>
            </div>
        </div>
        <div class="tabs">
            <button class="tab-button" onclick="showTab('Overview')">Overview</button>
            <button class="tab-button" onclick="showTab('Activity')">Activity</button>
            <button class="tab-button" onclick="showTab('Events')">Events</button>
            <button class="tab-button" onclick="showTab('Teams')">Teams</button>
        </div>

        <div class="tab-content" id="Overview">
            <div style="padding-left: 200px;"><b>Recent Events</b></div>
            <div class="recent-events">
                <!-- Update the event-carousel section in the Overview tab content -->
                <div class="event-carousel">
                    <button class="carousel-button" onclick="slideEvents(-1)" style="display: block;"><</button>
                    <div id="event1" style="display: center; text-align: center; padding: 10px">

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

                    <div id="event2" style="display: center; text-align: center; padding: 10px">

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

        <div class="tab-content" id="Activity" style="display: none;">
            <table class="member-table">
                <tbody>
                    <tr class="nf">
                        <th>
                            <div class="player-info">
                                <div>New</div>
                            </div>
                        </th>
                    </tr>
                    <tr class="nf">
                        <td>
                            <div class="player-info">
                                <div class="player-image" style="background-image: url('css/images/dota.png')"></div>
                                <span>Name won Prize in Media Prima's event. The Super Duper Extreme Dota Challenge League Season 1.</span>
                            </div>
                        </td>
                    </tr>
                    <tr class="nf">
                        <td>
                            <div class="player-info">
                                <div class="player-image" style="background-image: url('css/images/dota.png')"></div>
                                <span>Name won Prize in Harmony's event. Competition Name.</span>
                            </div>
                        </td>
                    </tr>
                    <tr class="nf">
                        <td>
                            <div class="player-info">
                                <div class="player-image" style="background-image: url('css/images/fnatic.jpg')"></div>
                                <span>Name won Prize in Harmony's event. Competition Name.</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <table class="member-table">
                <tbody>
                    <tr class="nf">
                        <th>
                            <div class="player-info">
                                <div>Recent</div>
                            </div>
                        </th>
                    </tr>
                    <tr class="nf">
                        <td>
                            <div class="player-info">
                                <div class="player-image" style="background-image: url('css/images/dota.png')"></div>
                                <span>Name won Prize in Media Prima's event. The Super Duper Extreme Dota Challenge League Season 1.</span>
                            </div>
                        </td>
                    </tr>
                    <tr class="nf">
                        <td>
                            <div class="player-info">
                                <div class="player-image" style="background-image: url('css/images/dota.png')"></div>
                                <span>Name won Prize in Harmony's event. Competition Name.</span>
                            </div>
                        </td>
                    </tr>
                    <tr class="nf">
                        <td>
                            <div class="player-info">
                                <div class="player-image" style="background-image: url('css/images/fnatic.jpg')"></div>
                                <span>Name won Prize in Harmony's event. Competition Name.</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <table class="member-table">
                <tbody>
                    <tr class="nf">
                        <th>
                            <div class="player-info">
                                <div>Older</div>
                            </div>
                        </th>
                    </tr>
                    <tr class="nf">
                        <td>
                            <div class="player-info">
                                <div class="player-image" style="background-image: url('css/images/dota.png')"></div>
                                <span>Name won Prize in Media Prima's event. The Super Duper Extreme Dota Challenge League Season 1.</span>
                            </div>
                        </td>
                    </tr>
                    <tr class="nf">
                        <td>
                            <div class="player-info">
                                <div class="player-image" style="background-image: url('css/images/dota.png')"></div>
                                <span>Name won Prize in Harmony's event. Competition Name.</span>
                            </div>
                        </td>
                    </tr>
                    <tr class="nf">
                        <td>
                            <div class="player-info">
                                <div class="player-image" style="background-image: url('css/images/fnatic.jpg')"></div>
                                <span>Name has joined Team Name.</span>
                            </div>
                        </td>
                    </tr>
                    <tr class="nf">
                        <td>
                            <div class="player-info">
                                <div class="player-image" style="background-image: url('css/images/fnatic.jpg')"></div>
                                <span>Name has left Team Name.</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>

        <div class="tab-content" id="Events" style="display: center;">

            <div style="padding-left: 200px;"><b>Active Events</b></div>

            <div class="player_event">
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

            <div style="padding-left: 200px;"><b>Past Events</b></div>

            <div class="player_event">
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

        <div class="tab-content" id="Teams" style="display: none;">
            <table class="member-table">
                <div id="table-like">
                    <tr class="nf">
                        <th>
                            <div class="player-info">
                                <div>Current Teams</div>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <th>Team name</th>
                        <th>Region</th>
                        <th>Members</th>
                    </tr>
                    <tr class="st">
                        <td>
                            <div class="player-info">
                                <div class="player-image" style="background-image: url('css/images/dota.png')"></div>
                                <span>Team Name</span>
                            </div>
                        </td>
                        <td>China</td>
                        <td>5/5</td>
                    </tr>


                    <div>
                        <tr class="nd">
                            <td>
                                <div class="player-info">
                                    <div class="player-image" style="background-image: url('css/images/dota.png')"></div>
                                    <span>Fnatic</span>
                                </div>
                            </td>
                            <td>South Asia</td>
                            <td>4/8</td>
                        </tr>
                    </div>

                    <tr class="st">
                        <td>
                            <div class="player-info">
                                <div class="player-image" style="background-image: url('css/images/dota.png')"></div>
                                <span>Awesome Team</span>
                            </div>
                        </td>
                        <td>China</td>
                        <td>5/5</td>
                    </tr>


                    <div>
                        <tr class="nd">
                            <td>
                                <div class="player-info">
                                    <div class="player-image" style="background-image: url('css/images/dota.png')"></div>
                                    <span>Good Team</span>
                                </div>
                            </td>
                            <td>China</td>
                            <td>5/5</td>
                        </tr>
                    </div>

                </div>
            </table>

            <table class="member-table">
                <div id="table-like">
                    <tr>
                        <td><br></td>
                    </tr>
                    <tr class="nf">
                        <th>
                            <div class="player-info">
                                <div>Past Teams</div>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <th>Team name</th>
                        <th>Region</th>
                        <th>Members</th>
                    </tr>
                    <tr class="st">
                        <td>
                            <div class="player-info">
                                <div class="player-image" style="background-image: url('css/images/dota.png')"></div>
                                <span>Team Name</span>
                            </div>
                        </td>
                        <td>China</td>
                        <td>5/5</td>
                    </tr>


                    <div>
                        <tr class="nd">
                            <td>
                                <div class="player-info">
                                    <div class="player-image" style="background-image: url('css/images/dota.png')"></div>
                                    <span>GG Team</span>
                                </div>
                            </td>
                            <td>South Asia</td>
                            <td>4/8</td>
                        </tr>
                    </div>

                    <tr class="st">
                        <td>
                            <div class="player-info">
                                <div class="player-image" style="background-image: url('css/images/dota.png')"></div>
                                <span>Nice</span>
                            </div>
                        </td>
                        <td>China</td>
                        <td>5/5</td>
                    </tr>


                    <div>
                        <tr class="nd">
                            <td>
                                <div class="player-info">
                                    <div class="player-image" style="background-image: url('css/images/dota.png')"></div>
                                    <span>GG Boyz</span>
                                </div>
                            </td>
                            <td>China</td>
                            <td>5/5</td>
                        </tr>
                    </div>

                </div>
            </table>
        </div>

    </main>


</body>

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
                    uploadedImage.style.backgroundImage = url("css/images/fnatic.jpg");
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

            // Show the form if the "Events" tab is selected
            if (tabName === 'Events') {
                const activeRostersForm = document.getElementById('activeRostersForm');
                activeRostersForm.style.display = 'block';
            }
        }
    }
</script>

</html>