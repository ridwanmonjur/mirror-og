throttle = (func, wait) => {
    let lastTime = 0;

    return (...args) => {
        const now = Date.now();

        if (now - lastTime >= wait) {
            func(...args);

            lastTime = now;
        }
    };
};
let debounce = (func, wait) => {
    let timeout;

    return (...args) => {
        if (timeout) clearTimeout(timeout);

        timeout = setTimeout(() => func(...args), wait);
    };
};
/*------------------------------------------
--------------------------------------------
call infinteLoadMore()
--------------------------------------------
--------------------------------------------*/
function infinteLoadMore(page, ENDPOINT) {
    if ($('.no-more-data').hasClass('d-none')) {
        let endpointFinal = page == null ? ENDPOINT : ENDPOINT + "?page=" + page
        $.ajax({
            url: endpointFinal,
            datatype: "html",
            type: "get",
            beforeSend: function () {
            }
        })
            .done(function (response) {
                if (response.html == '') {
                    var noMoreDataElement = document.querySelector('.no-more-data');
                    noMoreDataElement.classList.remove('d-none');
                    noMoreDataElement.style.display = 'flex';
                    noMoreDataElement.style.justifyContent = 'center';
                    noMoreDataElement.textContent = "We don't have more data to display";
                }

                // <!-- $('.auto-load').hide(); -->
                $(".scrolling-pagination").append(response.html);
            })
            .fail(function (jqXHR, ajaxOptions, thrownError) {
                console.log('Server error occured');
            });
    } else {
        return;
    }
}


function infinteLoadMoreByPost(ENDPOINT, body) {
    let noMoreDataElement = document.querySelector('.no-more-data');
    let scrollingPaginationElement = document.querySelector('.scrolling-pagination');
    let hasClass = noMoreDataElement.classList.contains('d-none');
    if (hasClass) {
        // window.history.replaceState({}, document.title, endpointFinal);
        fetch(ENDPOINT, {
            method: 'post',
            headers: {
                'Accept': 'text/html',
                "Content-Type": "application/json",
            },
            body: JSON.stringify(body)
        })
            .then((response) => response.json())
            .then((response) => {
                if (response.html == '') {
                    noMoreDataElement.classList.remove('d-none');
                    noMoreDataElement.style.display = 'flex';
                    noMoreDataElement.style.justifyContent = 'center';
                    noMoreDataElement.textContent = "We don't have more data to display";
                } else {
                    scrollingPaginationElement.innerHTML += response.html;
                }
            })
            .catch(function (error) {
                console.log('Server error occured');
            });
    } else {
        return;
    }
}


function loadByPost(ENDPOINT, body) {
    let noMoreDataElement = document.querySelector('.no-more-data');
    let scrollingPaginationElement = document.querySelector('.scrolling-pagination');
    let hasClass = noMoreDataElement.classList.contains('d-none');
    
    if (hasClass) { }
    
    fetch(ENDPOINT, {
        method: 'post',
        headers: {
            'Accept': 'text/html',
            "Content-Type": "application/json",
        },
        body: JSON.stringify(body)
    })
        .then((response) => response.json())
        .then((response) => {
           if (response.html == '') {
                scrollingPaginationElement.innerHTML = "";
                noMoreDataElement.classList.remove('d-none');
                noMoreDataElement.style.display = 'flex';
                noMoreDataElement.style.justifyContent = 'center';
                noMoreDataElement.textContent = "Data not found by your query...";
            } else {
                scrollingPaginationElement.innerHTML = "";
                scrollingPaginationElement.innerHTML = response.html;
            }
        })
        .catch(function (error) {
            scrollingPaginationElement.innerHTML = "Work in Progress!";
        });
}
