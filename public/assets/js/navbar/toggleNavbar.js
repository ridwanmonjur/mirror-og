function toggleNavbar() {
    const x = document.querySelector("nav.mobile-navbar");
    x.classList.toggle("d-none");
}

function searchNavbar(event) {
    const searchText = event.target.value;

    const currentUrl = window.location.href;
    let endpoint;
    if (currentUrl.includes("/organizer/events")) {
        endpoint = "/organizer/events";
    } else {
        endpoint = "/participant/events";
    }
    let endpointFinal = page ? ENDPOINT : ENDPOINT + "?page=" + page;
    
    fetch(endpointFinal, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ searchText: searchText })
        })
        .then(response => {
            data.forEach(event => {
                createEventElement(event);
            });

            const currentUrl = new URL(window.location.href);

            const queryParams = currentUrl.searchParams;
            
            if (!queryParams.has('page')) {
                const eventDiv = document.querySelector('.event');
                eventDiv.innerHTML = ""
            }

            createEventElement(event);
        })
        .then(data => {
            console.log(data);
        })
}

function createEventElement(event) {
    const eventDiv = document.querySelector('.event');
    
    eventDiv.innerHTML = `
        <b>${event.eventName}</b><br>
        <small>${event.venue || 'South East Asia'}</small>
    `;
    return eventDiv;
}


function appendEventElement(event) {
    const eventDiv = document.querySelector('.event');

    eventDiv.innerHTML += `
        <b>${event.eventName}</b><br>
        <small>${event.venue || 'South East Asia'}</small>
    `;

    return eventDiv;
}

function clearPlaceholder(element) {
    element.removeAttribute("placeholder");
}

function restorePlaceholder() {
    var teamNameInput = document.getElementById("teamName");
    if (!teamNameInput.value.trim()) {
        teamNameInput.setAttribute("placeholder", "Team Name");
    }
}
