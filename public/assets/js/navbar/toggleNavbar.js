function toggleNavbar() {
    const x = document.querySelector("nav.mobile-navbar");
    console.log({ x })
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
    let endpointFinal = page == null ? ENDPOINT : ENDPOINT + "?page=" + page
        // window.history.replaceState({}, document.title, endpointFinal);
    fetch(endpointFinal, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ searchText: searchText })
        })
        .then(response => {
            console.log(data);
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
    // Replace this with your actual logic to create the event element
    // Example: Create a div with event information
    const eventDiv = document.querySelector('.event');
    eventDiv.innerHTML = `
        <b>${event.eventName}</b><br>
        <small>${event.venue || 'South East Asia'}</small>
    `;
    return eventDiv;
}


function appendEventElement(event) {
    // Replace this with your actual logic to create the event element
    // Example: Create a div with event information
    const eventDiv = document.querySelector('.event');
    eventDiv.innerHTML += `
        <b>${event.eventName}</b><br>
        <small>${event.venue || 'South East Asia'}</small>
    `;
    return eventDiv;
}

function clearPlaceholder() {
    document.getElementById("teamName").removeAttribute("placeholder");
}

function restorePlaceholder() {
    var teamNameInput = document.getElementById("teamName");
    if (!teamNameInput.value.trim()) {
        teamNameInput.setAttribute("placeholder", "Team Name");
    }
}
