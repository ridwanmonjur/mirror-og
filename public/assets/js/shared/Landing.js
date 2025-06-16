var ENDPOINT_URL_OG = document.getElementById('endpoint_route').value;
var ENDPOINT_URL = ENDPOINT_URL_OG;
var page = 1;
let fetchedPage = 1;
var search = null;

window.addEventListener(
    "scroll",
    debounce(() => {

        page++;
        ENDPOINT_URL = ENDPOINT_URL_OG;
        search = document.getElementById('search-bar')?.value;
        if (!search || String(search).trim() == "") {
            search = document.getElementById('search-bar-mobile')?.value;
        } 

        if (!search || String(search).trim() == "") {
            search = null;
            ENDPOINT_URL += "?page=" + page;
        } else {
            ENDPOINT_URL += "?search=" + search + "&page=" + page;
        }

        infinteLoadMore(null, ENDPOINT_URL, ()=> {
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => {
                // Remove existing tooltip if it exists
                const existingTooltip = window.bootstrap.Tooltip.getInstance(tooltipTriggerEl);
                if (existingTooltip) {
                    existingTooltip.dispose();
                }
                // Create new tooltip
                return new window.bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    }, 100));


function myFunction() {
    document.getElementById("myDropdown").classList.toggle("show");
}

window.onclick = function (event) {

    if (!event.target.matches('.dropbtn')) {
        var dropdowns = document.getElementsByClassName("dropdown-content");
        var i;

        for (i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i]; if (openDropdown.classList.contains('show')) {
                openDropdown.classList.remove('show');
            }
        }
    }
}

