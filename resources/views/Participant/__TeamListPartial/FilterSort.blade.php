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
                    <button class="ps-0 pe-3 py-1 py-2 button-design-removed" type="button" id="dropdownFilterType"
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
                    <div onclick="event.stopPropagation;"; class="dropdown-menu px-0 py-1"
                        aria-labelledby="dropdownFilterTier">
                        <div class="px-3 py-1">
                            <p class="mb-1">Choose a country of origin</p>
                            {{-- <input id="select2-country2" type="checkbox" name="venue"> --}}
                            <select id="select2-country2" class="form-control" name="region"
                                style="width: 200px !important;">
                                <option value=""> </option>
                            </select>
                            <button type="button" class="my-2 rounded-pill btn btn-sm btn-primary text-light"
                                onclick="
                                resetInput('region');
                            ">
                                Reset </button>
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
                    <div onclick="event.stopPropagation();"; class="dropdown-menu px-0 py-1"
                        aria-labelledby="dropdownFilterTier">
                        @foreach ([['title' => 'Team member', 'value' => 'accepted'], ['title' => 'Pending invite', 'value' => 'pending'], ['title' => 'Rejected invite', 'value' => 'rejected'], ['title' => 'Left team', 'value' => 'left']] as $status)
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
                style="width: 150px; display: inline-block;" data-bs-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
                <span id="sortByTitleId">Sort by:</span>
                <span class="dropbtn-arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="feather feather-chevron-down">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </span>
            </button>
            <div onclick="event.stopPropagation();"; class="dropdown-menu px-3 ms-3"
                aria-labelledby="dropdownSortButton">
                <div class="sort-box d-block min-w-150px hover-bigger ps-3 py-1" onclick="setSortForFetch('recent');">
                    <label class="me-3 cursor-pointer" for="recent">Recent</label>
                </div>
                <div class="sort-box d-block min-w-150px hover-bigger ps-3 py-1"
                    onclick="setSortForFetch('birthDate');">
                    <label class="me-3 cursor-pointer" for="age">Age</label>
                </div>
                <div class="sort-box d-block min-w-150px hover-bigger ps-3 py-1" onclick="setSortForFetch('region');">
                    <label class="me-3 cursor-pointer" for="region">Region</label>
                </div>
                <div class="sort-box d-block min-w-150px hover-bigger ps-3 py-1" onclick="setSortForFetch('name');">
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