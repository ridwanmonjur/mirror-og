function goToCancelButton() {
    let url = document.getElementById('eventUrl').value;
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