
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
const COMPLETE_REQUEST = 4;
const COMPLETE_STATUS = 200

function infinteLoadMore(page, ENDPOINT) {
    let noMoreDataElement = document.querySelector('.no-more-data');
    let scrollingPaginationElement = document.querySelector('.scrolling-pagination');
    let hasClass = noMoreDataElement.classList.contains('d-none');
  
    if (hasClass) {
        let endpointFinal = page == null ? ENDPOINT : ENDPOINT + "?page=" + page
        
        let xhr = new XMLHttpRequest();
        
        xhr.open('GET', endpointFinal, true);

        xhr.onreadystatechange = function () {
            
            if (xhr.readyState == COMPLETE_REQUEST) { 
                if (xhr.status == COMPLETE_STATUS) { 
                    console.log({response: xhr.responseText})
                    var response = JSON.parse(xhr.responseText);

                    if (response.html === '') {
                        var noMoreDataElement = document.querySelector('.no-more-data');
                        noMoreDataElement.classList.remove('d-none');
                        noMoreDataElement.style.display = 'flex';
                        noMoreDataElement.style.justifyContent = 'center';
                        noMoreDataElement.textContent = "We don't have more data to display";
                    }

                    // Assuming that the response is JSON and contains an 'html' property
                    document.querySelector(".scrolling-pagination").insertAdjacentHTML('beforeend', response.html);
                } else {
                    console.log('Server error occurred');
                }
            } else {
                console.log('Server error occured');
            }
        };

        xhr.send();
    } else {
        return;
    }
}


function infinteLoadMoreByPost(ENDPOINT, body) {
    let noMoreDataElement = document.querySelector('.no-more-data');
    let scrollingPaginationElement = document.querySelector('.scrolling-pagination');
    let hasClass = noMoreDataElement.classList.contains('d-none');
    if (hasClass) {
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
