var ENDPOINT_OG = document.getElementById('endpoint_route').value;
var ENDPOINT = ENDPOINT_OG;
var page = 1;
let fetchedPage = 1;
var search = null;

window.addEventListener(
    "scroll",
    debounce(() => {

        page++;
        ENDPOINT = ENDPOINT_OG;

        if (!search || String(search).trim() == "") {
            search = null;
            ENDPOINT += "?page=" + page;
        } else {
            ENDPOINT += "?search=" + search + "&page=" + page;
        }

        infinteLoadMore(null, ENDPOINT
            // , ()=> {
            // window.motion.animateCard('event', [
            //     'cover', 'frame1', 'league_name', 'fs-7'
            // ]);
            // }
        );
        

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

// window.onload = () => {

    // window.motion.animateCard('event', [
    //     'cover', 'frame1', 'league_name', 'fs-7'
    // ]);
// }
