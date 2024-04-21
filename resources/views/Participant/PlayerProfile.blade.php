<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/player_profile.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamAdmin.css') }}">
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
                    <p>This is the player bio. The character limit should be up to 150 words. This field should accept
                        emojis.</p>
                    <img src="css/images/pin.png" class="icons"> USA&nbsp;&nbsp;&nbsp;&nbsp;
                    <img src="css/images/link.png" class="icons"> <a href="www.driftwood.gg"
                        style="text-decoration: none; color: black;">driftwood.gg</a>&nbsp;&nbsp;&nbsp;&nbsp;
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
            <button class="tab-button  outer-tab tab-button-active"
                onclick="showTab(event, 'Overview', 'outer-tab')">Overview</button>
            <button class="tab-button outer-tab" onclick="showTab(event, 'Activity', 'outer-tab')">Activity</button>
            <button class="tab-button outer-tab" onclick="showTab(event, 'Events', 'outer-tab')">Events</button>
            <button class="tab-button outer-tab" onclick="showTab(event, 'Teams', 'outer-tab')">Teams</button>
        </div>
        <div class="tab-content outer-tab" id="Overview">
            <div style="padding-left: 200px;"><b>Recent Events</b></div>
            <div class="recent-events">
                <!-- Update the event-carousel section in the Overview tab content -->

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
                            <li>Best Team Collaboration - LAN Event (2022) <span class="achievement-complete">✔️</span>
                            </li>
                            <!-- Add more achievements as needed -->
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-content outer-tab d-none" id="Activity">
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
                                <div class="player-image" style="background-image: url('css/images/dota.png')">
                                </div>
                                <span>Name won Prize in Media Prima's event. The Super Duper Extreme Dota Challenge
                                    League Season 1.</span>
                            </div>
                        </td>
                    </tr>
                    <tr class="nf">
                        <td>
                            <div class="player-info">
                                <div class="player-image" style="background-image: url('css/images/dota.png')">
                                </div>
                                <span>Name won Prize in Harmony's event. Competition Name.</span>
                            </div>
                        </td>
                    </tr>
                    <tr class="nf">
                        <td>
                            <div class="player-info">
                                <div class="player-image" style="background-image: url('css/images/fnatic.jpg')">
                                </div>
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
                                <div class="player-image" style="background-image: url('css/images/dota.png')">
                                </div>
                                <span>Name won Prize in Media Prima's event. The Super Duper Extreme Dota Challenge
                                    League Season 1.</span>
                            </div>
                        </td>
                    </tr>
                    <tr class="nf">
                        <td>
                            <div class="player-info">
                                <div class="player-image" style="background-image: url('css/images/dota.png')">
                                </div>
                                <span>Name won Prize in Harmony's event. Competition Name.</span>
                            </div>
                        </td>
                    </tr>
                    <tr class="nf">
                        <td>
                            <div class="player-info">
                                <div class="player-image" style="background-image: url('css/images/fnatic.jpg')">
                                </div>
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
                                <div class="player-image" style="background-image: url('css/images/dota.png')">
                                </div>
                                <span>Name won Prize in Media Prima's event. The Super Duper Extreme Dota Challenge
                                    League Season 1.</span>
                            </div>
                        </td>
                    </tr>
                    <tr class="nf">
                        <td>
                            <div class="player-info">
                                <div class="player-image" style="background-image: url('css/images/dota.png')">
                                </div>
                                <span>Name won Prize in Harmony's event. Competition Name.</span>
                            </div>
                        </td>
                    </tr>
                    <tr class="nf">
                        <td>
                            <div class="player-info">
                                <div class="player-image" style="background-image: url('css/images/fnatic.jpg')">
                                </div>
                                <span>Name has joined Team Name.</span>
                            </div>
                        </td>
                    </tr>
                    <tr class="nf">
                        <td>
                            <div class="player-info">
                                <div class="player-image" style="background-image: url('css/images/fnatic.jpg')">
                                </div>
                                <span>Name has left Team Name.</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>

        <div class="tab-content outer-tab d-none" id="Events">

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
                                <p style="font-size: 10px; text-align: left;">The Super Duper Extreme Dota
                                    Challenge League Season 1</p>
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
                                <p style="font-size: 10px; text-align: left;">The Super Duper Extreme Dota
                                    Challenge League Season 1</p>
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

        <div class="tab-content outer-tab d-none" id="Teams">
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
                                <div class="player-image" style="background-image: url('css/images/dota.png')">
                                </div>
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
                                    <div class="player-image" style="background-image: url('css/images/dota.png')">
                                    </div>
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
                                <div class="player-image" style="background-image: url('css/images/dota.png')">
                                </div>
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
                                    <div class="player-image" style="background-image: url('css/images/dota.png')">
                                    </div>
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
                                <div class="player-image" style="background-image: url('css/images/dota.png')">
                                </div>
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
                                    <div class="player-image" style="background-image: url('css/images/dota.png')">
                                    </div>
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
                                <div class="player-image" style="background-image: url('css/images/dota.png')">
                                </div>
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
                                    <div class="player-image" style="background-image: url('css/images/dota.png')">
                                    </div>
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
    function reddirectToLoginWithIntened(route) {
        route = encodeURIComponent(route);
        let url = "{{ route('participant.signin.view') }}";
        url += `?url=${route}`;
        window.location.href = url;
    }

    function showTab(event, tabName, extraClassNameToFilter = "outer-tab") {
        const tabContents = document.querySelectorAll(`.tab-content.${extraClassNameToFilter}`);
        tabContents.forEach(content => {
            content.classList.add("d-none");
        });
        console.log({
            tabContents
        });

        const selectedTab = document.getElementById(tabName);
        selectedTab.classList.remove('d-none');
        selectedTab.classList.add('tab-button-active');
        console.log({
            selectedTab
        });

        const tabButtons = document.querySelectorAll(`.tab-button-active.${extraClassNameToFilter}`);
        tabButtons.forEach(button => {
            button.classList.remove("tab-button-active");
        });
        console.log({
            tabButtons
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


    function redirectToProfilePage(userId) {
        window.location.href = "{{ route('participant.profile.view', ['id' => ':id']) }}"
            .replace(':id', userId);
    }
</script>

</html>
