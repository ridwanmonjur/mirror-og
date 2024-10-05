var ENDPOINT = document.getElementById('endpoint_route').value;
var page = 1;
var search = null;

window.addEventListener(
    "scroll",
    throttle((e) => {

        var windowHeight = window.innerHeight;
        var documentHeight = document.documentElement.scrollHeight;
        var scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        if (scrollTop + windowHeight >= documentHeight - 200) {
            page++;
            ENDPOINT = document.getElementById('endpoint_route').value;

            if (!search || String(search).trim() == "") {
                search = null;
                ENDPOINT += "?page=" + page;
            } else {
                ENDPOINT += "?search=" + search + "&page=" + page;
            }

            infinteLoadMore(null, ENDPOINT);
        }
    }, 300)
);

let token = document.getElementById('session_token').value;
if (token) {
    console.log({token});
    localStorage.setItem('token', token);
}