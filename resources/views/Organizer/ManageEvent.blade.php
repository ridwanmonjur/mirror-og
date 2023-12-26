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
                <input type="submit" value="Create Event" onclick="goToCreateScreen();">


            </header>
            <div class="flexbox-filter">
                <p class="status-ALL">
                    <a href="{{ route('event.index', 
                        array_merge(request()->query(), ['status' => 'ALL', 'page' => 1])
                        ) }}">All</a>
                </p>
                <p class="status-LIVE">
                    <a href="{{ route('event.index',    
                        array_merge(request()->query(), ['status' => 'LIVE', 'page' => 1])
                        ) }}">Live</a>
                </p>
                <p class="status-SCHEDULED">
                    <a href="{{ route('event.index',
                        array_merge(request()->query(), ['status' => 'SCHEDULED', 'page' => 1])
                        ) }}">Scheduled</a>
                </p>
                <p class="status-DRAFT">
                    <a href="{{ route('event.index',
                        array_merge(request()->query(), ['status' => 'DRAFT', 'page' => 1])
                        ) }}">Drafts</a>
                </p>
                <p class="status-ENDED">
                    <a href="{{ route('event.index',
                        array_merge(request()->query(), ['status' => 'ENDED', 'page' => 1])
                        ) }}">Ended</a>
                </p>
            </div>
            <br>
            <div>
                <span class="icon2" onclick="openElementById('close-option'); openElementById('filter-option');  closeElementById('sort-option');">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-filter">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    <span> Filter </span>
                </span>
                &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                <span class="icon2" onclick="openElementById('close-option'); openElementById('sort-option'); closeElementById('filter-option');">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.2 7.8l-7.7 7.7-4-4-5.7 5.7" />
                        <path d="M15 7h6v6" />
                    </svg>
                    <span>
                        Sort
                    </span>
                </span>
                &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                <span class="icon2 d-none" id="close-option" onclick="closeElementById('close-option'); closeElementById('filter-option'); closeElementById('sort-option');">
                    <span style="font-weight: 900;">X</span>
                    <span>
                        Close
                    </span>
                </span>
            </div>
            <br>
            <div id="sort-option" class="d-none">
                <form action="{{ route('event.index') }}" method="get">

                    <!-- Include existing request parameters -->
                    @foreach(request()->except('sort', 'sortType', 'page') as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <input type="hidden" name="page" value="1">
                    <label> Sort by:</label>
                    &emsp;&emsp;&emsp;
                    <input type="hidden" name="sortType">
                    <span class="sort-box">
                        <input type="radio" name="sort" value="startDate">
                        <label for="startDate">Start Date</label>
                    </span>
                    &ensp; &ensp;
                    <span class="sort-box">
                        <input type="radio" name="sort" value="endDate">
                        <label for="endDate">End Date</label>
                    </span>
                    &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;
                    <button type="button" onclick="resetUrl();" class="oceans-gaming-default-button" style="background: #8CCD39 !important">
                        Reset
                    </button>
                    <button type="submit" class="oceans-gaming-default-button">Save & Sort</button>
                    <br> &emsp;&emsp;&emsp;&emsp;&emsp;
                    &nbsp; &nbsp;
                </form>
            </div>

            <div id="filter-option" class="d-none">
                <form name="filter" action="{{ route('event.index') }}" method="get">
                    <!-- Include existing request parameters -->
                    @foreach(request()->except('gameTitle', 'eventTier', 'eventType', 'page') as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <input type="hidden" name="page" value="1">
                    <div>
                        <label> Game Title:</label>
                        &emsp;
                        <input type="radio" name="gameTitle" value="Dota 2">
                        <label for="gameTitle">Dota 2</label>
                        &ensp;
                        <input type="radio" name="gameTitle" value="Dota">
                        <label for="gameTitle">Dota</label>
                        &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;
                        <button type="button" onclick="resetUrl();" class="oceans-gaming-default-button" style="background: #8CCD39 !important">
                            Reset
                        </button>
                        <button type="submit" class="oceans-gaming-default-button">Save & Filter</button>
                    </div>
                    <div>
                        <label> Event Type:</label>
                        &emsp;
                        <input type="radio" name="eventType" value="Tier">
                        <label for="eventTier">Tier</label>
                        &ensp;
                        <input type="radio" name="eventType" value="League">
                        <label for="eventTier">League</label>
                    </div>
                    <div>
                        <label> Event Tier: </label>
                        &emsp;&nbsp;&nbsp;
                        <input type="radio" name="eventTier" value="Dolphin">
                        <label for="eventType">Dolphin</label>
                        &ensp;
                        <input type="radio" name="eventTier" value="Turtle">
                        <label for="eventType">Turtle</label>
                        &ensp;
                        <input type="radio" name="eventTier" value="Starfish">
                        <label for="eventType">Starfish</label>
                    </div>
                </form>
            </div>
            <br>
            <div class="search-bar">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search search-bar2-adjust">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" onblur="handleInputBlur();" name="search" id="searchInput" placeholder="Search using title, description, or keywords">
                <button type="button" onclick="resetUrl();" class="oceans-gaming-default-button d-none" style="background: #8CCD39 !important">
                    Reset
                </button>
            </div>
        </div>
        <br><br>
        <div class="modal fade" id="shareModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
            @include("Organizer.ManageEventScroll")
        </div>

        <div class="no-more-data d-none" style="margin-top: 50px;"></div>
        @include('CommonLayout.BootstrapJs')
        <script src="{{ asset('/assets/js/pagination/loadMore.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

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

            var debounceTimer;

            function debouncedFunction() {
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
                params.search = inputValue;
                params.page = 1;
                ENDPOINT = `/organizer/event/?` + convertObjectToURLString(params);
                document.querySelector('.scrolling-pagination').innerHTML = '';
                window.history.replaceState({}, document.title, ENDPOINT);
                infinteLoadMore(null, ENDPOINT);
            }

            function handleInputBlur() {
                debouncedFunction();
                // clearTimeout(debounceTimer);
                // debounceTimer = setTimeout(debouncedFunction, 250);
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
                setHiddenElementValue(sortByList[index], 'desc')
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
            console.log({
                urlParams
            })

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
                        const checkbox = document.querySelector(`input[type="radio"][name="${inputName}"][value="${inputParamValueFromName}"]`);
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
            // const eventTypes = urlParams.getAll('eventType');

            const elementList = document.getElementsByClassName("sort-box");
            for (let i = 0; i < elementList.length; i++) {
                elementList[i].innerHTML +=
                    `
                    <span class="no-sort"
                        onclick="sortNone(${i});"
                    >
                        &nbsp; 
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-cw"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                        </svg>
                    </span>
                    <span class="ascending d-none" 
                        onclick="sortAscending(${i});"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevrons-up"><polyline points="17 11 12 6 7 11"></polyline><polyline points="17 18 12 13 7 18"></polyline></svg>
                    </span>
                    <span class="descending d-none"
                        onclick="sortDescending(${i});"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevrons-down"><polyline points="7 13 12 18 17 13"></polyline><polyline points="7 6 12 11 17 6"></polyline></svg>                    
                    </span>
                                       `;
            }

            var page = 1;
            window.addEventListener(
                "scroll",
                throttle((e) => {
                    var windowHeight = window.innerHeight;
                    var documentHeight = document.documentElement.scrollHeight;
                    var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                    if (scrollTop + windowHeight >= documentHeight - 200 ) {
                        let params = convertUrlStringToQueryStringOrObject({
                            isObject: true
                        });
                        page++;
                        params.page = page;
                        ENDPOINT = `/organizer/event/?` + convertObjectToURLString(params);
                        infinteLoadMoreByPost(null, ENDPOINT);
                    }
                }, 300)
            );
        </script>
    </main>
    <script src="{{ asset('/assets/js/navbar/toggleNavbar.js') }}"></script>
  <!-- <script src="script.js"></script> -->