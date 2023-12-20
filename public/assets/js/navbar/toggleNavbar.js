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
    .then(response => response.json())
    .then(data => {
        console.log(data);
    })
}