var ENDPOINT_OG = document.getElementById('endpoint_route').value;
var ENDPOINT = ENDPOINT_OG;
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
            ENDPOINT = ENDPOINT_OG;

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