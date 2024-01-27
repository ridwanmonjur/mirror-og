<div class="d-flex justify-content-between w-50s">
    <div class="icon2 mr-3 d-inline-block"
        onclick="openElementById('close-option'); openElementById('filter-option');  closeElementById('sort-option');">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-filter"
            viewBox="0 0 16 16">
            <path
                d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5" />
        </svg>
    </div>
    <div id="filter-option" class="d-flex justify-content-around">
        <div class="dropdown mr-3">
            <button class="px-3 py-2" type="button" id="dropdownFilterTitle" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <span>Type </span>
                <span class="dropbtn-arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="feather feather-chevron-down">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </span>
            </button>
            <div class="dropdown-menu px-0 py-1" aria-labelledby="dropdownFilterTitle">
                <div class="dropdown-item">
                    <input onchange="setLocalStorageFilter(event);" type="radio" name="gameTitle" value="Dota 2">
                    <label class="mr-3" for="gameTitle">Dota 2</label>
                </div>
                <div class="dropdown-item">
                    <input onchange="setLocalStorageFilter(event);" type="radio" name="gameTitle" value="Dota">
                    <label for="gameTitle">Dota</label>
                </div>
            </div>
        </div>

        <div class="dropdown mr-3">
            <button class="px-0 py-1 py-2" type="button" id="dropdownFilterType" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <span>Type </span>
                <span class="dropbtn-arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="feather feather-chevron-down">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </span>
            </button>
            <div class="dropdown-menu px-0 py-1" aria-labelledby="dropdownFilterType">
                <div class="dropdown-item">
                    <input onchange="setLocalStorageFilter(event);" type="radio" name="eventType" value="Tournament">
                    <label class="mr-3" for="eventTyoe">Tournament</label>
                </div>
                <div class="dropdown-item">
                    <input onchange="setLocalStorageFilter(event);" type="radio" name="eventType" value="League">
                    <label for="eventTyoe">League</label>
                </div>
            </div>
        </div>

        <div class="dropdown mr-3">
            <button class="px-3 py-2" type="button" id="dropdownFilterTier" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
                <span>Tier </span>
                <span class="dropbtn-arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="feather feather-chevron-down">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </span>
            </button>
            <div class="dropdown-menu px-3" aria-labelledby="dropdownFilterTier">
                <div class="dropdown-item">
                    <input onchange="setLocalStorageFilter(event);" type="radio" name="eventTier" value="Dolphin">
                    <label for="eventTier">Dolphin</label>
                </div>
                <div class="dropdown-item">
                    <input onchange="setLocalStorageFilter(event);" type="radio" name="eventTier" value="Turtle">
                    <label for="eventTier">Turtle</label>
                </div>
                <div class="dropdown-item">
                    <input onchange="setLocalStorageFilter(event);" type="radio" name="eventTier"
                        value="Starfish">
                    <label for="eventTier">Starfish</label>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between">
        <div class="icon2 d-inline mr-2 mt-2" onclick="toggleDropdown('dropdownSortButton')" id="sortMenuButton">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                class="bi bi-sort-down-alt" viewBox="0 0 16 16">
                <path
                    d="M3.5 3.5a.5.5 0 0 0-1 0v8.793l-1.146-1.147a.5.5 0 0 0-.708.708l2 1.999.007.007a.497.497 0 0 0 .7-.006l2-2a.5.5 0 0 0-.707-.708L3.5 12.293zm4 .5a.5.5 0 0 1 0-1h1a.5.5 0 0 1 0 1zm0 3a.5.5 0 0 1 0-1h3a.5.5 0 0 1 0 1zm0 3a.5.5 0 0 1 0-1h5a.5.5 0 0 1 0 1zM7 12.5a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 0-1h-7a.5.5 0 0 0-.5.5" />
            </svg>
        </div>
        <div class="dropdown">
            <button class="dropbtn px-3 py-2" type="button" id="dropdownSortButton" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <span>Sort by:</span>
                <span class="dropbtn-arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="feather feather-chevron-down">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </span>
            </button>
            <div class="dropdown-menu px-3" aria-labelledby="dropdownSortButton">
                <div class="sort-box d-block">
                    <input onchange="setLocalStorageSort(event);" type="radio" name="sort" value="startDate">
                    <label class="mr-2" for="startDate">Start Date</label>
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
                </div>
                <div class="sort-box d-block">
                    <input onchange="setLocalStorageSort(event);" type="radio" name="sort" value="endDate">
                    <label class="mr-2" for="endDateSortIcon endIcon">End Date</label>
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
                </div>
            </div>
        </div>
    </div>
</div>
