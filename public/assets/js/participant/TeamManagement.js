function reddirectToLoginWithIntened(route) {
    route = encodeURIComponent(route);
    let url = document.getElementById('signin_url')?.value;
    url+= `?url=${route}`;
    window.location.href = url;
}

function showTab(event, tabName, extraClassNameToFilter = "outer-tab") {
    const tabContents = document.querySelectorAll(`.tab-content.${extraClassNameToFilter}`);
    tabContents.forEach(content => {
        content.classList.add("d-none");
    });

    const selectedTab = document.getElementById(tabName);
    if (selectedTab) {
        selectedTab.classList.remove('d-none');
        selectedTab.classList.add('tab-button-active');
    }
    const tabButtons = document.querySelectorAll(`.tab-button-active.${extraClassNameToFilter}`);
    tabButtons.forEach(button => {
        button.classList.remove("tab-button-active");
    });

    let target = event.currentTarget;
    target.classList.add('tab-button-active');
}

carouselWork();
window.addEventListener('resize', debounce((e) => {
    carouselWork();
}, 250));

const searchInputs = document.querySelectorAll('.search_box input');
const memberTables = document.querySelectorAll('.member-table');

searchInputs.forEach((searchInput, index) => {
    searchInput.addEventListener("input", function() {
        const searchTerm = searchInput.value.toLowerCase();
        const memberRows = memberTables[index].querySelectorAll('tbody tr');

        memberRows.forEach(row => {
            const playerName = row.querySelector('.player-info span')
                .textContent.toLowerCase();

            if (playerName.includes(searchTerm)) {
                row.style.display = 'table-row';
            } else {
                row.style.display = 'none';
            }
        });
    });
});

window.onbeforeunload = function(){window.location.reload();}

function redirectToProfilePage(userId) {
    let route = document.getElementById('profile_route').value;
    route = route.replace(':id', userId);
    window.location.href = route;
}
