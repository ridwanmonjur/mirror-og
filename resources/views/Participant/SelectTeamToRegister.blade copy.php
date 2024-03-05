<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="{{ asset('/assets/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/registerTeam.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>
    @include('CommonLayout.NavbarForAnother')
    <main>
        <form action="{{ route('participant.selectTeamToJoin.action', ['id' => $id]) }}" method="POST">
            @csrf
            <div class="wrapper mx-2 ">
                <div class="first">
                    <header><u>Select Team to Register</u></header>
                    <br>
                    <br>
                    <div class="dropdown">
                        <button type="button" class="dropbtn" onclick="toggleDropdown()">
                            <span id="selectedTeamLabel">Select Team</span>
                            <span class="dropbtn-arrow">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </span>
                        </button>
                        <div class="dropdown-content" id="teamList">
                            <input type="text" id="teamSearch" oninput="filterTeams()"
                                placeholder="Search for teams...">
                            <div>
                                <input type="hidden" name="selectedTeamId" value="">
                                @foreach ($selectTeam as $item)
                                    <div class="team-info"
                                        onclick="selectOption(this, '{{ addslashes($item->teamName) }}', '{{$item->id}}')">
                                        <img src="{{ asset('/assets/images/dota.png') }}" height="25px" width="50px">
                                        <a class="teamNameAnchor"
                                            data-team-id="{{ $item->id }}">{{ $item->teamName }}</a>
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    </div>
                </div>

                <div class="sidebar">
                    <p>All members in the team you select will be notified to join this event</p>

                    <p>Registration will NOT be confirmed until enough team members have accepted to join and
                        payment is
                        complete. Once enough team members have accepted and the entry fee has been paid,
                        registration
                        can
                        be confirmed.</p>

                    {{-- <div class="remember-checkbox">
                    <input type="checkbox" id="confirmationCheckbox" class="largerCheckbox">
                    <p>Automatically confirm registration and lock in team when enough team members have accepted</p>
                </div>

                <div class="underline">
                    <ul>
                        <p><b>WARNING: Once your registration has been confirmed, no changes can be made to the team
                                lineup for this event.</b></p>
                    </ul>
                </div> --}}

                    <div class="text-center">
                        <button disabled class="oceans-gaming-default-button" type="submit"> 
                        Confirm Team and Notify
                        </button>
                    </div>
                    <div class="text-center mt-2">
                        <button
                            type="button"
                            onclick="goToCancelButton();" 
                            class="oceans-gaming-default-button oceans-gaming-white-button"> Cancel</button>
                    </div>

                </div>

            </div>
        </form>
    </main>
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
    @include('CommonLayout.BootstrapV5Js')

</body>

</html>
