<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/registerTeam.css') }}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
</head>

<body>

    <div class="wrapper">

        <div class="first">

            <header><u>Select Team to Register</u></header>
        </div>
        <br>
        <br>

        <div class="dropdown">
            <button class="dropbtn" onclick="toggleDropdown()">
                <span id="selectedTeamLabel">Select Team</span>
                <span class="dropbtn-arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="feather feather-chevron-down">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </span>
            </button>
            <div class="dropdown-content" id="teamList">
                <form action="{{ url('/participant/home') }}" method="POST">
                    @csrf
                    <!-- Display validation errors -->
                    @if ($errors->any())
                    <div class="alert alert-danger">
                    <ul>
                    @foreach ($errors->all() as $error)
                     <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <input type="text" id="teamSearch" oninput="filterTeams()" placeholder="Search for teams...">
                <div>
                    @foreach ($selectTeam as $item)
                    <div class="team-info" onclick="selectOption(this, '{{ $item->teamName }}', 'css/images/logo.png')">
                        <img src="{{ asset('/assets/images/dota.png') }}" height="25px" width="50px">
                        <a href="#" id="teamNameAnchor" data-team-name="{{ $item->teamName }}">{{ $item->teamName }}</a>
                        <!-- Hidden input to store the selected team's name -->
                        <input type="hidden" id="selectedTeamInput" name="selectedTeamName">
                    </div>
                    @endforeach
                </div>
                
                
            </div>
        </div>

        <div class="sidebar">
            <p>All members in the team you select will be notified to join this event</p>

            <p>Registration will NOT be confirmed until enough team members have accepted to join and payment is complete. Once enough team members have accepted and the entry fee has been paid, registration can be confirmed.</p>

            <div class="remember-checkbox">
                <input type="checkbox" id="confirmationCheckbox" class="largerCheckbox">
                <p>Automatically confirm registration and lock in team when enough team members have accepted</p>
            </div>

            <div class="underline">
                <ul>
                    <p><b>WARNING: Once your registration has been confirmed, no changes can be made to the team lineup for this event.</b></p>
                </ul>
            </div>

            <div class="text-center">
                <input type="submit" class="choose-payment-method" id="submitButton" disabled value="Confirm Team and Notify">
            </div>

            <div class="text-center">
                <button class="oceans-gaming-default-button"> Cancel</button>
            </div>

        </div>
    </form>
    </div>

        <script>
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

        function selectOption(element, label, imageUrl) {
            // Add the selected class to the parent button
            const dropdownButton = document.querySelector(".dropdown .dropbtn");
            dropdownButton.classList.add('selected');

            // Set the selected team label
            const selectedTeamLabel = document.getElementById("selectedTeamLabel");
            selectedTeamLabel.textContent = label;

            // Handle other selection logic if needed
            var teamName = element.querySelector('a').getAttribute('data-team-name');
            document.getElementById('selectedTeamInput').value = teamName;

            // Close the dropdown
            closeDropDown(dropdownButton);
        }

        function closeDropDown(button) {
            const dropdownContent = button.nextElementSibling;
            dropdownContent.classList.remove('show');
        }

        // For Checkbox
        document.getElementById('confirmationCheckbox').addEventListener('change', function() {
        var submitButton = document.getElementById('submitButton');
        if (this.checked) {
        submitButton.removeAttribute('disabled');
        } else {
        submitButton.setAttribute('disabled', 'disabled');
        }
        });
        </script>

</body>

</html>
