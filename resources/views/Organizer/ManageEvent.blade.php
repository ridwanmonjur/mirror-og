@include('Organizer.Layout.ManageEventHeadTag')

<body>
    @include('CommonLayout.Navbar')

    <main>
        <br class="d-none-at-desktop">
        <div class="">
            <header class="flexbox-welcome">
                <u>
                    <h3>
                        Manage your events
                    </h3>
                </u>
                <button class="oceans-gaming-default-button" value="Create Event" onclick="goToCreateScreen();">
                    Create Event
                </button>


            </header>
            <div class="flexbox-filter">
                <p class="status-ALL">
                    <a href="{{ route('event.index', ['status' => 'ALL', 'page' => 1]) }}">All</a>
                </p>
                <p class="status-LIVE">
                    <a href="{{ route('event.index', ['status' => 'LIVE', 'page' => 1]) }}">Live</a>
                </p>
                <p class="status-SCHEDULED">
                    <a href="{{ route('event.index', ['status' => 'SCHEDULED', 'page' => 1]) }}">Scheduled</a>
                </p>
                <p class="status-DRAFT">
                    <a href="{{ route('event.index', ['status' => 'DRAFT', 'page' => 1]) }}">Drafts</a>
                </p>
                <p class="status-ENDED">
                    <a href="{{ route('event.index', ['status' => 'ENDED', 'page' => 1]) }}">Ended</a>
                </p>
            </div>
            <br>
            <div>
                <span class="icon2"
                    onclick="openElementById('close-option'); openElementById('filter-option');  closeElementById('sort-option');">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="feather feather-filter">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    <span> Filter </span>
                </span>
                &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                <span class="icon2"
                    onclick="openElementById('close-option'); openElementById('sort-option'); closeElementById('filter-option');">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.2 7.8l-7.7 7.7-4-4-5.7 5.7" />
                        <path d="M15 7h6v6" />
                    </svg>
                    <span>
                        Sort
                    </span>
                </span>
                &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                <span class="icon2 d-none" id="close-option"
                    onclick="closeElementById('close-option'); closeElementById('filter-option'); closeElementById('sort-option');">
                    <span style="font-weight: 900;">X</span>
                    <span>
                        Close
                    </span>
                </span>
            </div>
            <br>
            <div id="sort-option" class="d-none">
                <form onSubmit="onSubmit(event);">
                    <label> Sort by:</label>
                    &emsp;&emsp;&emsp;
                    <span class="sort-box">
                        <input onchange="setLocalStorageSort(event);" type="radio" name="sort" value="startDate">
                        <label for="startDate">Start Date</label>
                        <span class="startDateSortIcon sortIcon">
                            <svg onclick="setLocalStorageSortIcon('startDate');" xmlns="http://www.w3.org/2000/svg"
                                width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="feather feather-refresh-cw">
                                <polyline points="23 4 23 10 17 10"></polyline>
                                <polyline points="1 20 1 14 7 14"></polyline>
                                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                            </svg>
                        </span>
                    </span>
                    &ensp; &ensp;
                    <span class="sort-box">
                        <input onchange="setLocalStorageSort(event);" type="radio" name="sort" value="endDate">
                        <label for="endDateSortIcon endIcon">End Date</label>
                        <span class="endDateSortIcon sortIcon">
                            <svg onclick="setLocalStorageSortIcon('endDate');" xmlns="http://www.w3.org/2000/svg"
                                width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="feather feather-refresh-cw">
                                <polyline points="23 4 23 10 17 10"></polyline>
                                <polyline points="1 20 1 14 7 14"></polyline>
                                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                            </svg>
                        </span>
                    </span>
                    &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;
                    <button type="button" onclick="resetUrl();" class="oceans-gaming-default-button"
                        style="background: #8CCD39 !important">
                        Reset
                    </button>
                    <button type="submit" class="oceans-gaming-default-button">Save & Sort</button>
                    <br> &emsp;&emsp;&emsp;&emsp;&emsp;
                    &nbsp; &nbsp;
                </form>
            </div>

            <div id="filter-option" class="d-none">
                <form name="filter" onSubmit="onSubmit(event);">
                    <!-- Include existing request parameters -->
                    <div>
                        <label> Game Title:</label>
                        &emsp;
                        <input onchange="setLocalStorageFilter(event);" type="radio" name="gameTitle"
                            value="Dota 2">
                        <label for="gameTitle">Dota 2</label>
                        &ensp;
                        <input onchange="setLocalStorageFilter(event);" type="radio" name="gameTitle"
                            value="Dota">
                        <label for="gameTitle">Dota</label>
                        &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;
                        <button type="button" onclick="resetUrl();" class="oceans-gaming-default-button"
                            style="background: #8CCD39 !important">
                            Reset
                        </button>
                        <button type="submit" class="oceans-gaming-default-button">Save & Filter</button>
                    </div>
                    <div>
                        <label> Event Type:</label>
                        &emsp;
                        <input onchange="setLocalStorageFilter(event);" type="radio" name="eventType"
                            value="Tournament">
                        <label for="eventTyoe">Tournament</label>
                        &ensp;
                        <input onchange="setLocalStorageFilter(event);" type="radio" name="eventType"
                            value="League">
                        <label for="eventTyoe">League</label>
                    </div>
                    <div>
                        <label> Event Tier: </label>
                        &emsp;&nbsp;&nbsp;
                        <input onchange="setLocalStorageFilter(event);" type="radio" name="eventTier"
                            value="Dolphin">
                        <label for="eventTier">Dolphin</label>
                        &ensp;
                        <input onchange="setLocalStorageFilter(event);" type="radio" name="eventTier"
                            value="Turtle">
                        <label for="eventTier">Turtle</label>
                        &ensp;
                        <input onchange="setLocalStorageFilter(event);" type="radio" name="eventTier"
                            value="Starfish">
                        <label for="eventTier">Starfish</label>
                    </div>
                </form>
            </div>
            <br>
            <div class="search-bar">
                <svg onclick= "handleInputBlur();" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" class="feather feather-search search-bar2-adjust">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" onkeydown="handleInputBlur();" name="search" id="searchInput"
                    placeholder="Search using title, description, or keywords">
                <button type="button" onclick="resetUrl();" class="oceans-gaming-default-button d-none"
                    style="background: #8CCD39 !important">
                    Reset
                </button>
            </div>
        </div>
        <br><br>
        <div class="modal fade" id="shareModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Share on social media</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        NEED TO GET UI FROM LEIGH
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="scrolling-pagination grid-container">
            @include('Organizer.ManageEventScroll')
        </div>
        <svg class="none-sort-icon d-none" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round" class="feather feather-refresh-cw">
            <polyline points="23 4 23 10 17 10"></polyline>
            <polyline points="1 20 1 14 7 14"></polyline>
            <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
        </svg>
        <svg class="asc-sort-icon d-none" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round" class="feather feather-chevrons-up">
            <polyline points="17 11 12 6 7 11"></polyline>
            <polyline points="17 18 12 13 7 18"></polyline>
        </svg>
        <svg class="desc-sort-icon d-none" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round" class="feather feather-chevrons-down">
            <polyline points="7 13 12 18 17 13"></polyline>
            <polyline points="7 6 12 11 17 6"></polyline>
        </svg>

        <div class="no-more-data d-none" style="margin-top: 50px;"></div>
        @include('CommonLayout.BootstrapJs')
        <script src="{{ asset('/assets/js/navbar/toggleNavbar.js') }}"></script>
        <script src="{{ asset('/assets/js/pagination/loadMore.js') }}"></script>

        <script>
            $(document).ready(function() {
                $('.activate-tooltip').tooltip();
            });
        </script>

        <script>
            ["sort", "filter", "sortType"].forEach((name) => {
                localStorage.removeItem(name);
            })

            var page = 1;

            function onSubmit(event) {
                event.preventDefault();
                let params = convertUrlStringToQueryStringOrObject({
                    isObject: true
                });
                if (!params) params = {}
                params.page = 1;
                ENDPOINT = "{{ route('event.search.view') }}";
                let body = {
                    ...params,
                    filter: JSON.parse(localStorage.getItem('filter')),
                    sort: JSON.parse(localStorage.getItem('sort')),
                    userId: Number("{{ auth()->user()->id }}")
                }
                loadByPost(ENDPOINT, body);
            }

            function setLocalStorageFilter(event) {
                console.log(event.target.value);
                console.log(event.target.value);
                console.log(event.target.value);
                console.log(event.target.value);
                let localItem = localStorage.getItem('filter') ?? null;
                let filter = null;
                if (localItem) filter = JSON.parse(localItem);
                else filter = {};
                let value = event.target.value;
                if (event.target.checked) {
                    filter[event.target.name] = value;
                } else {
                    delete filter[event.target.name];
                }
                localStorage.setItem('filter', JSON.stringify(filter));
            }

            function setLocalStorageSort(event) {
                let key = event.target.value;
                let localItem = localStorage.getItem('sort') ?? null;
                let sort = null;
                if (localItem) sort = JSON.parse(localItem);
                else sort = {};
                if (event.target.checked) {
                    sort[key] = 'asc';
                    let iconSpan = document.querySelector(`.${key}SortIcon`);
                    iconSpan.innerHTML = "";
                    let cloneNode = document.querySelector(`.asc-sort-icon`).cloneNode(true);
                    cloneNode.classList.remove('d-none');
                    cloneNode.onclick = () => {
                        setLocalStorageSortIcon(key);
                    }
                    iconSpan.appendChild(cloneNode);
                } else {
                    delete sort[key];
                }
                localStorage.setItem('sort', JSON.stringify(sort));
            }

            function setLocalStorageSortIcon(key) {
                let input = document.querySelector(`input[value=${key}][type='radio']`);
                let isChecked = input.checked;
                let localItem = localStorage.getItem('sort') ?? null;
                let sort = null;
                if (localItem) sort = JSON.parse(localItem);
                else sort = {};
                let value = 'none';
                if (isChecked) {
                    if (key in sort) {
                        value = sort[key];
                    }
                    if (value == 'asc') {
                        value = 'desc';
                    } else if (value == 'desc') {
                        value = 'none';
                    } else {
                        value = 'asc';
                    }
                }
                if (value == 'none') {
                    input.checked = false;
                }
                if (input.checked) {
                    sort[key] = value;
                } else {
                    delete sort[key];
                }
                let iconSpan = document.querySelector(`.${key}SortIcon`);
                iconSpan.innerHTML = "";
                let cloneNode = document.querySelector(`.${value}-sort-icon`).cloneNode(true);
                cloneNode.classList.remove('d-none');
                cloneNode.onclick = () => {
                    setLocalStorageSortIcon(key);
                }
                iconSpan.appendChild(cloneNode);
                localStorage.setItem('sort', JSON.stringify(sort));
            }
        </script>
        <script>
            const copyUrlFunction = (copyUrl) => {
                navigator.clipboard.writeText(copyUrl).then(function() {
                    console.log('Copying to clipboard was successful! Copied: ' + copyUrl);
                }, function(err) {
                    console.error('Could not copy text to clipboard: ', err);
                });
            }

            var ENDPOINT;

            function getQueryStringValue(key) {
                const urlParams = new URLSearchParams(window.location.search);
                return urlParams.get(key);
            }

            function convertObjectToURLString(object) {
                var queryString = "";
                for (const [key, value] of Object.entries(object)) {
                    if (Array.isArray(value)) {
                        value.forEach(function(value) {
                            queryString += `${key}=${value}&`;
                        });
                    } else {
                        queryString += `${key}=${value}&`;
                    }
                }
                return queryString;
            }

            function convertUrlStringToQueryStringOrObject({
                isObject
            } = {
                isObject: false
            }) {
                var queryString = window.location.search;
                var queryString = queryString.substring(1);
                var paramsArray = queryString.split("&");
                var params = {};
                paramsArray.forEach(function(param) {
                    var pair = param.split("=");
                    var key = decodeURIComponent(pair[0]);
                    var value = decodeURIComponent(pair[1] || '');
                    if (key.trim() != "") {
                        if (key in params) {
                            params[key] = [...params[key], value];
                        } else {
                            params[key] = [value];

                        }
                    }
                });
                if (isObject) return params;
                else return convertObjectToURLString(params);
            }
            ENDPOINT = "/organizer/event/?" + convertUrlStringToQueryStringOrObject({
                isObject: false
            });


            function handleInputBlur() {
                const inputElement = document.getElementById('searchInput');
                const inputValue = inputElement.value;
                const nextSearch = inputElement.nextElementSibling;
                if (nextSearch) {
                    if (String(inputValue).trim() === '') {
                        nextSearch.classList.add('d-none');
                    } else {
                        nextSearch.classList.remove('d-none')
                    }
                }
                let params = convertUrlStringToQueryStringOrObject({
                    isObject: true
                });
                if (!params) params = {}
                params.page = 1;
                ENDPOINT = "{{ route('event.search.view') }}";
                let body = {
                    ...params,
                    filter: JSON.parse(localStorage.getItem('filter')),
                    sort: JSON.parse(localStorage.getItem('sort')),
                    userId: Number("{{ auth()->user()->id }}"),
                    search: inputValue
                }
                loadByPost(ENDPOINT, body);
            }

            function resetUrl() {
                let url = "{{ route('event.index') }}";
                window.location.href = url;
            }

            function goToCreateScreen() {
                let url = "{{ route('event.create') }}";
                window.location.href = url;
            }

            // open/ close
            function openElementById(id) {
                const element = document.getElementById(id);
                if (element) element?.classList.remove("d-none");
            }

            function closeElementById(id) {
                const element = document.getElementById(id);
                if (element && !(element.classList.contains("d-none"))) element?.classList.add("d-none");
            }

            const sortByList = ["startDate", "endDate"];

            function sortAscending(index) {
                const list = document.querySelectorAll('.ascending');
                element = list[index];
                element.classList.toggle('d-none');
                element.nextElementSibling.classList.toggle('d-none');
                // setHiddenElementValue(sortByList[index], 'desc') --}}
            }

            function sortDescending(index) {
                const list = document.querySelectorAll('.descending');
                element = list[index]
                element.classList.toggle('d-none');
                element.previousElementSibling.previousElementSibling.classList.toggle('d-none');
                setHiddenElementValue(sortByList[index], 'none')
            }

            function sortNone(index) {
                const list = document.querySelectorAll('.no-sort');
                element = list[index]
                element.classList.toggle('d-none');
                element.nextElementSibling.classList.toggle('d-none');
                setHiddenElementValue(sortByList[index], 'asc')
            }

            function setHiddenElementValue(key, value) {
                let input = document.querySelector(`input[name=sortType]`);
                const sortType = (isValidJson(input.value)) ? JSON.parse(input.value) : {};
                if (value == "none" && key in sortType) {
                    delete sortType[key];
                } else {
                    sortType[key] = value;
                }
                input.value = JSON.stringify(sortType);
            }

            const urlParams = convertObjectToURLString(window.location.search);


            // Function to check if a string is valid JSON
            function isValidJson(str) {
                try {
                    JSON.parse(str);
                    return true;
                } catch (e) {
                    return false;
                }
            }

            window.onload = function() {
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.get('status')) {
                    let value = urlParams.get('status');
                    let element = document.querySelector(`p.status-${value}`)
                    element.style.color = '#8CCD39';
                    element.style.border = '3px solid #43A4D7';
                    element.style.padding = "5px";
                    element.style.borderRadius = '12px';
                }
                const inputNameList = ['gameTitle', 'eventTier', 'eventType', 'sort'];
                for (let j = 0; j < inputNameList.length; j++) {
                    const inputName = inputNameList[j];
                    const inputParamValueListFromName = urlParams.getAll(inputName);
                    for (let i = 0; i < inputParamValueListFromName.length; i++) {
                        const inputParamValueFromName = inputParamValueListFromName[i];
                        const checkbox = document.querySelector(
                            `input[type="radio"][name="${inputName}"][value="${inputParamValueFromName}"]`);
                        if (checkbox) {
                            checkbox.checked = true
                        }
                    }
                }
                const sortType = urlParams.get('sortType');
                if (sortType) {
                    const sortTypeJson = isValidJson(sortType) ? JSON.parse(sortType) : {};
                    document.querySelector(`input[name="sortType"]`).value = sortType;
                    const sortTypeJsonKeys = Object.keys(sortTypeJson);
                    var sortBoxes = document.querySelectorAll('.sort-box');

                    for (let i = 0; i < sortTypeJsonKeys.length; i++) {
                        const sortTypeJsonKey = sortTypeJsonKeys[i];
                        const sortTypeJsonValue = sortTypeJson[sortTypeJsonKey];
                        console.log({
                            sortTypeJsonKey,
                            sortTypeJsonValue
                        });
                        let index = sortByList.indexOf(sortTypeJsonKey);
                        if (index < 0) continue;
                        var childElements = sortBoxes[index].children;
                        var lastThreeChildren = Array.from(childElements).slice(-3);
                        lastThreeChildren.forEach(function(child) {
                            if (!child.classList.contains('d-none')) {
                                child.classList.add('d-none');
                            }
                        });
                        if (sortTypeJsonValue === 'asc') {
                            lastThreeChildren[1].classList.remove('d-none');
                        } else if (sortTypeJsonValue === 'desc') {
                            lastThreeChildren[2].classList.remove('d-none');
                        } else {
                            lastThreeChildren[0].classList.remove('d-none');
                        }
                    }
                }
            }

            window.addEventListener(
                "scroll",
                throttle((e) => {
                    var windowHeight = window.innerHeight;
                    var documentHeight = document.documentElement.scrollHeight;
                    var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                    if (scrollTop + windowHeight >= documentHeight - 200) {
                        let params = convertUrlStringToQueryStringOrObject({
                            isObject: true
                        });
                        page++;
                        let body = {};
                        if (!params) params = {}
                        params.page = page;
                        ENDPOINT = "{{ route('event.search.view') }}";
                        body = {
                            filter: JSON.parse(localStorage.getItem('filter')),
                            sort: JSON.parse(localStorage.getItem('sort')),
                            userId: Number("{{ auth()->user()->id }}"),
                            ...params
                        }
                        infinteLoadMoreByPost(ENDPOINT, body);
                    }
                }, 600)
            );
        </script>
    </main>
