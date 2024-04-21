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
@auth
    @php
        if (!isset($user)) {
            $user = auth()->user();
        }
    @endphp
@endauth
<body>
    @include('CommonPartials.NavbarGoToSearchPage')
    @include('CommonPartials.BootstrapV5Js')

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
                    {{-- @if ($selectTeam->isEdited)
                        <h2>{{$selectTeam->nickname}} <img src="css/images/edit-text.png" class="icons-game"></h2>
                        <h5>{{$selectTeam->name}}, {{$selectTeam->age}}</h5>
                        <p>{{$selectTeam->bio}}</p>
                        <img src="css/images/pin.png" class="icons"> USA&nbsp;&nbsp;&nbsp;&nbsp;
                        <img src="css/images/link.png" class="icons"> <a href="www.driftwood.gg"
                            style="text-decoration: none; color: black;">driftwood.gg</a>&nbsp;&nbsp;&nbsp;&nbsp;
                        <img src="css/images/user.png" class="icons"> Joined 5 January 2024<br>
                        <br>
                        <img src="css/images/dota.png" class="icons-game"> Dota 2&nbsp;&nbsp;&nbsp;&nbsp;
                        <img src="css/images/lol.png" class="icons-game"> League Of Legends&nbsp;&nbsp;&nbsp;&nbsp;
                        <img src="css/images/valo.jpg" class="icons-game"> Valorant<br>
                        <br>
                    @else
                        <h2>{{$selectTeam->nickname  }} <img src="css/images/edit-text.png" class="icons-game"></h2>
                        <h5>{{$selectTeam->name}}, 24</h5>
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
                    @endif --}}
                </div>
            </div>
        </div>
        <div class="tabs">
            <button class="tab-button  outer-tab tab-button-active"
                onclick="showTab(event, 'Overview', 'outer-tab')">Overview</button>
            <button class="tab-button outer-tab" onclick="showTab(event, 'Activity', 'outer-tab')">Activity</button>
            <button class="tab-button outer-tab" onclick="showTab(event, 'Events', 'outer-tab')">Events</button>
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
                    @if (!isset($joinEvents[1]))
                        <div class="d-flex justify-content-center event-carousel-works">
                            @foreach ($joinEvents as $key => $joinEvent)
                                @include('Participant.Partials.RosterView',  ['isRegistrationView' => false])
                            @endforeach
                        </div>
                    @else
                        <div class="event-carousel-styles event-carousel-works">
                            @foreach ($joinEvents as $key => $joinEvent)
                                @include('Participant.Partials.RosterView',  ['isRegistrationView' => false])
                            @endforeach
                        </div>
                    @endif
                 
                @endif
            </div>

            <div class="team-info">
                <div class="showcase">
                    <div><b>Showcase</b></div>
                    <br>
                    <div @class(["showcase-box d-none-until-hover-parent" , 
                            "d-flex justify-content-between flex-wrap" => !isset($awardList[2])
                    ])>
                        <div>
                            <p>Events Joined: {{ $totalEventsCount }}</p>
                            <p>Wins: {{ $wins }}</p>
                            <p>Win Streak: {{ $streak }}</p>
                        </div>
                        <div class="d-none-until-hover">
                            <div class="d-flex justify-content-between w-100 h-100">
                                @foreach ($awardList as $award)
                                    <div>
                                        <img src="{{ '/' . 'storage/' . $award->awards_image }} " alt="Trophy" class="trophy me-2">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="achievements">
                    <div><b>Achievements</b></div>
                    @if (!isset($achievementList[0]))
                        <ul class="achievement-list mt-4">
                            <p>No achievements available</p>
                        </ul>
                    @else
                        <ul class="achievement-list">
                            @foreach ($achievementList as $achievement)
                                <li>
                                    <span class="additional-text d-flex justify-content-between">
                                        <span>
                                        {{ $achievement->title }} ({{ \Carbon\Carbon::parse($achievement->created_at)->format('Y') }})
                                        </span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                        <path d="m10.97 4.97-.02.022-3.473 4.425-2.093-2.094a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05"/>
                                        </svg>
                                    </span><br>
                                    <span class="ps-2"> {{ $achievement->description }} </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    </ul>
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

            <div class="mx-auto" style="width: 80%;"><b>Active Events</b></div>
            
            @if (!isset($joinEventsActive[0]))
                <p class="text-center">
                    This profile has no active events
                </p>
            @else
                <div id="activeRostersForm" class="tex-center mx-auto">
                    <br>
                    @foreach ($joinEventsActive as $key => $joinEvent)
                        @include('Participant.Partials.RosterView', ['isRegistrationView' => false])
                        <br><br>
                    @endforeach
                </div>
            @endif

            <div class="mx-auto" style="width: 80%;"><b>Past Events</b></div>

            @if (!isset($joinEventsHistory[0]))
                <p class="text-center">
                    This profile have no past events
                </p>
            @else
                <div id="activeRostersForm" class="tex-center mx-auto">
                    <br>
                    @foreach ($joinEventsHistory as $key => $joinEvent)
                        @include('Participant.Partials.RosterView', ['isRegistrationView' => false])
                        <br><br>
                    @endforeach
                </div>
            @endif
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
        window.location.href = "{{ route('public.participant.view', ['id' => ':id']) }}"
            .replace(':id', userId);
    }
</script>

</html>
