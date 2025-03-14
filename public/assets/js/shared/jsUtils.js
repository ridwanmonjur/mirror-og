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
    
    if (specialElementHeightId) {
        console.log({specialElementHeightId});
        let bracketList = document.getElementById(specialElementHeightId);
        console.log({bracketList});
        console.log({bracketList});
        console.log({bracketList});
        console.log({bracketList});
        console.log({bracketList});
        console.log({bracketList});
        if (!bracketList) return;
        let bracketListHeight = bracketList.clientHeight;
        console.log({bracketListHeight});
        console.log({bracketListHeight});
        console.log({bracketListHeight});
        console.log({bracketListHeight});
        console.log({bracketListHeight});
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
    });

    const selectedTab = document.getElementById(tabName);
    if (selectedTab) {
        selectedTab.classList.remove('d-none');
        
    }

    const tabButtons = document.querySelectorAll(`.tab-button-active.${extraClassNameToFilter}`);
    tabButtons.forEach(button => {
        button.classList.remove("tab-button-active");
    });

    let target = event.currentTarget;
    target.classList.add('tab-button-active');
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

var currentIndex = 0;
function carouselWork(increment = 0) {
    const eventBoxes = document.querySelectorAll('.event-carousel-works > div');
    let boxLength = eventBoxes.length;
    let numberOfBoxes = 1;
    if (window.matchMedia("(min-width: 1100px)").matches) {
        numberOfBoxes = 2;
    }

    let incrementSignMultipier = increment >= 0 ? 1 : -1;
    if (increment != 0) increment = numberOfBoxes * incrementSignMultipier;

    let newSum = currentIndex + increment;

    if (newSum >= boxLength || newSum < 0) {
        return;
    } else {
        currentIndex = newSum;
    }

    console.log({ currentIndex, boxLength, numberOfBoxes })


    // carousel top button working
    const button1 = document.querySelector('.carousel-button:nth-child(1)');
    const button2 = document.querySelector('.carousel-button:nth-child(2)');
    button1.classList.remove('carousel-button-disabled');
    button2.classList.remove('carousel-button-disabled');

    if (currentIndex <= 0) {
        button1.classList.add('carousel-button-disabled');
    }

    if (currentIndex + numberOfBoxes > boxLength - 1) {
        button2.classList.add('carousel-button-disabled');
    }

    // carousel swing
    for (let i = 0; i < currentIndex; i++) {
        eventBoxes[i]?.classList.add('d-none');
    }

    for (let i = currentIndex; i < currentIndex + numberOfBoxes; i++) {
        eventBoxes[i]?.classList.remove('d-none');
    }

    for (let i = currentIndex + numberOfBoxes; i < boxLength; i++) {
        eventBoxes[i]?.classList.add('d-none');
    }
    console.log("ended")
}

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

function goToUrl(event, element) {
    event.stopPropagation();
    event.preventDefault();
    const url = element.getAttribute('data-url');
    window.location.href = url;

}

async function onFollowSubmit(event) {
    event.preventDefault();
    event.stopPropagation();
    let form = event.currentTarget;
    let dataset = form.dataset;
    let followButtons = document.getElementsByClassName(
        'followButton' + dataset.joinEventUser
    );
    let followCounts = document.getElementsByClassName(
        'followCounts' + dataset.joinEventUser
    );


    let formData = new FormData(form);
    [...followButtons].forEach((button) => {
        button.style.setProperty('pointer-events', 'none');
    });

    try {
        let jsonObject = {}
        for (let [key, value] of formData.entries()) {
            jsonObject[key] = value;
        }

        let response = await fetch(form.action, {
            method: form.method,
            body: JSON.stringify(jsonObject),
            headers: {
                'credentials': 'include',
                'Accept': 'application/json',
                "Content-Type": "application/json",
            }
        });

        let data = await response.json();
        [...followButtons].forEach((followButton) => {
            followButton.style.setProperty('pointer-events', 'none');

            if (data.isFollowing) {
                followButton.innerText = 'Following';
                followButton.style.backgroundColor = '#8CCD39';
                followButton.style.color = '#2e4b59';
            } else {
                followButton.innerText = 'Follow';
                followButton.style.backgroundColor = '#43A4D7';
                followButton.style.color = 'white';
            }

            followButton.style.setProperty('pointer-events', 'auto');
        });

        let count = Number(followCounts[0].dataset.count);
        if (data.isFollowing) {
            count++;
        } else {
            count--;
        }

        [...followCounts].forEach((followCount) => {
            followCount.dataset.count = count;
            if (count == 1) {
                followCount.innerHTML = '1 follower';
            } else if (count == 0) {
                followCount.innerHTML = `0 followers`;
            } else {
                followCount.innerHTML = `${followCount.dataset.count} followers`;
            }
        })
    } catch (error) {
        [...followButtons].forEach(function (followButton) {
            followButton.style.setProperty('pointer-events', 'auto');
        });

       window.toastError("Following failed!")
    }
}



class DynamicSelect {

    constructor(element, options = {}) {
        let defaults = {
            placeholder: 'Select an option',
            columns: 1,
            name: '',
            width: '',
            height: '',
            data: [],
            onChange: function() {}
        };
        this.options = Object.assign(defaults, options);
        this.selectElement = typeof element === 'string' ? document.querySelector(element) : element;
        for(const prop in this.selectElement.dataset) {
            if (this.options[prop] !== undefined) {
                this.options[prop] = this.selectElement.dataset[prop];
            }
        }
        this.name = this.selectElement.getAttribute('name') ? this.selectElement.getAttribute('name') : 'dynamic-select-' + Math.floor(Math.random() * 1000000);
        if (!this.options.data.length) {
            let options = this.selectElement.querySelectorAll('option');
            for (let i = 0; i < options.length; i++) {
                this.options.data.push({
                    value: options[i].value,
                    text: options[i].innerHTML,
                    img: options[i].getAttribute('data-img'),
                    selected: options[i].selected,
                    html: options[i].getAttribute('data-html'),
                    imgWidth: options[i].getAttribute('data-img-width'),
                    imgHeight: options[i].getAttribute('data-img-height')
                });
            }
        }
        this.element = this._template();
        this.selectElement.replaceWith(this.element);
        this._updateSelected();
        this._eventHandlers();
    }

    _template() {
        let optionsHTML = '';
        for (let i = 0; i < this.data.length; i++) {
            let optionWidth = 100 / this.columns;
            let optionContent = '';
            if (this.data[i].html) {
                optionContent = this.data[i].html;
            } else {
                optionContent = `
                    ${this.data[i].img ? `<img  onerror="this.src='/assets/images/404q.png';"
                        src="${this.data[i].img}" alt="${this.data[i].text}" class=" border border-primary border-2   object-fit-cover ${this.data[i].imgWidth && this.data[i].imgHeight ? '' : ''}" style="${this.data[i].imgWidth ? 'width:' + this.data[i].imgWidth + ';' : ''}${this.data[i].imgHeight ? 'height:' + this.data[i].imgHeight + ';' : ''}">` : ''}
                    ${this.data[i].text ? '<span class="dynamic-select-option-text">' + this.data[i].text + '</span>' : ''}
                `;
            }
            optionsHTML += `
                <div class="dynamic-select-option${this.data[i].value == this.selectedValue ? ' dynamic-select-selected' : ''}${this.data[i].text || this.data[i].html ? '' : ' dynamic-select-no-text'}" data-value="${this.data[i].value}" style="width:${optionWidth}%;${this.height ? 'height:' + this.height + ';' : ''}">
                    ${optionContent}
                </div>
            `;
        }
        let template = `
            <div class="dynamic-select ${this.name}"${this.selectElement.id ? ' id="' + this.selectElement.id + '"' : ''} style="${this.width ? 'width:' + this.width + ';' : ''}${this.height ? 'height:' + this.height + ';' : ''}">
                <input type="hidden" name="${this.name}" value="${this.selectedValue}">
                <div class="dynamic-select-header" style="${this.width ? 'width:' + this.width + ';' : ''}${this.height ? 'height:' + this.height + ';' : ''}"><span class="dynamic-select-header-placeholder">${this.placeholder}</span></div>
                <div class="dynamic-select-options" style="${this.options.dropdownWidth ? 'width:' + this.options.dropdownWidth + ';' : ''}${this.options.dropdownHeight ? 'height:' + this.options.dropdownHeight + ';' : ''}">${optionsHTML}</div>
            </div>
        `;
        let element = document.createElement('div');
        element.innerHTML = template;
        return element;
    }

    _eventHandlers() {
        this.element.querySelectorAll('.dynamic-select-option').forEach(option => {
            option.onclick = () => {
                this.element.querySelectorAll('.dynamic-select-selected').forEach(selected => selected.classList.remove('dynamic-select-selected'));
                option.classList.add('dynamic-select-selected');
                this.element.querySelector('.dynamic-select-header').innerHTML = option.innerHTML;
                this.element.querySelector('input').value = option.getAttribute('data-value');
                this.data.forEach(data => data.selected = false);
                this.data.filter(data => data.value == option.getAttribute('data-value'))[0].selected = true;
                this.element.querySelector('.dynamic-select-header').classList.remove('dynamic-select-header-active');
                this.options.onChange(option.getAttribute('data-value'), option.querySelector('.dynamic-select-option-text') ? option.querySelector('.dynamic-select-option-text').innerHTML : '', option);
            };
        });
        this.element.querySelector('.dynamic-select-header').onclick = () => {
            this.element.querySelector('.dynamic-select-header').classList.toggle('dynamic-select-header-active');
        };  
        if (this.selectElement.id && document.querySelector('label[for="' + this.selectElement.id + '"]')) {
            document.querySelector('label[for="' + this.selectElement.id + '"]').onclick = () => {
                this.element.querySelector('.dynamic-select-header').classList.toggle('dynamic-select-header-active');
            };
        }
        document.addEventListener('click', event => {
            if (!event.target.closest('.' + this.name) && !event.target.closest('label[for="' + this.selectElement.id + '"]')) {
                this.element.querySelector('.dynamic-select-header').classList.remove('dynamic-select-header-active');
            }
        });
    }

    _updateSelected() {
        if (this.selectedValue) {
            this.element.querySelector('.dynamic-select-header').innerHTML = this.element.querySelector('.dynamic-select-selected').innerHTML;
        }
    }

    get selectedValue() {
        let selected = this.data.filter(option => option.selected);
        selected = selected.length ? selected[0].value : '';
        return selected;
    }

    set data(value) {
        this.options.data = value;
    }

    get data() {
        return this.options.data;
    }

    set selectElement(value) {
        this.options.selectElement = value;
    }

    get selectElement() {
        return this.options.selectElement;
    }

    set element(value) {
        this.options.element = value;
    }

    get element() {
        return this.options.element;
    }

    set placeholder(value) {
        this.options.placeholder = value;
    }

    get placeholder() {
        return this.options.placeholder;
    }

    set columns(value) {
        this.options.columns = value;
    }

    get columns() {
        return this.options.columns;
    }

    set name(value) {
        this.options.name = value;
    }

    get name() {
        return this.options.name;
    }

    set width(value) {
        this.options.width = value;
    }

    get width() {
        return this.options.width;
    }

    set height(value) {
        this.options.height = value;
    }

    get height() {
        return this.options.height;
    }

    _createOptionsHTML() {
        let optionsHTML = '';
        for (let i = 0; i < this.data.length; i++) {
            let optionWidth = 100 / this.columns;
            let optionContent = '';
            if (this.data[i].html) {
                optionContent = this.data[i].html;
            } else {
                optionContent = `
                    ${this.data[i].img ? `<img onerror="this.src='/assets/images/404q.png';"
                        src="${this.data[i].img}" alt="${this.data[i].text}" class=" border border-primary border-2   object-fit-cover ${this.data[i].imgWidth && this.data[i].imgHeight ? 'object-fit-cover' : ''}" style="${this.data[i].imgWidth ? 'width:' + this.data[i].imgWidth + ';' : ''}${this.data[i].imgHeight ? 'height:' + this.data[i].imgHeight + ';' : ''}">` : ''}
                    ${this.data[i].text ? '<span class="dynamic-select-option-text">' + this.data[i].text + '</span>' : ''}
                `;
            }
            optionsHTML += `
                <div class="dynamic-select-option${this.data[i].value == this.selectedValue ? ' dynamic-select-selected' : ''}${this.data[i].text || this.data[i].html ? '' : ' dynamic-select-no-text'}" data-value="${this.data[i].value}" style="width:${optionWidth}%;${this.height ? 'height:' + this.height + ';' : ''}">
                    ${optionContent}
                </div>
            `;
        }
        return optionsHTML;
    }

    updateSelectElement(dataValue) {
        const selectedData = this.data.find(item => item.value == dataValue);
        if (selectedData) {
            this.data.forEach(item => item.selected = (item.value == dataValue));

            let optionsHTML = this._createOptionsHTML();
            this.element.querySelector('.dynamic-select-options').innerHTML = optionsHTML;

            this.element.querySelector('input').value = dataValue;

            const headerContent = selectedData.html || `
                ${selectedData.img ? `<img onerror="this.src='/assets/images/404q.png';"
                    src="${selectedData.img}" alt="${selectedData.text}" class=" border border-primary border-2   object-fit-cover ${selectedData.imgWidth && selectedData.imgHeight ? 'object-fit-cover' : ''}" style="${selectedData.imgWidth ? 'width:' + selectedData.imgWidth + ';' : ''}${selectedData.imgHeight ? 'height:' + selectedData.imgHeight + ';' : ''}">` : ''}
                ${selectedData.text ? '<span class="dynamic-select-option-text">' + selectedData.text + '</span>' : ''}
            `;
            this.element.querySelector('.dynamic-select-header').innerHTML = headerContent;

            this._eventHandlers();

            this.options.onChange(dataValue, selectedData.text, this.element.querySelector(`.dynamic-select-option[data-value="${dataValue}"]`));
        } else {
            this.element.querySelector('input').value = '';
            this.element.querySelector('.dynamic-select-header').innerHTML = `<span class="dynamic-select-header-placeholder">${this.placeholder}</span>`;
        }
    }

}

/*

let selectMap = {};
document.querySelectorAll('[data-dynamic-select]').forEach(select => {
    selectMap[select.name] = new DynamicSelect(select);
});

*/
let ENDPOINT = null;
const importantUrlsDiv = document.getElementById('importantUrls');
let {
    searchEndpoint,
    landingEndpoint
} = importantUrlsDiv?.dataset ?? {};

console.log({
    searchEndpoint,
    landingEndpoint
})

if (searchEndpoint) {
    ENDPOINT = searchEndpoint;
    document.getElementById('search-bar')?.addEventListener(
        "keydown",
        debounce((e) => {
            if (e.keyCode === 91 || e.keyCode === 92) {
                e.preventDefault(); 
                return; 
            }
            

            goToSearchPage();
        }, 1000)
    );
    
    document.getElementById('search-bar-mobile')?.addEventListener(
        "keydown",
        debounce((e) => {
            if (e.keyCode === 91 || e.keyCode === 92) {
                e.preventDefault(); 
                return; 
            }
            
            goToSearchPage();
        }, 1000)
    );

} else {

    ENDPOINT = landingEndpoint;
    var page = 1;
    var search = null;

    document.getElementById('search-bar')?.addEventListener(
        "keydown",
        debounce((e) => {
            if (e.keyCode === 91 || e.keyCode === 92) {
                e.preventDefault(); 
                return; 
            }
            
            searchPart(e);
        }, 1000)
    );

    document.getElementById('search-bar-mobile')?.addEventListener(
        "keydown",
        debounce((e) => {
            if (e.keyCode === 91 || e.keyCode === 92) {
                e.preventDefault(); 
                return; 
            }
            
            searchPart(e);
        }, 1000)
    );
}

document.getElementById('search-bar')?.addEventListener("blur", () => {
    const element = document.querySelector('.scrolling-pagination');
    if (element) {
        element.innerHTML = '';
    }
});

function goToSearchPage() {
    ENDPOINT = searchEndpoint;
    let page = 1;
    let search = null;
    let searchBar = document.querySelector('input#search-bar');
    if (searchBar.style.display != 'none') {
        search = searchBar.value;
    } else {
        searchBar = document.querySelector('input#search-bar-mobile');
        search = searchBar.value;
    }
    if (!search || String(search).trim() == "") {
        search = null;
        ENDPOINT += "?page=" + page;
    } else {
        ENDPOINT += "?search=" + search + "&page=" + page;
    }
    window.location = ENDPOINT;
}

function searchPart(e) {
    page = 1;
    let noMoreDataElement = document.querySelector('.no-more-data');
    noMoreDataElement.classList.add('d-none');
    document.querySelector('.scrolling-pagination').innerHTML = '';
    search = e.target.value;
    ENDPOINT = landingEndpoint;
    console.log({ENDPOINT});
    console.log({ENDPOINT});
    console.log({ENDPOINT});
    if (!search || String(search).trim() == "") {
        search = null;
        ENDPOINT += "?page=" + page;
        infinteLoadMore(null, ENDPOINT);
    } else {
        ENDPOINT = landingEndpoint;
        ENDPOINT += "?search=" + e.target.value + "&page=" + page;
        window.location.href = ENDPOINT;
    }
}

