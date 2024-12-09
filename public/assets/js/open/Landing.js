var ENDPOINT_OG = document.getElementById('endpoint_route').value;
var ENDPOINT = ENDPOINT_OG;
var page = 1;
let fetchedPage = 1;
var search = null;

window.addEventListener(
    "scroll",
    (e) => {
        var windowHeight = window.innerHeight;
        var documentHeight = document.documentElement.scrollHeight;
        var scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        if (scrollTop + windowHeight >= documentHeight - 200) {
            page++;
            ENDPOINT = ENDPOINT_OG;

            if (!search || String(search).trim() == "") {
                search = null;
                ENDPOINT += "?page=" + page;
            } else {
                ENDPOINT += "?search=" + search + "&page=" + page;
            }

            infinteLoadMore(null, ENDPOINT);
            document.head.insertAdjacentHTML('beforeend', 
                '<style>.event { opacity: 1 !important }</style>'
            );
        }
    });


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

window.onload = () => {
    console.log("hi");
    console.log("hi");
    console.log("hi");
    window.motion.animateCard();
}
