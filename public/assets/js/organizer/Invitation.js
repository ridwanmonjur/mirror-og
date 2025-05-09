const eventData = document.getElementById('eventData');

const goToManageScreen = () => {
    window.location.href = eventData.dataset.routeShow;
}

let csrfTokenNew = document.querySelector('meta[name="csrf-token"]').getAttribute('content');


function addParticant() {
    const teamId = document.querySelector('select').value;
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
            "X-CSRF-TOKEN": csrfTokenNew,
            "Accept": "application/json",
        },
        body: JSON.stringify(data)
    })
        .then(response => {
            return response.json()
        })
        .then(responseData => {
            if (!responseData.success) {
                Toast.fire({
                    icon: 'error',
                    text: responseData.message
                })

                return;
            } 

            const teamElement = document.createElement('div');
            let { team, invitation } = responseData.data;
            teamElement.innerHTML = `
            <div id="invite-${invitation.id}" class="d-flex justify-content-center  my-2 align-items-center">
                        <img src="/storage/${team.teamBanner}" 
                        class="team-banner border border-dark object-fit-cover rounded-circle "  
                        onerror="this.src='/assets/images/404q.png';"
                        width="30" height="30"
                    >
                    <p class="ms-1 me-1  d-inline my-0 py-0">${team.teamName}</p>
                    <span class="me-1">${team.country_flag}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                        data-invitation-id = "${invitation.id}"
                        onclick="removeParticant(event)"
                        class="text-red border border-danger cursor-pointer rounded-circle p-0 ms-2"
                    >
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
            </div>
            `;
            addedTeam.appendChild(teamElement);

            Toast.fire({
                icon: 'success',
                text: responseData.message
            })
        })
        .catch(error => {
            console.error(error);
        })
}

function removeParticant(event) {
    let crossItem = event.target;
    let inviteId = crossItem.dataset.invitationId;
    
    fetch(eventData.dataset.routeInvitationDestroy, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfTokenNew,
            "Accept": "application/json",
        },
        body: JSON.stringify({
            inviteId: inviteId
        })

    })
        .then(response => {
            return response.json()
        })
        .then(responseData => {
            if (!responseData.success) {
                Toast.fire({
                    icon: 'error',
                    text: responseData.message
                })

                return;
            } 

            let div = document.getElementById(`invite-${inviteId}`);
            div.remove();
            Toast.fire({
                icon: 'success',
                text: "Successfully removed the invitation"
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