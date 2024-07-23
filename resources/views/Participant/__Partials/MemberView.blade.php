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
                    <input style="width: min(90vw, 350px); font-size: 1rem;" class="rounded-pill px-4 form-control me-3 cursor-pointer" type="text" placeholder="Search for player name">
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
                            onclick="event.stopPropagation();"; 
                            class="dropdown-menu px-3" aria-labelledby="dropdownFilterTier"
                        >
                            <p class="mb-1">Choose a date of birth to filter age</p>
                            <input  type="date" class="form-control" name="birthDate">
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
                            onclick="event.stopPropagation();"; 
                            class="dropdown-menu px-0 py-1" aria-labelledby="dropdownFilterTier"
                        >
                            @foreach([
                                ['title' => 'Team member', 'value' => 'member'],
                                ['title' => 'Pending invite', 'value' => 'pending'],
                                ['title' => 'Rejected invite', 'value' => 'rejected'],
                                ['title' => 'Left team', 'value' => 'left'],
                                ['title' => 'No status', 'value' => 'no-status'],
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
                <div class="ddropdown dropdown-click-outside d-inline-block">
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
                        onclick="event.stopPropagation();"; 
                        class="dropdown-menu px-3 ms-3" aria-labelledby="dropdownSortButton"
                    >
                        <div class="sort-box d-block min-w-150px hover-bigger ps-3 py-1" onclick="setSortForFetch('recent');">
                            <label class="me-3 cursor-pointer" for="recent">Recent</label>
                            <span class="recentSortIcon sortIcon">
                            </span>
                        </div>
                        <div class="sort-box d-block min-w-150px hover-bigger ps-3 py-1" onclick="setSortForFetch('age');">
                            <label class="me-3 cursor-pointer" for="age">Age</label>
                            <span class="aToZSortIcon sortIcon">
                            </span>
                        </div>
                        <div class="sort-box d-block min-w-150px hover-bigger ps-3 py-1" onclick="setSortForFetch('region');">
                            <label class="me-3 cursor-pointer" for="region">Region</label>
                            <span class="startDateSortIcon sortIcon">
                            </span>
                        </div>
                        <div class="sort-box d-block min-w-150px hover-bigger ps-3 py-1" onclick="setSortForFetch('name');">
                            <label class="me-3 cursor-pointer" for="prize">Name</label>
                            <span class="prizeSortIcon sortIcon">
                            </span>
                        </div>
                    </div>
                </div>
            </div> 

            <div id="filter-search-results" class="d-none">
                <span class="me-5 cursor-not-allowed" class="">
                    <small class="me-4">Filter: </small>
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
                    </span>
                </span> 
                <span class="me-5 cursor-not-allowed" class="">
                    <small class="me-3">Sort: </small>
                    <span class="">
                        <small data-form-parent="default-sort" class="me-2">
                            <small class="btn btn-secondary text-light rounded-pill px-2 py-0">
                                Default
                            </small>
                        </small>
                        <small data-form-parent="sortKeys">  
                        </small>
                    </span>
                    {{-- <small  id="default-sorts" class="btn btn-primary text-light px-2 py-0">Default</small> --}}
                </span> 
            </div>
        </div>
        <table class="member-table" id="member-table-body">
            <input id="membersJson" type="hidden" value="{{json_encode($selectTeam->members)}}">
            <input id="captainJson" type="hidden" value="{{json_encode($captain)}}">
            <tbody>
                    
            </tbody>
        </table>
    @endif
    </form>
</div>

<script>
    let membersJsonInput = document.getElementById('membersJson');
    let captainJsonInput = document.getElementById('captainJson');
    let membersJson = JSON.parse(membersJsonInput.value);
    let captainJson = JSON.parse(captainJsonInput.value);

    let newMembersForm = document.getElementById('newMembersForm');
    let newMembersFormKeys = ['sortKeys', 'birthDate', 'region', 'status'];
    let sortKeysInput = document.getElementById("sortKeys")
    function setSortForFetch(value) {
        const element = document.getElementById("sortKeys");

        if (element) {
            element.value = value;
            const event = new CustomEvent("sortKeysChange", {
                detail: {
                    name: 'sortKeys',
                    value: value,
                }
            }); 
            window.dispatchEvent(event);
            fetchMembers();
        }
    }

    let countries = [];
    window.addEventListener('sortKeysChange',
        debounce((event) => {
            changeUI(event);
        }, 300)
    );
    
    newMembersForm.addEventListener('change',
        debounce((event) => {
            changeUI(event);
            fetchMembers();
        }, 300)
    );

    function changeUI(event) {
        let target = event.target, 
            name = undefined,
            value = undefined;
        if (event.detail) {
            target = event.detail;
        }    

        name = target.name;
        value = target.value;
        console.log({event, name, value});
        
        console.log("HI");console.log("HI");console.log("HI");console.log("HI");
        if (name != "search") {
            let formData = new FormData(newMembersForm);
            let isAppend = true;
            let targetElemnetParent = document.querySelector(`small[data-form-parent="${name}"]`);

            if (name == 'sortKeys') {
                let defaultSort = document.querySelector(`small[data-form-parent="default-sort"]`);
                defaultSort?.remove();
            } else {
                let defaultFilter = document.querySelector(`small[data-form-parent="default-filter"]`);
                defaultFilter?.remove();
            }

            targetElemnetParent.innerHTML = '';
            targetElemnetHeading = document.createElement('small');
            targetElemnetHeading.classList.add('me-2');
            targetElemnetHeading.innerHTML = String(name)?.toUpperCase();
            targetElemnetParent.append(targetElemnetHeading);
            for (let formValue of formData.getAll(name)) {
                targetElemnet = document.createElement('small');
                targetElemnet.classList.add('btn', 'btn-secondary', 'text-light', 
                    'rounded-pill', 'px-2', 'py-0', 'me-1'
                );
                targetElemnet.innerHTML = formValue;
                targetElemnetParent.append(targetElemnet);
            }
        }
    }

    async function fetchCountries () {
        try {
            const data = await storeFetchDataInLocalStorage('/countries');
            if (data?.data) {
                countries = data.data;
                const choices2 = document.getElementById('select2-country2');
                let countriesHtml = "<option value=''";
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

    async function fetchMembers(event = null) {
        let route;
        let bodyHtml = '', pageHtml = '';
        let teamId = document.getElementById('teamId')?.value;
        if (event?.target && event.target?.dataset?.url) {
                route = event.target.dataset.url;
        } else {
            route = document.getElementById('membersUrl')?.value;
        }
        
        for (member of membersJson) {
            bodyHtml+=`
                <tr class="st px-3">
                    <td class="colorless-col">
                        <svg 
                            onclick="redirectToProfilePage('${member.user_id}'');"
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
