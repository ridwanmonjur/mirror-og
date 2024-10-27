const eventData = document.getElementById('eventData');
let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

const goToManageScreen = () => {
    window.location.href = eventData.dataset.routeShow;
}

function addParticant() {
    const teamId = document.querySelector('select').value;
    const teamName = document.querySelector('select').value;
    const addedTeam = document.querySelector('.added-participant');
    const hideIfTeam = document.querySelector('.hide-if-participant');
    if (hideIfTeam) {
        hideIfTeam.classList.add('d-none');
    }

    let data = {
        event_id: eventData.dataset.eventId,
        team_id: teamId,
        organizer_id: eventData.dataset.userId,
    };

    fetch(eventData.dataset.routeInvitationStore, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
            "Accept": "application/json",
        },
        body: JSON.stringify(data)
    })
        .then(response => {
            return response.json()
        })
        .then(responseData => {
            const teamElement = document.createElement('p');
            teamElement.textContent = responseData?.data.team.teamName;
            addedTeam.appendChild(teamElement);

            Toast.fire({
                icon: 'success',
                text: "Successfully added user."
            })
        })
        .catch(error => {
            console.error(error);
        })
}

function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
}