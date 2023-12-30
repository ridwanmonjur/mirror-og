<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="{{ asset('assets/css/participant/player_event_reg.css') }}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('/assets/css/app.css') }}">
</head>

<body>
    @include('CommonLayout.NavbarGoToSearchPage')

    <div class="wrapper">

        <div class="first">

            <header><u>Select Team to Register</u></header>
        </div>
        <br>
        <br>

        <div class="dropdown">
            <button class="dropbtn" onclick="toggleDropdown()">
                Select Team
                <span class="dropbtn-arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="feather feather-chevron-down">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </span>
            </button>
            <div class="dropdown-content" id="teamList">
                <input type="text" id="teamSearch" oninput="filterTeams()" placeholder="Search for teams...">
                <div>
                    <div class="team-info">
                        <img src="css/images/logo.png" height="25px" width="50px">
                        <a href="#">Team 1</a>
                    </div>
                </div>
                <div>
                    <div class="team-info">
                        <img src="css/images/logo.png" height="25px" width="50px">
                        <a href="#">Team 2</a>
                    </div>
                </div>
                <div>
                    <div class="team-info">
                        <img src="css/images/logo.png" height="25px" width="50px">
                        <a href="#">Team 3</a>
                    </div>
                </div>
                <div>
                    <div class="team-info">
                        <img src="css/images/logo.png" height="25px" width="50px">
                        <a href="#">Team 4</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- <button id="searchButton" onclick="filterTeams()">
            <div class="team-info">
                <div class="plus-icon">
                    <div class="circle">
                        <span class="plus">+</span>
                    </div>
                </div>
                Create new team
            </div>
        </button> -->

        <div class="sidebar">
            <p>All members in the team you select will be notified to join this event</p>

            <p>Registration will NOT be confirmed until enough team members have accepted to join and payment is complete. Once enough team members have accepted and the entry fee has been paid, registration can be confirmed.</p>

            <div class="remember-checkbox">
                <input type="checkbox" name="" class="largerCheckbox">
                <p>Automatically confirm registration and lock in team when enough team members have accepted</p>
            </div>

            <div class="underline">

                <ul>
                    <p><b>WARNING: Once your registration has been confirmed, no changes can be made to the team lineup for this event.</b></p>
                </ul>
            </div>

            <div class="text-center">
                <input type="submit" class="choose-payment-method" value="Confirm Team and Notify">
            </div>

            <div class="text-center">
                <button class="oceans-gaming-default-button"> Cancel</button>
            </div>

        </div>
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
            const dropdownButton = element.closest('.dropdown').querySelector('.dropbtn');
            dropdownButton.classList.add('selected');

            // Handle selection logic here
            const selectedLabel = dropdownButton.querySelector('.selected-label');
            const selectedImage = dropdownButton.querySelector('.selected-image img');
            selectedLabel.textContent = label;
            selectedImage.src = imageUrl;

            // Close the dropdown
            closeDropDown(dropdownButton);
        }

        // Function to close the dropdown
        function closeDropDown(button) {
            const dropdownContent = button.nextElementSibling;
            dropdownContent.classList.remove('d-block');
        }

        // Function to open the dropdown
        function openDropDown(button) {
            const dropdownContent = button.nextElementSibling;
            dropdownContent.classList.add('d-block');
        }
    </script>
    @include('CommonLayout.BootstrapJs')

</body>

</html>
