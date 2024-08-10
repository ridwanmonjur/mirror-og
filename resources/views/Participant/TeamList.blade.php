<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Teams</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/organizer/manageEvent.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamList.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @include('__CommonPartials.HeadIcon')
</head>

<body>
    @include('__CommonPartials.NavbarGoToSearchPage')
    <br>
    <main>
        <h5> Your Teams </h5> <br>
        <form id="newTeamsForm">
            <div>
                <input type="hidden" id="countServer" value="{{ $count }}">
                <input type="hidden" id="teamListServer" value="{{ json_encode($teamList) }}">
                <input type="hidden" id="membersCountServer" value="{{ json_encode($membersCount) }}">
                <input type="hidden" id="userIdServer" value="{{ $user->id }}">
                <input type="hidden" name="sortKeys" id="sortKeys" value="">
                <input type="hidden" name="sortType" id="sortType" value="">
                <input name="search" style="width: min(90vw, 450px); font-size: 1rem;" class="rounded-pill px-4 form-control d-inline-block me-3 cursor-pointer" type="text" placeholder="Search for player name/ email">
                <button type="button" class="btn btn-primary text-light px-2 border-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="feather feather-search"
                    >
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </button>

            </div>
            <div class="d-flex justify-content-between  w-70s align-items-center flex-wrap mt-2">
                <div>
                    <div class="cursor-pointer me-5 d-inline-block"
                        onclick="openElementById('close-option'); openElementById('filter-option');  closeElementById('sort-option');">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                            class="bi bi-filter mt-2" viewBox="0 0 16 16">
                            <path
                                d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5" />
                        </svg>
                    </div>
                    <div id="filter-option" class="mx-0 px-0 mb-2 ms-3 d-inline-block">
                        <div class="d-flex justify-content-start">
                            <div class="dropdown me-3">
                                <button class="ps-0 pe-3 py-1 py-2 button-design-removed" type="button"
                                    id="dropdownFilterType" data-bs-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    <span>Created </span>
                                    <span class="dropbtn-arrow">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="feather feather-chevron-down">
                                            <polyline points="6 9 12 15 18 9"></polyline>
                                        </svg>
                                    </span>
                                </button>
                                <div onclick="event.stopPropagation();" class="dropdown-menu px-3"
                                    aria-labelledby="dropdownFilterSort">
                                    <p class="mb-1">Choose a date to filter team by creation time</p>
                                    <input type="date" class="form-control" name="created_at">
                                    <button id="created_atResetButton" type="button" class="my-2 rounded-pill btn btn-sm btn-primary text-light"
                                        onclick="
                                        resetInput('created_at');
                                    ">
                                        Reset
                                    </button>
                                </div>
                            </div>

                            <div class="dropdown me-3">
                                <button class="ps-0 pe-3 py-2 button-design-removed" type="button"
                                    id="dropdownFilterSort" data-bs-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    <span>Region </span>
                                    <span class="dropbtn-arrow">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="feather feather-chevron-down">
                                            <polyline points="6 9 12 15 18 9"></polyline>
                                        </svg>
                                    </span>
                                </button>
                                <div onclick="event.stopPropagation;"; class="dropdown-menu px-0 py-1"
                                    aria-labelledby="dropdownFilterSort">
                                    <div class="px-3 py-1">
                                        <p class="mb-1">Choose a country of origin</p>
                                        {{-- <input id="select2-country2" type="checkbox" name="venue"> --}}
                                        <select id="select2-country2" class="form-control" name="region"
                                            style="width: 200px !important;">
                                            <option value=""> </option>
                                        </select>
                                        <button type="button"
                                            class="my-2 rounded-pill btn btn-sm btn-primary text-light"
                                            id="regionResetButton"
                                            onclick="
                                                resetInput('region');
                                            ">
                                            Reset 
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown me-3">
                                <button class="ps-0 pe-3 py-2 button-design-removed" type="button"
                                    id="dropdownFilterSort" data-bs-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    <span>Status</span>
                                    <span class="dropbtn-arrow">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="feather feather-chevron-down">
                                            <polyline points="6 9 12 15 18 9"></polyline>
                                        </svg>
                                    </span>
                                </button>
                                <div onclick="event.stopPropagation();" class="dropdown-menu px-0 py-1"
                                    aria-labelledby="dropdownFilterSort">
                                    @foreach ([['title' => 'Public (free to apply)', 'value' => 'public'], ['title' => 'Private', 'value' => 'Private (cannot apply)'] ] as $status)
                                        <div class="px-3 py-1" style="width: 200px;">
                                            <input type="checkbox" name="status" value="{{ $status['value'] }}">
                                            <label for="status">{{ $status['title'] }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="dropdown me-3" style="min-width: 250px;">
                                <button class="ps-0 pe-3 py-2 button-design-removed" type="button"
                                    id="dropdownFilterSort" data-bs-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    <span>Members</span>
                                    <span class="dropbtn-arrow">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="feather feather-chevron-down">
                                            <polyline points="6 9 12 15 18 9"></polyline>
                                        </svg>
                                    </span>
                                </button>
                                <div onclick="event.stopPropagation();" class="dropdown-menu px-3 py-1"
                                    aria-labelledby="dropdownFilterSort">
                                        <p class="mb-1">Choose the minimumn number of members in team</p>
                                        <input type="range" class="form-range" name="membersCount" min="0" defaultValue="0" value="0" max="10" step="1" id="customRange3">
                                         <button type="button"
                                            class="my-2 rounded-pill btn btn-sm btn-primary text-light"
                                            id="membersCountResetButton"
                                            onclick="
                                                resetInput('membersCount');
                                            ">
                                            Reset 
                                        </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="sort-option" class="mx-0 px-0 mb-3 d-inline-block">
                    <div class="ddropdown dropdown-click-outside d-inline-block">
                        <span class="sort-icon-list" onclick="changeSortType()">
                            {{-- Ascending --}}
                            <svg data-value="asc-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="d-none cursor-pointer gear-icon-button bi bi-sort-alpha-up" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M10.082 5.629 9.664 7H8.598l1.789-5.332h1.234L13.402 7h-1.12l-.419-1.371zm1.57-.785L11 2.687h-.047l-.652 2.157z"/>
                            <path d="M12.96 14H9.028v-.691l2.579-3.72v-.054H9.098v-.867h3.785v.691l-2.567 3.72v.054h2.645zm-8.46-.5a.5.5 0 0 1-1 0V3.707L2.354 4.854a.5.5 0 1 1-.708-.708l2-1.999.007-.007a.5.5 0 0 1 .7.006l2 2a.5.5 0 1 1-.707.708L4.5 3.707z"/>
                            </svg>
                            {{-- Descending --}}
                            <svg data-value="desc-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="d-none cursor-pointer gear-icon-button bi bi-sort-alpha-down" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M10.082 5.629 9.664 7H8.598l1.789-5.332h1.234L13.402 7h-1.12l-.419-1.371zm1.57-.785L11 2.687h-.047l-.652 2.157z"/>
                                <path d="M12.96 14H9.028v-.691l2.579-3.72v-.054H9.098v-.867h3.785v.691l-2.567 3.72v.054h2.645zM4.5 2.5a.5.5 0 0 0-1 0v9.793l-1.146-1.147a.5.5 0 0 0-.708.708l2 1.999.007.007a.497.497 0 0 0 .7-.006l2-2a.5.5 0 0 0-.707-.708L4.5 12.293z"/>
                            </svg>
                        </span>
                        <button class="dropbtn py-1 px-2 me-3" type="button" id="dropdownSortButton"
                            style="min-width: 150px; display: inline-block;" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <span id="sortByTitleId">Sort by:</span>
                            <span class="dropbtn-arrow">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="feather feather-chevron-down">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </span>
                        </button>
                        <div onclick="event.stopPropagation();" class="dropdown-menu px-3 ms-3"
                            aria-labelledby="dropdownSortButton">
                            <div class="sort-box d-block min-w-150px hover-bigger ps-3 py-1"
                                onclick="setSortForFetch('recent');">
                                <label class="me-3 cursor-pointer" for="recent">Recent</label>
                            </div>
                            <div class="sort-box d-block min-w-150px hover-bigger ps-3 py-1"
                                onclick="setSortForFetch('created_at');">
                                <label class="me-3 cursor-pointer" for="created_at">Created</label>
                            </div>
                            <div class="sort-box d-block min-w-150px hover-bigger ps-3 py-1"
                                onclick="setSortForFetch('region');">
                                <label class="me-3 cursor-pointer" for="region">Region</label>
                            </div>
                            <div class="sort-box d-block min-w-150px hover-bigger ps-3 py-1"
                                onclick="setSortForFetch('name');">
                                <label class="me-3 cursor-pointer" for="name">Name</label>
                            </div>
                            <div class="sort-box d-block min-w-150px hover-bigger ps-3 py-1"
                                onclick="setSortForFetch('membersCount');">
                                <label class="me-3 cursor-pointer" for="name">Members</label>
                            </div>
                            <div class="d-block min-w-150px hover-bigger ps-3 py-1" onclick="resetInput('sortKeys');">
                                <button id="sortKeysResetButton" type="button" class="rounded-pill btn btn-sm btn-primary text-light">
                                    Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="filter-search-results">
                <span class="me-5 cursor-not-allowed" class="">
                    <small class="me-4">Filter/ Sort: </small>
                    <span class="">
                        <small data-form-parent="default-filter" class="me-2">
                            <small class="btn btn-secondary text-light rounded-pill px-2 py-0">
                                Default
                            </small>
                        </small>
                        <small data-form-parent="created_at" class="me-2">
                        </small>
                        <small data-form-parent="region" class="me-2">
                        </small>
                        <small data-form-parent="status" class="me-2">
                        </small>
                        <small data-form-parent="membersCount" class="me-2">
                        </small>
                        <small data-form-parent="sortKeys">
                        </small>
                    </span>
                    {{-- <small  id="default-sorts" class="btn btn-primary text-light px-2 py-0">Default</small> --}}
                </span>
            </div>
            <div class="grid-3-columns justify-content-center" id="filter-sort-results" style="grid-auto-rows : 1fr !important;">
            </div>
        </form>
        <br>
        <br>
    </main>

    <script>
        function goToScreen() {
            window.location.href = "{{ route('participant.request.view') }}";
        }

        // Elements
        let newTeamsForm = document.getElementById('newTeamsForm');
        let filteredSortedTeams = [];
        let newTeamsFormKeys = ['sortKeys', 'created_at', 'region', 'status', 'membersCount'];
        let sortKeysInput = document.getElementById("sortKeys");

        let teamListServer = document.getElementById('teamListServer');
        let userIdServer = document.getElementById('userIdServer');
        let membersCountServer = document.getElementById('membersCountServer');
        let countServer = document.getElementById('countServer');
        let teamListServerValue = JSON.parse(teamListServer.value);
        let membersCountServerValue = JSON.parse(membersCountServer.value);
        let countServerValue = Number(countServer.value);
        let userIdServerValue = Number(userIdServer.value);
        let filterSortResultsDiv = document.getElementById('filter-sort-results');
        console.log({
            teamListServerValue,
            membersCountServerValue,
            countServerValue
        });

        // UTILITIES

        function getNestedValue(obj, propertyPath) {
            return propertyPath.split('.').reduce((acc, part) => acc && acc[part], obj);
        }

        function sortByProperty(arr, propertyPath, ascending = true) {
            return arr.sort((a, b) => {
                let aValue = getNestedValue(a, propertyPath);
                let bValue = getNestedValue(b, propertyPath);

                if (typeof aValue === 'string' && typeof bValue === 'string') {
                    return ascending ? aValue.localeCompare(bValue) : bValue.localeCompare(aValue);
                } else {
                    return ascending ? aValue - bValue : bValue - aValue;
                }
            });
        }


        function formatStringUpper(str) {
            return str
                .replace(/^./, (match) => match.toUpperCase())
                .replace(/([a-z])([A-Z])/g, '$1 $2')
                .replace(/(_|\s)([a-z])/g, (match, p1, p2) => p1 + p2.toUpperCase())
                .replace(/_/g, ' ');
        }

        function setSortForFetch(value) {
            const element = document.getElementById("sortKeys");
            const sortByTitleId = document.getElementById('sortByTitleId');
            sortByTitleId.innerText = formatStringUpper(value);
            if (element) {
                element.value = value;
                const event = new CustomEvent("formChange", {
                    detail: {
                        name: 'sortKeys',
                        value: value,
                    }
                });
                window.dispatchEvent(event);
            }
        }

        function changeSortType() {
            let sortTypeElement = document.getElementById("sortType");
            let sortIconList = document.querySelector(".sort-icon-list");
            let currentSortType = sortTypeElement?.value;
            if (!sortTypeElement) return;
            sortIconList?.querySelectorAll("svg").forEach((element)=>{
                element.classList.add("d-none");
            })

            if (currentSortType === "") {
                sortTypeElement.value = "asc";
                sortIconList.querySelector(`[data-value="asc-icon"]`).classList.remove("d-none");
            }

            if (currentSortType === "asc") {
                sortTypeElement.value = "desc";
                sortIconList.querySelector(`[data-value="desc-icon"]`).classList.remove("d-none");
            }

            if (currentSortType === "desc") {
                sortTypeElement.value = "";
            }

            fetchTeams();
        }

        // Event Listeners

        window.addEventListener('formChange',
            debounce((event) => {
                changeFilterSortUI(event);
                fetchTeams();
            }, 300)
        );

        newTeamsForm.addEventListener('change',
            debounce((event) => {
                changeFilterSortUI(event);
                fetchTeams();
            }, 300)
        );

        function changeFilterSortUI(event) {
            let target = event.target;
            if (event.detail) {
                target = event.detail;
            }

            name = target.name;
            value = target.value;

            if (name == "search") {
                return;
            }

            if (name ===  "sortKeys") {
                let sortTypeElement = document.getElementById("sortType");
                let sortIconList = document.querySelector(".sort-icon-list");
                let currentSortType = sortTypeElement?.value;
                if (!sortTypeElement) return;
                sortIconList?.querySelectorAll("svg").forEach((element)=>{
                    element.classList.add("d-none");
                })

                sortTypeElement.value = "asc";
                sortIconList.querySelector(`[data-value="asc-icon"]`).classList.remove("d-none");
            }

            let formData = new FormData(newTeamsForm);
            let targetElemnetParent = document.querySelector(`small[data-form-parent="${name}"]`);
            let defaultFilter = document.querySelector(`small[data-form-parent="default-filter"]`);

            let isShowDefaults = true;
            for (let newTeamsFormKey of newTeamsFormKeys) {
                let elementValue = formData.getAll(newTeamsFormKey);
                if (elementValue != "" || (Array.isArray(elementValue) && elementValue[0])) {
                    isShowDefaults = isShowDefaults && false;
                }
            }

             if (isShowDefaults) {
                defaultFilter.classList.remove('d-none');
            } else {
                defaultFilter.classList.add('d-none');
            }

            targetElemnetParent.innerHTML = '';

            let valuesFormData = formData.getAll(name);
            if (value == "" || (Array.isArray(valuesFormData) && valuesFormData[0] == null)) {
                return;
            }

            targetElemnetHeading = document.createElement('span');
            targetElemnetHeading.classList.add('me-2');
            targetElemnetHeading.innerHTML = formatStringUpper(name);
            targetElemnetParent.append(targetElemnetHeading);        
            for (let formValue of valuesFormData) {
                let targetElemnet = document.createElement('small');
                targetElemnet.classList.add('btn', 'btn-secondary', 'text-light', 
                    'rounded-pill', 'px-2', 'py-0', 'me-1'
                );

                targetElemnet.dataset.type = target.type === "checkbox" ? "checkbox" : target.type;
                targetElemnet.dataset.name = name;
                targetElemnet.dataset.value = formValue;
                targetElemnet.innerHTML = `
                    <span> ${formatStringUpper(formValue)} </span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle ms-2" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                    </svg>
                `;
                
                targetElemnetParent.append(targetElemnet);

                console.log({name, value: formValue, targetElemnet, type: target.type});
                if (targetElemnet.dataset.type === "checkbox") {
                    targetElemnet.onclick = function (event2) {
                        let target2 = event2.currentTarget; 
                        let name2 = target2.dataset.name;
                        let value2 = target2.dataset.value;
                        let checkbox = document.querySelector(`input[type="checkbox"][name="${name2}"][value="${value2}"]`);
                        console.log({checkbox, target2, dataset: target2.dataset});
                        checkbox.checked = false;
                        checkbox.removeAttribute('checked');
                        window.dispatchEvent(new CustomEvent("formChange", {
                            detail: {
                                name: name2,
                                value: formData.getAll(name2),
                                type: "checkbox"
                            }
                        }) );
                    }
                }   else {
                    targetElemnet.onclick = function (event3) {
                        let target3 = event3.currentTarget; 
                        let name3 = target3.dataset.name;
                        let value3 = target3.dataset.value; 
                        console.log({target3, dataset: target3.dataset});

                        let resetButton = document.querySelector(`#${name3}ResetButton`);
                        resetButton.click();
                    }
                }
            }

        }


        function sortTeams(arr, sortKey, sortOrder) {
            if (sortKey === "" || sortKey === null || sortOrder === "" || sortOrder === null) {
            const sortByTitleId = document.getElementById('sortByTitleId');
                sortByTitleId.innerText = "Sort By";
                return teamListServerValue;  
            }
            
            let arr2 = [...arr];
            console.log({sortKey, sortOrder});

            let sortTypeToKeyMap = {
                "created_at" : "created_at",
                "region" : "country",
                "status": "status",
                "name": "teamName",
                "recent": "id",
                "membersCount" : "membersCount"  
            };

            arr2 = sortByProperty(arr2, sortTypeToKeyMap[sortKey], sortOrder == "asc");
            return arr2;
        }

        async function fetchTeams(event = null) {
            let route;
            let bodyHtml = '';

            let formData = new FormData(newTeamsForm);
            let sortedTeams = sortTeams(teamListServerValue, formData.get("sortKeys"), formData.get("sortType"));
            filteredSortedTeams = [];

            for (let sortedTeam of sortedTeams) {
                let isToBeAdded = true;
                let nameFilter = String(formData.get('search')).toLowerCase().trim();
                let regionFilter = formData.get('region');
                let createdAtFilter = formData.get('created_at');
                let statusListFilter = formData.getAll('status');
                let membersCountFilter = formData.getAll('membersCount');

                
                if (nameFilter != "" && !(
                        String(sortedTeam?.teamName).includes(nameFilter) 
                    )) {
                    isToBeAdded = isToBeAdded && false;
                }

                if (regionFilter != "" && sortedTeam?.country != regionFilter) {
                    isToBeAdded = isToBeAdded && false;
                }

                if (membersCountFilter != "" && sortedTeam?.membersCount < membersCountFilter) {
                    isToBeAdded = isToBeAdded && false;
                }

                let isArrayFilter = statusListFilter && statusListFilter[0] == null;
                for (let statusItemFilter of statusListFilter) {
                    let isItemGood = statusItemFilter === "private" && sortedTeam?.membersCount >= 5
                        || statusItemFilter === "public" && sortedTeam?.membersCount < 5;

                    if (isItemGood) {
                        isArrayFilter = true || isArrayFilter;
                    }
                }

                isToBeAdded = isArrayFilter && isToBeAdded;

                if (createdAtFilter != "" && new Date(sortedTeam?.created_at) < new Date(createdAtFilter)) {
                    isToBeAdded = isToBeAdded && false;
                }

                if (isToBeAdded) {
                    filteredSortedTeams.push(sortedTeam);
                }
            }

            paintScreen(filteredSortedTeams, membersCountServerValue, countServerValue);

        }

        async function fetchCountries() {
            try {
                const data = await storeFetchDataInLocalStorage('/countries');
                if (data?.data) {
                    countries = data.data;
                    const choices2 = document.getElementById('select2-country2');
                    let countriesHtml = "<option value=''>Choose a country</option>";
                    countries.forEach((value) => {
                        countriesHtml += `
                            <option value='${value.id}''>${value.emoji_flag} ${value.name.en}</option>
                        `;
                    });

                    choices2.innerHTML = countriesHtml;
                } else {
                    errorMessage = "Failed to get data!";
                }
            } catch (error) {
                console.error('Error fetching countries:', error);
            }
        }

        function resetInput(name) {
            let formData = new FormData(newTeamsForm);
            let newValue = "";
            if (name == "sortKeys") {
                newValue = [];
            }

            if (name == "membersCount") {
                newValue = "0";
            } 

            document.querySelector(`[name="${name}"]`).value = newValue;
            formData.set(name, newValue);
            const event = new CustomEvent("formChange", {
                detail: {
                    name: name,
                    value: newValue
                }
            });
            window.dispatchEvent(event);
        }

        fetchCountries();

        function paintScreen(teamListServerValue, membersCountServerValue, countServerValue) {
            let html = ``;
            if (countServerValue <= 0) {
                html += `
                    <div class="wrapper mx-auto">
                    <div class="team-section mx-auto">
                        <div class="upload-container">
                            <label for="image-upload" class="upload-label">
                                <img                       
                                    src="/assets/images/animation/empty-exclamation.gif"
                                    width="150"
                                    height="150"
                                >
                            </label>
                        </div>
                        <h3 class="team-name text-center" id="team-name">No teams yet</h3>
                        <br>
                    </div>
                </div>
                `;
            } else {
                for (let team of teamListServerValue) {
                    team['membersCount'] = membersCountServerValue[team?.id] ?? 0;
                    html += `
                        <a style="cursor:pointer;" class="mx-auto" href="/participant/team/${team?.id}/manage">
                            <div class="wrapper">
                                <div class="team-section">
                                    <div class="upload-container text-center">
                                        <div class="circle-container" style="cursor: pointer;">
                                            <img
                                                onerror="this.onerror=null;this.src='/assets/images/404.png';"
                                                id="uploaded-image" class="uploaded-image"
                                                src="${team?.teamBanner ? '/storage' + '/' + team?.teamBanner : '/assets/images/animations/empty-exclamation.gif' }"
                                            >
                                            </label>
                                        </div>
                                        <div>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <h3 class="team-name" id="team-name">${team?.teamName}</h3>
                                        <span> Region: ${team?.country_name ? team?.country_name: '-'} </span>  <br>
                                        <br>
                                        <span> Members:
                                            ${membersCountServerValue[team?.id] ? membersCountServerValue[team?.id] : 0}
                                        </span> <br>
                                        <small class="${team?.creator_id != userIdServerValue && 'd-none'}"><i>Created by you</i></small>
                                        <br>
                                        <span> 
                                            ${membersCountServerValue[team?.id] ? 
                                                (membersCountServerValue[team?.id] < 5 ? 'Status: Public (Apply)' : 'Status: Private')
                                                : '' 
                                            } 
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    `;
                }
            }
           
            filterSortResultsDiv.innerHTML = html;
        }

        paintScreen(teamListServerValue, membersCountServerValue, countServerValue);
    </script>
</body>

</html>
