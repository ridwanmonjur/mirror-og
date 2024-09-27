@php 
    use Carbon\Carbon;
    $counTeamMembers = count($selectTeam->members);
@endphp
<br>

<div id="CurrentMembers">
    <p class="text-center mx-auto mt-2">Team {{ $selectTeam->teamName }} has
        {{ $counTeamMembers }} accepted member{{bladePluralPrefix($counTeamMembers)}} &nbsp;&nbsp;
        @if (isset($user) && $selectTeam->creator_id == $user->id)
            <button class="oceans-gaming-default-button oceans-gaming-default-button-link" 
                onclick="window.location.href='{{route('participant.member.manage', ['id'=> $selectTeam->id ])}}'">
                Manage Members
            </button>
        @endif            
    </p>
    <form id="newMembersForm">
    <input type="hidden" name="sortKeys" id="sortKeys" value="">
    <input type="hidden" name="sortType" id="sortType" value="">

    @if($counTeamMembers > 0)
        <div class="tab-size d-flex justify-content-between flex-wrap tab-size mt-3 pt-3">
            
            <div class="mb-2">
               <span class="cursor-pointer me-4" onclick="
                        document.getElementById('filter-option').classList.remove('d-none'); 
                        document.getElementById('sort-option').classList.add('d-none');
                        document.getElementById('filter-search-results').classList.remove('d-none');
                    ">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="feather feather-filter">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3">
                        </polygon>
                    </svg>
                    <span> Filter </span>
                </span>
                <span class="cursor-pointer" onclick="
                        document.getElementById('filter-option').classList.add('d-none'); 
                        document.getElementById('sort-option').classList.remove('d-none');
                        document.getElementById('filter-search-results').classList.remove('d-none');
                    ">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M20.2 7.8l-7.7 7.7-4-4-5.7 5.7" />
                        <path d="M15 7h6v6" />
                    </svg>
                    <span>
                        Sort
                    </span>
                </span>
            </div>
            <div class="mb-2">
                <div class="d-flex justify-content-end">
                    <input name="search" style="width: min(90vw, 350px); font-size: 1rem;" class="rounded-pill px-4 form-control me-3 cursor-pointer" type="text" placeholder="Search for player name/ email">
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
            </div>
        </div>
        <div class="tab-size px-0">
            <div id="filter-option" class="mx-0 px-0 d-none mb-2">
                <div class="d-flex justify-content-start">
                    <div class="dropdown me-3">
                        <button
                            class="ps-0 pe-3 py-1 py-2 button-design-removed" type="button" id="dropdownFilterType"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span>Age </span>
                            <span class="dropbtn-arrow">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="feather feather-chevron-down">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </span>
                        </button>
                        <div
                            onclick="event.stopPropagation();" 
                            class="dropdown-menu px-3" aria-labelledby="dropdownFilterTier"
                        >
                            <p class="mb-1">Choose a date of birth to filter age</p>
                            <input  type="date" class="form-control" name="birthDate">
                            <button id="birthDateResetButton" type="button" class="my-2 rounded-pill btn btn-sm btn-primary text-light" onclick="
                                resetInput('birthDate');
                            "> Reset </button>
                        </div>
                    </div>

                    <div class="dropdown me-3">
                        <button class="ps-0 pe-3 py-2 button-design-removed" type="button" id="dropdownFilterTier"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span>Region </span>
                            <span class="dropbtn-arrow">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="feather feather-chevron-down">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </span>
                        </button>
                        <div
                            onclick="event.stopPropagation;"; 
                            class="dropdown-menu px-0 py-1" aria-labelledby="dropdownFilterTier"
                        >
                            <div class="px-3 py-1">
                                <p class="mb-1">Choose a country of origin</p>
                                {{-- <input id="select2-country2" type="checkbox" name="venue"> --}}
                                <select id="select2-country2" class="form-control" name="region" style="width: 200px !important;">
                                    <option value=""> </option>
                                </select>
                                 <button id="regionResetButton" type="button" class="my-2 rounded-pill btn btn-sm btn-primary text-light" onclick="
                                    resetInput('region');
                                "> Reset </button>
                            </div>
                        </div>
                    </div>
                    <div class="dropdown me-3">
                        <button class="ps-0 pe-3 py-2 button-design-removed" type="button" id="dropdownFilterTier"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span>Status</span>
                            <span class="dropbtn-arrow">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="feather feather-chevron-down">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </span>
                        </button>
                        <div
                            onclick="event.stopPropagation();" 
                            class="dropdown-menu px-0 py-1" aria-labelledby="dropdownFilterTier"
                        >
                            @foreach([
                                ['title' => 'Team member', 'value' => 'accepted'],
                                ['title' => 'Pending invite', 'value' => 'pending'],
                                ['title' => 'Rejected invite', 'value' => 'rejected'],
                                ['title' => 'Left team', 'value' => 'left'],
                            ] as $status)
                                <div class="px-3 py-1" style="width: 200px;">
                                    <input
                                        type="checkbox" name="status" value="{{$status['value']}}"
                                    >
                                    <label for="status">{{$status['title']}}</label>
                                </div>
                            @endforeach
                        </div>
                    </div> 
                </div>
            </div>
            <div id="sort-option" class="mx-0 px-0 mb-3 d-none">
                <div class="dropdown dropdown-click-outside d-inline-block">
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
                    <button 
                        class="dropbtn py-1 px-2 me-3" 
                        type="button" id="dropdownSortButton" 
                        style="width: 150px; display: inline-block;"
                        data-bs-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <span id="sortByTitleId">Sort by:</span>
                        <span class="dropbtn-arrow">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-chevron-down">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </span>
                    </button>
                    <div
                        onclick="event.stopPropagation();" 
                        class="dropdown-menu px-3 ms-3" aria-labelledby="dropdownSortButton"
                    >
                        <div class="sort-box d-block min-w-150px hover-bigger ps-3 py-1" onclick="setSortForFetch('recent');">
                            <label class="me-3 cursor-pointer" for="recent">Recent</label>
                        </div>
                        <div class="sort-box d-block min-w-150px hover-bigger ps-3 py-1" onclick="setSortForFetch('birthDate');">
                            <label class="me-3 cursor-pointer" for="age">Age</label>
                        </div>
                        <div class="sort-box d-block min-w-150px hover-bigger ps-3 py-1" onclick="setSortForFetch('region');">
                            <label class="me-3 cursor-pointer" for="region">Region</label>
                        </div>
                        <div class="sort-box d-block min-w-150px hover-bigger ps-3 py-1" onclick="setSortForFetch('name');">
                            <label class="me-3 cursor-pointer" for="name">Name</label>
                        </div>
                        <div class="d-block min-w-150px hover-bigger ps-3 py-1" id="sortKeysResetButton" onclick="resetInput('sortKeys');">
                            <button type="button" class="rounded-pill btn btn-sm btn-primary text-light"> 
                                Reset 
                            </button>
                        </div>
                    </div>
                </div>
            </div> 

            <div id="filter-search-results" class="d-none">
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
        </div>
        <table class="member-table responsive " id="member-table responsive -body">
            <input id="membersJson" type="hidden" value="{{json_encode($selectTeam->members)}}">
            <input id="captainJson" type="hidden" value="{{json_encode($captain)}}">
            <tbody>
                    
            </tbody>
        </table>
    @endif
    </form>
</div>

<script>
    let filteredSortedMembers = [];
    let countries = [];
    let membersJsonInput = document.getElementById('membersJson');
    let captainJsonInput = document.getElementById('captainJson');
    let membersJson = JSON.parse(membersJsonInput.value);
    let captainJson = JSON.parse(captainJsonInput.value);

    let newMembersForm = document.getElementById('newMembersForm');
    let newMembersFormKeys = ['sortKeys', 'birthDate', 'region', 'status'];
    let sortKeysInput = document.getElementById("sortKeys");

    function resetInput(name) {
        let formData = new FormData(newMembersForm);
        let newValue = name == "sortKeys" ? [] : ""; 
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

    function setSortForFetch(value) {
        const sortByTitleId = document.getElementById('sortByTitleId');
        sortByTitleId.innerText = formatStringUpper(value);
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
            // fetchMembers();
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

        fetchMembers();
    }
    
    window.addEventListener('formChange',
        debounce((event) => {
            changeFilterSortUI(event);
            fetchMembers();
        }, 300)
    );
    
    newMembersForm.addEventListener('change',
        debounce((event) => {
            changeFilterSortUI(event);
            fetchMembers();
        }, 300)
    );

    function changeFilterSortUI(event) {
        let target = null, type = null, value = null; 
        if (event.detail) {
            target = event.detail; 
        }  else {
            target = event.target; 
        }
        
        name = target.name;
        value = target.value;
        type = target.type;

        
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
            
        let formData = new FormData(newMembersForm);
        let targetElemnetParent = document.querySelector(`small[data-form-parent="${name}"]`);
        let defaultFilter = document.querySelector(`small[data-form-parent="default-filter"]`);
        
        let isShowDefaults = true;
        for (let newMembersFormKey of newMembersFormKeys) {
            let elementValue = formData.getAll(newMembersFormKey);
            if (elementValue != "" || (Array.isArray(elementValue) && elementValue[0] )) {
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
        if (value == "" || (Array.isArray(valuesFormData) && valuesFormData[0] == null )) {
            return;
        }
        
        targetElemnetHeading = document.createElement('span');
        targetElemnetHeading.classList.add('me-2');
        targetElemnetHeading.innerHTML = formatStringUpper(name);
        targetElemnetParent.append(targetElemnetHeading);
        console.log({valuesFormData});
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

    async function fetchCountries () {
        try {
            const data = await storeFetchDataInLocalStorage('/countries');
            if (data?.data) {
                countries = data.data;
                const choices2 = document.getElementById('select2-country2');
                let countriesHtml = "<option value=''>Choose a country</option>";
                countries.forEach((value) => {
                    countriesHtml +=`
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

    function sortMembers(arr, sortKey, sortOrder) {
        if (sortKey === "" || sortKey === null || sortOrder === "" || sortOrder === null) {
           const sortByTitleId = document.getElementById('sortByTitleId');
            sortByTitleId.innerText = "Sort By";
          
          return membersJson;  
        }
        
        let arr2 = [...arr];
        console.log({sortKey, sortOrder});

        let sortTypeToKeyMap = {
            "birthDate" : "user.participant.birthday",
            "region" : "user.participant.region",
            "status": "status",
            "name": "user.name",
            "recent": "id"
        };

        arr2 = sortByProperty(arr2, sortTypeToKeyMap[sortKey], sortOrder == "asc");
        return arr2;
    }

    async function fetchMembers(event = null) {
        let route;
        let bodyHtml = '';

        let formData = new FormData(newMembersForm);
        console.log(membersJson);
        let sortedMembers = sortMembers(membersJson, formData.get("sortKeys"), formData.get("sortType"));
        filteredSortedMembers = [];

        for (let sortedMember of sortedMembers) {
            let isToBeAdded = true;
            let nameFilter = String(formData.get('search')).toLowerCase().trim();
            let regionFilter = formData.get('region');
            let ageFilter = formData.get('birthDate');

            let statusListFilter = formData.getAll('status');
            if (nameFilter != "" && !(
                String(sortedMember?.user?.name).includes(nameFilter) ||
                String(sortedMember?.user?.email).includes(nameFilter)
            )) {
                isToBeAdded = isToBeAdded && false;
            } 

            if (regionFilter != "" && sortedMember?.user?.participant?.region != regionFilter) {
                isToBeAdded = isToBeAdded && false;
            } 

            let isArrayFilter = statusListFilter && statusListFilter[0] == null;
            for (let statusItemFilter of statusListFilter) {
                if (statusItemFilter === sortedMember?.status) isArrayFilter = true || isArrayFilter;
            }
            isToBeAdded = isArrayFilter && isToBeAdded;

            if (ageFilter != "" && new Date(sortedMember?.user?.participant?.birthday) < new Date(ageFilter)) {
                isToBeAdded = isToBeAdded && false;
            } 

           if (isToBeAdded) {
                filteredSortedMembers.push(sortedMember);
           }
        }
        
        for (member of filteredSortedMembers) {
            bodyHtml+=`
                <tr class="st px-3">
                    <td class="colorless-col">
                        <svg 
                            onclick="redirectToProfilePage('${member.user_id}');"
                            class="gear-icon-btn" xmlns="http://www.w3.org/2000/svg" width="20"
                            height="20" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                            <path
                                d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                        </svg>
                    </td>
                    <td class="coloured-cell px-3">
                        <div class="player-info">
                                <div class="${!captainJson || member.id != captainJson?.team_member_id && 'd-none'} player-image">
                                </div>
                                <img 
                                    width="45" height="45" 
                                    src="/storage/${member?.user?.userBanner}"
                                    class="mx-2 random-color-circle object-fit-cover rounded-circle"
                                    onerror="this.error=null;this.src='/assets/images/404.png';"
                                >
                            <span>${member?.user?.name}</span>
                        </div>
                    </td>
                    <td class="coloured-cell px-3">
                        <span>${member?.user?.email}</span>
                    </td>
                    <td class="coloured-cell px-3">
                        <span>${member?.status} ${member?.updated_at ? member.updated_at: ''} </span>
                    </td>
                    <td class="flag-cell coloured-cell px-3 fs-4">
                        <span>${member?.user?.participant?.region_flag}</span>
                    </td>
                </tr>
            `;
        }

        let tbodyElement = document.querySelector('#member-table-body tbody');
        tbodyElement.innerHTML = bodyHtml;  
    };

    fetchMembers();
    fetchCountries();
</script>  
