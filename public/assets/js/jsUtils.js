function toggleNavbar() {
    const x = document.querySelector("nav.mobile-navbar");
    x.classList.toggle("d-none");
}


function clearPlaceholder(element) {
    element.removeAttribute("placeholder");
}

function restorePlaceholder() {
    var teamNameInput = document.getElementById("teamName");
    if (!teamNameInput.value.trim()) {
        teamNameInput.setAttribute("placeholder", "Team Name");
    }
}

var throttle = (func, wait) => {
    let lastTime = 0;

    return (...args) => {
        const now = Date.now();

        if (now - lastTime >= wait) {
            func(...args);

            lastTime = now;
        }
    };
};

var debounce = (func, wait) => {
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


function removeData() {
    let scrollingPaginationElement = document.querySelector('.scrolling-pagination');
    scrollingPaginationElement.innerHTML = "";
}


function resetNoMoreElement() {
    let noMoreDataElement = document.querySelector('.no-more-data');
    noMoreDataElement.innerHTML = "";
    noMoreDataElement.classList.add("d-none");
}

function addOnLoad(newFunction) {
    const oldOnLoad = window.onload;
    if (typeof window.onload !== 'function') {
        window.onload = newFunction;
    } else {
        window.onload = () => {
            if (oldOnLoad) {
                oldOnLoad();
            }
            newFunction();
        };
    }
}


async function fetchData(url, callback, errorCallback, options = {}) {
    const defaultOptions = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            
        },
    };
    
    const mergedOptions = { ...defaultOptions, ...options };

    try {
        const response = await fetch(url, mergedOptions);
        const responseData = await response.json();
        callback(responseData);
    } catch (error) {
        if (errorCallback) {
            errorCallback(error);
        } else {
            console.error('Error fetching data:', error);
        }
    }
}

async function storeFetchDataInLocalStorage(url) {
    try {
        let isValid = false;
        let data = JSON.parse(localStorage.getItem('countriesData'));
        let innerData = data?.data;
        if (innerData) {
            isValid = innerData[0] && innerData[1] && innerData[99] && innerData[100];
        } 

        if (isValid) {
            return data;
        }

        const response = await fetch(url);
        data = await response.json();
        localStorage.setItem('countriesData', JSON.stringify(data));
        return data;
    } catch (error) {
        console.error('Error storing data in localStorage:', error);
    }
}

function openTab(evt, activeName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
      tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
      tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(activeName).style.display = "block";
    evt.currentTarget.className += " active";
  }