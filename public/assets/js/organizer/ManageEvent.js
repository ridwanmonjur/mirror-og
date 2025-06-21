let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function cancelEvent(event) {
    let svgElement = event.target.closest('svg');
    if (!svgElement) return;
    let eventUrl = svgElement.dataset.url;

    Swal.fire({
        title: "Are you sure you want to cancel this event?",
        text: "You won't be able to un-cancel your canceling if you go forward with this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#43A4D7",
        cancelButtonColor: "#d33",
        cancelButtonText: "Cancel this event!",
        confirmButtonText: "Oops, no..."
    })
        .then((result) => {
            if (result.isDismissed) {
                fetch(eventUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Cancelled!',
                                text: 'Event has been cancelled.',
                                icon: 'success',
                                confirmButtonColor: "#43A4D7",
                            });
                            location.reload();
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Failed to cancel the event.',
                                icon: 'error',
                                confirmButtonColor: "#43A4D7",
                            });
                        }
                    })
                    .catch((error) => {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Something went wrong!',
                            icon: 'error',
                            confirmButtonColor: "#43A4D7",
                        });
                    });
            }
        });
}
// ManageEventFetchProcessor
class FetchVariables {
    constructor(formId = null, formKeys = []) {
        this.sortType = 'asc';
        this.sortKey = '';
        this.filter = {};
        this.search = null;
        this.fetchedPage = 1;
        this.currentPage = 1;
        this.formId = formId;
        this.formKeys = formKeys;
    }

    visualize() {
        console.log({
            filter: this.getFilter(),
            search: this.search,
            sortKey: this.sortKey,
            sortType: this.sortType,
            fetchedPage: this.fetchedPage                
        });
    }

    getSortType() {
        return this.sortType;
    }

    getSortKey() {
        return this.sortKey;
    }

    getFilter() {
        let form = document.getElementById(this.formId);
        console.log({form})
        let formData = new FormData(form);
        console.log({formData, keys: [...formData.keys()]})

        let filterValues = {};
        for (let key of this.formKeys) {
            filterValues[key] = formData.getAll(key);
        }

        return filterValues;
    }

    getFetchedPage() {
        return this.fetchedPage;
    }

    getCurrentPage() {
        return this.currentPage;
    }

    getSearch() {
        return this.search;
    }

    setSortKey(value) {
        this.sortKey = value;
    }

    setFilter(value) {
        this.filter = value;
    }

    setFilterDate(date1, date2) {
    }

    setSearch(value) {
        this.search = value;
    }

    setSortType(value) {
        this.sortType = value;
    }

    setFetchedPage(value) {
        this.fetchedPage = value;
    }

    setCurrentPage(value) {
        this.currentPage = value;
    }
}

const appData = document.getElementById('app-data');
const ENDPOINT_URL_OG = appData.dataset.endpoint;
const userId = Number(appData.dataset.userId);
const eventIndexUrl = appData.dataset.eventIndexUrl;
const eventCreateUrl = appData.dataset.eventCreateUrl;

addOnLoad(()=> {
    addTippy();
    // window.motion.animateCard('event-box', [
    //     'card-text'
    // ]);
});

function addTippy() {
    const parentElements = document.querySelectorAll(".popover-parent");
    parentElements.forEach(parent => {
        const contentElement = parent.querySelector(".popover-content2");
        const parentElement = parent.querySelector(".popover-button");
        if (contentElement) {
            window.addPopover(parentElement, contentElement, 'click' , {
                hideOnClick: true
            });
        }
    });
}


function infinteLoadMoreByPost(ENDPOINT_URL, body, successCb = null) {
    let noMoreDataElement = document.querySelector('.no-more-data');
    let scrollingPaginationElement = document.querySelector('.scrolling-pagination');
    let hasClass = noMoreDataElement.classList.contains('d-none');
    
    if (hasClass) {
        fetch(ENDPOINT_URL, {
            method: 'post',
            headers: {
                'Accept': 'text/html',
                "Content-Type": "application/json",
                'credentials': 'include',
            },
            body: JSON.stringify(body)
        })
            .then((response) => response.json())
            .then((response) => {
                
                if (!response.html || response.html === '') {
                    noMoreDataElement.classList.remove('d-none');
                    noMoreDataElement.style.display = 'flex';
                    noMoreDataElement.style.justifyContent = 'center';
                    noMoreDataElement.textContent = "We don't have more data to display";
                } else {
                    scrollingPaginationElement.innerHTML += response.html ;
                    if (successCb) {
                        successCb();
                    }
                }

                addTippy();
            })
            .catch(function (error) {

                console.log('Server error occured');
                throw new Error('Error occurred');
            });
    } else {
        return;
    }
}


function loadByPost(ENDPOINT_URL, body, successCb = null) {
    let noMoreDataElement = document.querySelector('.no-more-data');
    let scrollingPaginationElement = document.querySelector('.scrolling-pagination');
    let hasClass = noMoreDataElement.classList.contains('d-none');
    
    if (hasClass) { }
    
    fetch(ENDPOINT_URL, {
        method: 'post',
        headers: {
            'Accept': 'text/html',
            "Content-Type": "application/json",
            'credentials': 'include',
        },
        body: JSON.stringify(body)
    })
        .then((response) => response.json())
        .then((response) => {
            if (!response.html || response.html === '') {
                scrollingPaginationElement.innerHTML = "";
                noMoreDataElement.classList.remove('d-none');
                noMoreDataElement.style.display = 'flex';
                noMoreDataElement.style.justifyContent = 'center';
                noMoreDataElement.innerHTML = `
                    <div class="mx-auto text-center">

                        <div>
                        <svg class="text-muted" width="48" height="48" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                      </svg>
                      <p>Data not found by your query...</p>

                        </div>
                    </div>
                `;
            } else {
                scrollingPaginationElement.innerHTML = response.html;
                if (successCb) {
                    successCb();
                }
            }

            addTippy();
        })
        .catch(function (error) {
            scrollingPaginationElement.innerHTML = "Work in Progress!";
        });
}

var isPickerShown = false;
let formKeys = ['gameTitle[]', 'eventType[]', 'eventTier[]', 'venue', 'date[]'];
var datePicker = new Litepicker({ 
    element: document.getElementById('litepicker'),
    singleMode: false,
    autoApply: false,
    setup: (_datePicker) => {
        _datePicker.on('button:apply', (date1, date2) => {
            _date1 = `${date1.getDate()}/${date1.getMonth() + 1}/${date1.getFullYear()}`;
            _date2 = `${date2.getDate()}/${date2.getMonth() + 1}/${date2.getFullYear()}`;
            console.log({isPickerShown, date1, date2, _date1, _date2});
            isPickerShown = false;
            let startDateInput = document.getElementById('startDate');
            if (startDateInput) startDateInput.value = _date1;
            let endDateInput = document.getElementById('endDate');
            if (endDateInput) endDateInput.value = _date2;
            let tag = document.querySelector(".durationDate");
            tag?.remove();
            addFilterTags(`Date: ${_date1} - ${_date2}`, 'durationDate', `${_date1}${_date2}`);
            console.log({isPickerShown});
            fetchSearchSortFiter();
            fetchVariables.visualize();
        });
        _datePicker.on('button:cancel', () => {
            console.log({isPickerShown});
            isPickerShown = false;
            let startDateInput = document.getElementById('startDate');
            if (startDateInput) startDateInput.value = "";
            let endDateInput = document.getElementById('endDate').value = "";
            if (endDateInput) endDateInput.value = "";
            let tag = document.querySelector(".durationDate");
            tag?.remove();
            fetchSearchSortFiter();
            fetchVariables.visualize();
        });
    }
});

function togglePicker(event) {
    if (event) {
        datePicker?.show(); }
    }

document.getElementById("dropdownFilterDate")?.addEventListener('click', togglePicker);

const SORT_CONSTANTS = {
    'ASC' : 'asc',
    'DESC': 'desc',
    'NONE': 'none'
};

const STORAGE_KEYS = {
    'SORT_TYPE': 'sortType',
    "FILTER": 'filter',
    "SEARCH": 'search',
    "SORT_KEY" : 'sortKey'
}


function toggleDropdown(id) {
    
    let dropdown = document.querySelector(`#${id}[data-bs-toggle='dropdown']`);
    dropdown.parentElement.click();
}

document.getElementById('searchInput').addEventListener('keydown',
    debounce((e) => {
        handleSearch();
    }, 1000)
);

let fetchVariables = new FetchVariables(
    "filterForm", formKeys    
);    

function fetchSearchSortFiter() {
    resetNoMoreElement();

    let params = new URLSearchParams(window.location.search);

    ENDPOINT_URL = ENDPOINT_URL_OG;

    let body = {
        page: params.get('page') ?? 1,
        filter: fetchVariables.getFilter(),
        sort: { 
            [fetchVariables.getSortKey()] : fetchVariables.getSortType()
        },
        userId,
        search: fetchVariables.getSearch(),
        status: params.get('status')
    }

    loadByPost(ENDPOINT_URL, body, () => {
        window.initSocialShareModals();
        reloadToolTips();
    });     
}

function setFilterForFetch(event, title) {

    let value = event.target.value;

    if (event.target.checked) {
        addFilterTags(title, event.target.name, event.target.value);

    } else {
        deleteTagByNameValue(event.target.name, event.target.value);
    }

    fetchSearchSortFiter();
    fetchVariables.visualize();
}

function deleteTagByNameValue(name, value){
    let id = `${name}${value}tag`;
    let tagElement = document.getElementById(id);
    tagElement?.remove();
}

function deleteTagByParent(event, name, value) {
    let element = event.currentTarget;
    element.parentElement.remove();
    let inputCheckbox = document.querySelector(`input[name="${name}"][value="${value}"][type="checkbox"]`);
    if (inputCheckbox) {
        inputCheckbox.checked = false;
        inputCheckbox.removeAttribute("checked");
    } else {
        datePicker.clearSelection();
        let startDateInput = document.getElementById('startDate');
        if (startDateInput) startDateInput.value = "";
        let endDateInput = document.getElementById('endDate').value = "";
        if (endDateInput) endDateInput.value = "";
    }

    fetchSearchSortFiter();
    fetchVariables.visualize();
}

function addFilterTags(title, name, value) {
    let tagElement = document.getElementById('insertFilterTags');
    tagElement.classList.remove('d-none');
    tagElement.classList.add('d-flex', 'flex-wrap');
    tagElement.innerHTML += `
        <div id='${name}${value}tag' class="me-3 ${name} px-3 py-1 d-flex justify-content-around mb-2" style="background-color: #95AEBD; color: white; border-radius: 30px;"> 
            <span class="me-3"> ${title} </span>
            <svg
                onclick="deleteTagByParent(event, '${name}', '${value}');"
                xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle mt-1 cursor-pointer" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
            </svg> 
        </div>

    `;
}

function setSortForFetch(key, title) {
    let sortKey = fetchVariables.getSortKey();
    let sortType = fetchVariables.getSortType();
    let sortByTitleId = document.getElementById('sortByTitleId');
    sortByTitleId.textContent = title;
    fetchVariables.setSortKey(key);
    fetchVariables.setSortType(sortType); 
    let elementView = document.querySelector(`.${sortType}-sort-icon`);
    elementView?.classList.remove("d-none");
    elementView?.classList.add("d-inline"); 
    fetchSearchSortFiter();
    fetchVariables.visualize();
}

function setFetchSortType(event) {
    let sortType = fetchVariables.getSortType();
    let sortKey = fetchVariables.getSortKey();
    if (sortType === SORT_CONSTANTS['ASC']) {
        sortType = SORT_CONSTANTS['DESC'];
    } else if (sortType === SORT_CONSTANTS['DESC']) {
        sortType = SORT_CONSTANTS['NONE'];
    } else {
        sortType = SORT_CONSTANTS['ASC'];
    }

    let parentElement = document.getElementById("insertSortTypeIcon"); 
    let childElements = parentElement.children;

    for (let i = 0; i < childElements.length; i++) {
        childElements[i]?.classList.add("d-none");
    }
    
    let elementView = document.querySelector(`.${sortType}-sort-icon`);
    elementView?.classList.remove("d-none");
    elementView?.classList.add("d-inline");      
    fetchVariables.setSortType(sortType);   
    fetchSearchSortFiter();
    fetchVariables.visualize();  
}

const copyUrlFunction2 = (copyUrl) => {
    navigator.clipboard.writeText(copyUrl).then(function() {
    }, function(err) {
        console.error('Could not copy text to clipboard: ', err);
    });
}

var ENDPOINT_URL;

let params = new URLSearchParams(window.location.search);

ENDPOINT_URL = `/organizer/event/?page=${params.get('page')}&status=${params.get('status')}`; 


function handleSearch() {
    const inputElement = document.getElementById('searchInput');
    const inputValue = inputElement.value;
    const nextSearch = inputElement.nextElementSibling;

    if (nextSearch) {
        if (inputValue === null || inputValue === undefined || String(inputValue).trim() === '') {
            nextSearch.classList.add('d-none');
        } else {
            nextSearch.classList.remove('d-none')
        }
    }

    let params = new URLSearchParams(window.location.search);

    ENDPOINT_URL = ENDPOINT_URL_OG;
    fetchVariables.setSearch(inputValue)

    let body = {
        page: 1,
        status: params.get('status'),
        filter: fetchVariables.getFilter(),
        sort: { 
            [fetchVariables.getSortKey()] : fetchVariables.getSortType()
        },
        userId,
        search: inputValue
    }

    
    loadByPost(ENDPOINT_URL, body, () => {
        window.initSocialShareModals();
        reloadToolTips();
    });
}

function resetUrl() {
    let url = eventIndexUrl;
    window.location.href = url;
}

function goToCreateScreen() {
    let url = eventCreateUrl;
    window.location.href = url;
}

function openElementById(id) {
    const element = document.getElementById(id);
    
    if (element) {
        element?.classList.remove("d-none");
    }
}

function closeElementById(id) {
    const element = document.getElementById(id);

    if (element && !(element.classList.contains("d-none"))) {
        element?.classList.add("d-none");
    }
}

const sortByList = ["startDate", "endDate"];

window.addEventListener(
    "scroll",
    debounce((e) => {

        var windowHeight = window.innerHeight;
        var documentHeight = document.documentElement.scrollHeight;
        var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        if (scrollTop + windowHeight >= documentHeight - 600) {
            
            let params = new URLSearchParams(window.location.search);

            fetchVariables.setCurrentPage(fetchVariables.getCurrentPage() + 1);

            let body = {};

            if ( fetchVariables.getCurrentPage() > fetchVariables.getFetchedPage() + 1 ) {
                fetchVariables.setCurrentPage( fetchVariables.getFetchedPage() );
                return;
            }

            fetchVariables.setFetchedPage(fetchVariables.getCurrentPage());
            
            ENDPOINT_URL = ENDPOINT_URL_OG;

            body = {
                filter: fetchVariables.getFilter(),
                sort: { 
                    [fetchVariables.getSortKey()] : fetchVariables.getSortType()
                },
                userId,
                search: fetchVariables.getSearch(),
                status: params.get('status'),
                page: fetchVariables.getCurrentPage()
            }
            try{
                infinteLoadMoreByPost(ENDPOINT_URL, body, () => {
                    window.initSocialShareModals();
                    reloadToolTips();
                });

            } catch {
                fetchVariables.setFetchedPage(fetchVariables.getCurrentPage()-1);
            };
        }
    }, 300)
);

function reloadToolTips () {
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => {
        // Remove existing tooltip if it exists
        const existingTooltip = window.bootstrap.Tooltip.getInstance(tooltipTriggerEl);
        if (existingTooltip) {
            existingTooltip.dispose();
        }
        // Create new tooltip
        return new window.bootstrap.Tooltip(tooltipTriggerEl);
    });

}
window.addOnLoad(()=> {
    reloadToolTips();
})
