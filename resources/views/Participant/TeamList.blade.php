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
                    <div id="filter-option" class="mx-0 px-0 mb-2 ms-5 d-inline-block">
                        <div class="d-flex justify-content-start">
                            <div class="dropdown me-3">
                                <button class="ps-0 pe-3 py-1 py-2 button-design-removed" type="button"
                                    id="dropdownFilterType" data-bs-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    <span>Age </span>
                                    <span class="dropbtn-arrow">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="feather feather-chevron-down">
                                            <polyline points="6 9 12 15 18 9"></polyline>
                                        </svg>
                                    </span>
                                </button>
                                <div onclick="event.stopPropagation();"; class="dropdown-menu px-3"
                                    aria-labelledby="dropdownFilterTier">
                                    <p class="mb-1">Choose a date of birth to filter age</p>
                                    <input type="date" class="form-control" name="birthDate">
                                    <button type="button" class="my-2 rounded-pill btn btn-sm btn-primary text-light"
                                        onclick="
                                        resetInput('birthDate');
                                    ">
                                        Reset
                                    </button>
                                </div>
                            </div>

                            <div class="dropdown me-3">
                                <button class="ps-0 pe-3 py-2 button-design-removed" type="button"
                                    id="dropdownFilterTier" data-bs-toggle="dropdown" aria-haspopup="true"
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
                                    aria-labelledby="dropdownFilterTier">
                                    <div class="px-3 py-1">
                                        <p class="mb-1">Choose a country of origin</p>
                                        {{-- <input id="select2-country2" type="checkbox" name="venue"> --}}
                                        <select id="select2-country2" class="form-control" name="region"
                                            style="width: 200px !important;">
                                            <option value=""> </option>
                                        </select>
                                        <button type="button"
                                            class="my-2 rounded-pill btn btn-sm btn-primary text-light"
                                            onclick="
                                                resetInput('region');
                                            ">
                                            Reset </button>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown me-3">
                                <button class="ps-0 pe-3 py-2 button-design-removed" type="button"
                                    id="dropdownFilterTier" data-bs-toggle="dropdown" aria-haspopup="true"
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
                                <div onclick="event.stopPropagation();"; class="dropdown-menu px-0 py-1"
                                    aria-labelledby="dropdownFilterTier">
                                    @foreach ([['title' => 'Team team', 'value' => 'accepted'], ['title' => 'Pending invite', 'value' => 'pending'], ['title' => 'Rejected invite', 'value' => 'rejected'], ['title' => 'Left team', 'value' => 'left']] as $status)
                                        <div class="px-3 py-1" style="width: 200px;">
                                            <input type="checkbox" name="status" value="{{ $status['value'] }}">
                                            <label for="status">{{ $status['title'] }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="sort-option" class="mx-0 px-0 mb-3 d-inline-block">
                    <div class="ddropdown dropdown-click-outside d-inline-block">
                        <button class="dropbtn py-1 px-2 me-3" type="button" id="dropdownSortButton"
                            style="width: 150px; display: inline-block;" data-bs-toggle="dropdown"
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
                        <div onclick="event.stopPropagation();"; class="dropdown-menu px-3 ms-3"
                            aria-labelledby="dropdownSortButton">
                            <div class="sort-box d-block min-w-150px hover-bigger ps-3 py-1"
                                onclick="setSortForFetch('recent');">
                                <label class="me-3 cursor-pointer" for="recent">Recent</label>
                            </div>
                            <div class="sort-box d-block min-w-150px hover-bigger ps-3 py-1"
                                onclick="setSortForFetch('birthDate');">
                                <label class="me-3 cursor-pointer" for="age">Age</label>
                            </div>
                            <div class="sort-box d-block min-w-150px hover-bigger ps-3 py-1"
                                onclick="setSortForFetch('region');">
                                <label class="me-3 cursor-pointer" for="region">Region</label>
                            </div>
                            <div class="sort-box d-block min-w-150px hover-bigger ps-3 py-1"
                                onclick="setSortForFetch('name');">
                                <label class="me-3 cursor-pointer" for="name">Name</label>
                            </div>
                            <div class="d-block min-w-150px hover-bigger ps-3 py-1" onclick="resetInput('sortKeys');">
                                <button type="button" class="rounded-pill btn btn-sm btn-primary text-light">
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
                        <small data-form-parent="birthDate" class="me-2">
                        </small>
                        <small data-form-parent="region" class="me-2">
                        </small>
                        <small data-form-parent="status" class="me-2">
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

    <script src="{{ asset('/assets/js/fetch/fetch.js') }}"></script>
    <script>
        function goToScreen() {
            window.location.href = "{{ route('participant.request.view') }}";
        }

        let newTeamsForm = document.getElementById('newTeamsForm');
        let filteredSortedTeams = [];
        let newTeamsFormKeys = ['sortKeys', 'birthDate', 'region', 'status'];
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

        function setSortForFetch(value) {
            const element = document.getElementById("sortKeys");

            if (element) {
                element.value = value;
                const event = new CustomEvent("formChange", {
                    detail: {
                        name: 'sortKeys',
                        value: value,
                    }
                });
                window.dispatchEvent(event);
                fetchTeams();
            }
        }


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

            if (name === "search") {
                return;
            }

            let formData = new FormData(newTeamsForm);
            let targetElemnetParent = document.querySelector(`small[data-form-parent="${name}"]`);
            let defaultFilter = document.querySelector(`small[data-form-parent="default-filter"]`);

            let isShowDefaults = true;
            for (let newTeamsFormKey of newTeamsFormKeys) {
                let elementValue = formData.getAll(newTeamsFormKey);
                if (elementValue !== "" || (Array.isArray(elementValue) && elementValue[0])) {
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
            if (value === "" || (Array.isArray(valuesFormData) && valuesFormData[0] === null)) {
                return;
            }

            targetElemnetHeading = document.createElement('small');
            targetElemnetHeading.classList.add('me-2');
            targetElemnetHeading.innerHTML = String(name)?.toUpperCase();
            targetElemnetParent.append(targetElemnetHeading);
            for (let formValue of valuesFormData) {
                targetElemnet = document.createElement('small');
                targetElemnet.classList.add('btn', 'btn-secondary', 'text-light',
                    'rounded-pill', 'px-2', 'py-0', 'me-1'
                );
                targetElemnet.innerHTML = formValue;
                targetElemnetParent.append(targetElemnet);
            }

        }

        function sortTeams(teamsJson) {
            return teamsJson;
        }

        async function fetchTeams(event = null) {
            let route;
            let bodyHtml = '';

            let formData = new FormData(newTeamsForm);
            let sortedTeams = sortTeams(teamListServerValue);
            filteredSortedTeams = [];

            for (let sortedTeam of sortedTeams) {
                let isToBeAdded = true;
                let nameFilter = String(formData.get('search')).toLowerCase().trim();
                let regionFilter = formData.get('region');
                let ageFilter = formData.get('birthDate');

                let statusListFilter = formData.getAll('status') ?? null;
                if (nameFilter !== "" && !(
                        String(sortedTeam?.teamName).includes(nameFilter) 
                    )) {
                    isToBeAdded = isToBeAdded && false;
                }

                if (regionFilter !== "" && sortedTeam?.country !== regionFilter) {
                    isToBeAdded = isToBeAdded && false;
                }

                let isArrayFilter = statusListFilter && statusListFilter[0] === null;
                for (let statusItemFilter of statusListFilter) {
                    if (statusItemFilter === sortedTeam?.status) isArrayFilter = true || isArrayFilter;
                }
                isToBeAdded = isArrayFilter && isToBeAdded;

                if (ageFilter !== "" && new Date(sortedTeam?.user?.participant?.birthday) < new Date(ageFilter)) {
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
            document.querySelector(`[name="${name}"]`).value = '';
            let formData = new FormData(newTeamsForm);
            let newValue = name === "sortKeys" ? [] : "";
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
                                        <span> Teams:
                                            ${membersCountServerValue[team?.id] ? membersCountServerValue[team?.id] : 0}
                                        </span> <br>
                                        <small class="${team?.creator_id !== userIdServerValue && 'd-none'}"><i>Created by you</i></small>
                                        <br>
                                        <span> 
                                            ${membersCountServerValue[team?.id] ? 
                                                (membersCountServerValue[team?.id] > 5 ? 'Status: Public (Apply)' : 'Status: Private')
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
