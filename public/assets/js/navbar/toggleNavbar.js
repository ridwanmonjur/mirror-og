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

    fetch(endpoint, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ searchText: searchText })
    })
    .then(response => {
        console.log(data);

        // Assuming you want to update the document with the fetched data
        // Replace this with your actual logic to update the DOM
        document.innerHTML = ''; // Clear existing content

        data.forEach(event => {
            const eventElement = createEventElement(event);
            document.appendChild(eventElement);
        });
    })
    .then(data => {
        console.log(data);
    })
}

function createEventElement(event) {
    // Replace this with your actual logic to create the event element
    // Example: Create a div with event information
    const eventDiv = document.createElement('div');
    eventDiv.innerHTML = `
        <b>${event.eventName}</b><br>
        <small>${event.region || 'South East Asia'}</small>
    `;
    return eventDiv;
}