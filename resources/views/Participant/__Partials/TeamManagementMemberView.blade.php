@php 
    $counTeamMembers = count($selectTeam->members);
@endphp
<br>

<div id="CurrentMembers">
    <p class="text-center mx-auto mt-2">Team {{ $selectTeam->teamName }} has
        {{ $counTeamMembers }} present team member{{bladePluralPrefix($counTeamMembers)}}. 
        @if (isset($user) && $selectTeam->creator_id == $user->id)
            
                <a 
                    href="{{route('participant.member.manage', ['id'=> $selectTeam->id ])}}"
                    clss="ms-2  small py-1 mx-0"
                > 
                <button role="btn" class="btn text-primary text-primary  cursor-pointer  px-2">
                    Manage Members
                </button>
            </span>

                </a>
        @endif            
    </p>
    <form id="newMembersForm">
    <input type="hidden" name="sortKeys" id="sortKeys" value="">
    <input type="hidden" name="sortType" id="sortType" value="">

    @if($counTeamMembers > 0)
        <div class="tab-size 3 d-flex justify-content-between flex-wrap tab-size mt-3 pt-3">
            
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
                        fill="none" stroke="#2e4b59" stroke-width="2" stroke-linecap="round"
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
                <div class="d-flex  justify-content-start flex-wrap ">
                    <input name="search" style="width: min(90vw, 350px); font-size: 1rem;" class="mb-1 rounded-pill px-4 form-control me-3 cursor-pointer" type="text" placeholder="Search for player name/ email">
                    <button type="button" class="btn btn-primary text-light px-2 border-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="feather mb-1 feather-search"
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
                                <select id="select2-country2" class="form-control form-select" name="region" style="width: 200px !important;">
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

            <div id="filter-search-results" class="d-none pb-2">
                <span class="me-5 " >
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
        <div class="tab-size">
            <div class="member-table responsive " id="member-table-body">
                <input id="membersJson" type="hidden" value="{{json_encode($selectTeam->members)}}">
                <input id="captainJson" type="hidden" value="{{json_encode($captain)}}">
                <div class="team-body">
                        
                </div>
            </div>
        </div>
    @else
        <div class="tab-size">
            No items available
        </div>
    @endif
    </form>
</div>

