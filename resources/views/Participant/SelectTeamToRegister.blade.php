<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Team to Register</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/registerTeam.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/timeline.css') }}">

</head>

<body>
    @include('__CommonPartials.NavbarGoToSearchPage')
    <main>
        <div class="flexbox box-width back-next">
            <button onclick="goToNextScreen('step-1', 'none')" type="button"
                class="oceans-gaming-default-button oceans-gaming-transparent-button back-button"> Back </button>
            <button onclick="goToNextScreen('step-3', 'timeline-1')" type="button" class="oceans-gaming-default-button"
                onclick=""> Next > </button>
        </div>
        <div class="time-line-box mx-auto" id="timeline-box">
            <div class="swiper-container ps-5 text-center">
                <div class="swiper-wrapper ps-5">
                    <div class="swiper-slide swiper-slide__left" id="timeline-1">
                        <div class="timestamp" onclick="goToNextScreen('step-1', 'timeline-1')"><span
                                class="cat">Select Team</span></div>
                        <div class="status__left" onclick="goToNextScreen('step-1', 'timeline-1')">
                            <span><small></small></span></div>
                    </div>
                    <div class="swiper-slide" id="timeline-2">
                        <div class="timestamp" onclick="goToNextScreen('step-5', 'timeline-2')"><span>Receive
                                Notification</span></div>
                        <div class="status" onclick="goToNextScreen('step-5', 'timeline-2')">
                            <span><small></small></span></div>
                    </div>
                    <div class="swiper-slide" id="timeline-launch">
                        <div class="timestamp" onclick="goToNextScreen('step-launch-1', 'timeline-launch')"><span
                                class="date">Manage Members</span></div>
                        <div class="status" onclick="goToNextScreen('step-launch-1', 'timeline-launch')">
                            <span><small></small></span></div>
                    </div>
                    <div class="swiper-slide swiper-slide__right" id="timeline-payment">
                        <div class="timestamp"
                            onclick="goToNextScreen('step-payment', 'timeline-payment'); fillStepPaymentValues();">
                            <span>Manage Roster</span></div>
                        <div class="status__right"
                            onclick="goToNextScreen('step-payment', 'timeline-payment'); fillStepPaymentValues();">
                            <span><small></small></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="breadcrumb-top">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a onclick="goToNextScreen('step-1', 'timeline-1')">Categories</a></li>
                    <li class="breadcrumb-item"><a onclick="goToNextScreen('step-5', 'timeline-2')">Details</a></li>
                    <li class="breadcrumb-item"><a
                            onclick="goToNextScreen('step-payment', 'timeline-payment'); fillStepPaymentValues();">Payment</a>
                    </li>
                    <li class="breadcrumb-item"><a
                            onclick="goToNextScreen('step-launch-1', 'timeline-launch')">Launch</a></li>
                </ol>
            </nav>
        </div>
        <div class="wrapper-height">
            <div class="wrapper grid-2-at-screen mx-auto mx-2" style="background-color: #FFFBFB;">
                <div>

                    <header><u>Select Team to Register</u></header>
                    <br>
                    <div class="dropdown" data-bs-auto-close="outside">
                        <button type="button" class="dropbtn px-0 py-2" onclick="toggleDropdown()">
                            <span id="selectedTeamLabel">Select Team</span>
                            <span class="dropbtn-arrow">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </span>
                        </button>
                        <div class="dropdown-content" id="teamList" style="z-index: 999;">
                            <input type="text" id="teamSearch" oninput="filterTeams()"
                                placeholder="Search for teams...">
                            <div>
                                <form id="selectTeam"
                                    action="{{ route('participant.selectTeamToJoin.action', ['id' => $id]) }}"
                                    method="POST">
                                    @csrf
                                    <input type="hidden" name="selectedTeamId" value="">
                                </form>
                                @foreach ($selectTeam as $item)
                                    <div class="px-0 py-0 mx-0 my-2 cursor-pointer"
                                        onclick="selectOption(this, '{{ addslashes($item->teamName) }}', '{{ $item->id }}')">
                                        <img src="{{ '/storage' . '/' . $item->teamBanner }}" width="35px"
                                            height="35px" class="rounded-circle object-fit-cover"
                                            onerror="this.onerror=null;this.src='/assets/images/404.png';">
                                        <a class="d-inline" data-team-id="{{ $item->id }}">{{ $item->teamName }}
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @if ($count <= 5)
                            <form id="createTeam"
                                action="{{ route('participant.event.createTeam.redirect', ['id' => $id]) }}"
                                method="POST">
                                @csrf
                                <div>
                                    <br>
                                    <p>You have {{ $count }} teams. You can be part of maximum 5 teams!</p>
                                    @if ($count < 5)
                                        <p class="text-success"> You can still create {{ 5 - $count }} teams.</p>
                                        <button class="btn btn-link text-secondary px-0" type="submit">
                                            Create Another Team
                                        </button>
                                    @else
                                        <p class="text-red"> You cannot create more teams.</p>
                                    @endif
                                </div>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="mt-5">
                    <p>All members in the team you select will be notified to join this event</p>

                    <p>Registration will NOT be confirmed until enough team members have accepted to join and
                        payment is
                        complete. Once enough team members have accepted and the entry fee has been paid,
                        registration
                        can
                        be confirmed.
                    </p>

                    <div class="text-center">
                        <button form="selectTeam" disabled class="oceans-gaming-default-button" type="submit">
                            Confirm Team and Notify
                        </button>
                    </div>
                    <div class="text-center mt-2">
                        <button type="button" onclick="goToCancelButton();"
                            class="oceans-gaming-default-button oceans-gaming-white-button"> Cancel</button>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </main>
    <script src="{{ asset('/assets/js/event_creation/timeline.js') }}"></script>
    <script>
        function goToCancelButton() {
            let url = "{{ route('participant.event.view', $id) }}";
            window.location.href = url;
        }

        function toggleDropdown() {
            var dropdown = document.querySelector(".dropdown-content");
            dropdown.classList.toggle("show");
        }

        function filterTeams() {
            var input = document.getElementById("teamSearch").value.toLowerCase();
            var teams = document.getElementById("teamList");
            var teamDivs = teams.getElementsByTagName("div");

            for (var i = 0; i < teamDivs.length; i++) {
                var teamName = teamDivs[i].querySelector("a").textContent.toLowerCase();
                var teamLogo = teamDivs[i].querySelector("img");

                if (teamName.includes(input)) {
                    teamDivs[i].style.display = "block";
                    teamLogo.style.display = "block";
                } else {
                    teamDivs[i].style.display = "none";
                    teamLogo.style.display = "none";
                }
            }
        }

        function selectOption(element, label, teamId) {
            const dropdownButton = document.querySelector(".dropdown .dropbtn");
            dropdownButton.classList.add('selected');

            const selectedTeamLabel = document.getElementById("selectedTeamLabel");
            selectedTeamLabel.textContent = label;

            const selectedTeamInput = document.querySelector('input[name="selectedTeamId"]');
            selectedTeamInput.value = teamId;
            closeDropDown(dropdownButton);
            document.querySelector('button[disabled]').removeAttribute('disabled');
        }

        function closeDropDown(button) {
            const dropdownContent = button.nextElementSibling;
            dropdownContent.classList.remove('show');
        }
    </script>
    

</body>

</html>
