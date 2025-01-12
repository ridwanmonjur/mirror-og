function toggleNavbar() {
    const x = document.querySelector("nav.mobile-navbar");
    x.classList.toggle("d-none");
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

function infinteLoadMore(page, ENDPOINT, cbIfDataPresent=null) {
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
                'credentials': 'include'
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
                    return;
                }
        
                scrollingPaginationElement.insertAdjacentHTML('beforeend', response.html);
                cbIfDataPresent();
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

let bodyHeight3 = null;


function openTab(evt, activeName, specialElementHeightId = null) {
    var i, tabcontent, tablinks;
    
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.opacity = "0";
        tabcontent[i].style.display = "none";

    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    if (typeof bodyHeight3 === 'undefined' || bodyHeight3 === null) {
        bodyHeight3 = document.body.offsetHeight;
    }
    
    let activeElement = document.getElementById(activeName);
    activeElement.style.display = "block";
    evt.currentTarget.className += " active";
    
    activeElement.style.transition = "opacity 0.5s ease-in-out";
    setTimeout(() => {
        activeElement.style.opacity = "1";
    }, 10);
    
    if (specialElementHeightId) {
        let bracketList = document.getElementById(specialElementHeightId);
        let bracketListHeight = bracketList.getBoundingClientRect().height;
        let main = document.querySelector('main');
        if (main) {
            main.style.transition = "height 0.5s ease-in-out";
            main.style.height = bodyHeight3 + bracketListHeight + 'px';
        }
    }
}

function showTab(event, tabName, extraClassNameToFilter = "outer-tab") {

    const tabContents = document.querySelectorAll(`.tab-content.${extraClassNameToFilter}`);
    tabContents.forEach(content => {
        content.classList.add("d-none");
        content.style.opacity = "0";
    });

    const selectedTab = document.getElementById(tabName);
    if (selectedTab) {
        selectedTab.classList.remove('d-none');
        
        setTimeout(() => {
            selectedTab.style.transition = "opacity 0.5s ease-in-out";
            selectedTab.style.opacity = "1";
        }, 10);
    }

    const tabButtons = document.querySelectorAll(`.tab-button-active.${extraClassNameToFilter}`);
    tabButtons.forEach(button => {
        button.classList.remove("tab-button-active");
    });

    let target = event.currentTarget;
    target.classList.add('tab-button-active');
}

function setAllNotificationsRead(event) {
    event.preventDefault();
    event.stopPropagation();
    var notificationId = event.target.dataset.notificationId;
    console.log(notificationId);
    var divElements = document.querySelectorAll('div.notification-container');
    // route('user.notifications.readAll') 
    let url = "/user/notifications/read";
    console.log({url})
     fetch(url, {
        method: 'put',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            credentials: 'include',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
    })
        .then((response) => response.json())
        .then((response) => {
            if (response.success) {
                divElements.forEach(function(div) {
                    div.classList.remove('notification-container-not-read');
                });
            }
        })
        .catch(function (error) {
            console.log('Server error occured');
            throw new Error('Error occurred');
        });
}

function setNotificationReadById(event, notificationId, loopCount) {
    event.preventDefault();
    event.stopPropagation();

    var divElement = document.querySelector('div.notification-container[data-loop-count="' + loopCount + '"]');
    var element = divElement.querySelector('a.mark-read');
    console.log({element, notificationId, loopCount});
    // route('user.notifications.read', ['id' => ':id'])
    let url = `/user/notifications/${notificationId}/read`;
     fetch(url, {
        method: 'put',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-type': 'application/json',            
        },
    })
        .then((response) => response.json())
        .then((response) => {
            if (response.success) {
                const notificationCountElement = document.getElementById('countUnread');
                const notificationCount = notificationCountElement.dataset.notificationCount;
                const decreasedCount = parseInt(notificationCount, 0) - 1;
                if (decreasedCount >= 0) {
                    notificationCountElement.dataset.notificationCount = decreasedCount;
                    notificationCountElement.textContent = decreasedCount;
                }
                divElement.classList.remove('notification-container-not-read');
                
                element.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-all" viewBox="0 0 16 16">
                    <path d="M8.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L2.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093L8.95 4.992zm-.92 5.14.92.92a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 1 0-1.091-1.028L9.477 9.417l-.485-.486z"/>
                    </svg>
                    <span> Read </span>
                `;
            }
        })
        .catch(function (error) {

            console.error(error);
            throw new Error('Error occurred');
        });
}

document.getElementById('load-more')?.addEventListener('click', function(event) {
    let baseUrl = event.target.getAttribute('data-url');
    let cursor = event.target.getAttribute('data-cursor');
    event.preventDefault();
    event.stopPropagation();
    let url = `${baseUrl}?cursor=${cursor}`;

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-type': 'application/json',  
        }
    })
        .then(response => response.json())
        .then(data => {
            document.querySelector('.notifications-list-container').innerHTML+= data?.html ?? '';
            applyRandomColorsAndShapes();
            let nextPageUrl = data.nextPageUrl;
            if (nextPageUrl) {
                this.setAttribute('data-cursor', nextPageUrl);
            } else {
                this.style.display = 'none';
            }
        });
});

function getUrl(inputId, id = null) {
    let url = document.getElementById(inputId).value;
    if (url === null) {
        console.error("Null input: ", url);
    }
    if (id !== null) {
        url = url.replace(':id', id);
    }
    return url;
}


let navbar = document.querySelector('.navbar');
let lastScrollTop = 0;

// navbar.addEventListener('mouseenter', () => {
//     navbar.classList.remove('navbar-scrolled');
// });

// window.addEventListener('scroll', throttle(function() {
//     let currentScroll = window.scrollY;
    
//     if (!navbar.matches(':hover')) {
//         if (currentScroll > lastScrollTop && currentScroll > 100) {
//             navbar.classList.add('navbar-scrolled');
//         } 
//         else if (currentScroll < lastScrollTop) {
//             navbar.classList.remove('navbar-scrolled');
//         }
//     }
    
//     lastScrollTop = currentScroll;
// }, 300));