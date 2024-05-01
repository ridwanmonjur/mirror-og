
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
        fetch(endpointFinal, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                ...window.loadBearerHeader()
              },
          })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json(); 
            })
            .then(response => {
                if (response.html === '') {
                    var noMoreDataElement = document.querySelector('.no-more-data');
                    noMoreDataElement.classList.remove('d-none');
                    noMoreDataElement.style.display = 'flex';
                    noMoreDataElement.style.justifyContent = 'center';
                    noMoreDataElement.textContent = "We don't have more data to display";
                }
        
                scrollingPaginationElement.insertAdjacentHTML('beforeend', response.html);
            })
            .catch(error => {
                console.error('There was a problem with the fetch operation:', error);
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
        fetch(ENDPOINT, {
            method: 'post',
            headers: {
                'Accept': 'text/html',
                "Content-Type": "application/json",
                ...window.loadBearerHeader()
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
                    scrollingPaginationElement.innerHTML += response.html ;
                }
            })
            .catch(function (error) {

                console.log('Server error occured');
                throw new Error('Error occurred');
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
            ...window.loadBearerHeader()
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
                scrollingPaginationElement.innerHTML = response.html;
            }
        })
        .catch(function (error) {
            scrollingPaginationElement.innerHTML = "Work in Progress!";
        });
}

function removeData() {
    let scrollingPaginationElement = document.querySelector('.scrolling-pagination');
    scrollingPaginationElement.innerHTML = "";
}


function resetNoMoreElement() {
    let noMoreDataElement = document.querySelector('.no-more-data');
    noMoreDataElement.innerHTML = "";
    noMoreDataElement.classList.add("d-none");
}