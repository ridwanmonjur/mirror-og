<div class="d-flex justify-content-between w-60s align-items-center flex-wrap">
    <div class="icon2 mr-3 d-inline-block"
        onclick="openElementById('close-option'); openElementById('filter-option');  closeElementById('sort-option');">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-filter mt-2"
            viewBox="0 0 16 16">
            <path
                d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5" />
        </svg>
    </div>
    <div id="filter-option" class="d-flex justify-content-start flex-wrap">
        <div class="dropdown mr-3">
            <button class="px-3 py-2 button-design-removed" type="button" id="dropdownFilterTitle"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span>Title </span>
                <span class="dropbtn-arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="feather feather-chevron-down">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </span>
            </button>
            <div
                onclick="stopPropagation(event);";  
                class="dropdown-menu px-0 py-1" aria-labelledby="dropdownFilterTitle"
            >
                @foreach([
                    ['value'=> 'Dota 2'],
                    ['value'=> 'Dota'],
                ] as $gameTitle)
                    <div class="px-3 min-w-150px">
                        <input onchange="setLocalStorageFilter(event);" type="checkbox" name="gameTitle" value="{{$gameTitle['value']}}">
                        <label for="gameTitle">{{$gameTitle['value']}}</label>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="dropdown mr-3">
            <button
                class="px-3 py-1 py-2 button-design-removed" type="button" id="dropdownFilterType"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span>Type </span>
                <span class="dropbtn-arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="feather feather-chevron-down">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </span>
            </button>
            <div
                onclick="stopPropagation(event);"; 
                class="dropdown-menu px-0 py-1 pt-3" aria-labelledby="dropdownFilterType">
                @foreach([
                    ['value'=> 'Tournament'],
                    ['value'=> 'League'],
                ] as $eventType)
                    <div class="px-3 min-w-150px">
                        <input onchange="setLocalStorageFilter(event);" type="checkbox" name="eventType" value="{{$eventType['value']}}">
                        <label for="eventType">{{$eventType['value']}}</label>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="dropdown mr-3">
            <button class="px-3 py-2 button-design-removed" type="button" id="dropdownFilterTier"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span>Tier </span>
                <span class="dropbtn-arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="feather feather-chevron-down">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </span>
            </button>
            <div
                onclick="stopPropagation(event);"; 
                class="dropdown-menu px-0 pt-3" aria-labelledby="dropdownFilterTier"
            >
                @foreach([
                    ['value'=> 'Dolphin'],
                    ['value'=> 'Turtle'],
                    ['value'=> 'Starfish'],
                    ['value'=> 'Mermaid'],
                ] as $eventTier)
                    <div class="px-3 min-w-150px">
                        <input onchange="setLocalStorageFilter(event);" type="checkbox" name="eventTier" value="{{$eventTier['value']}}">
                        <label for="eventTier">{{$eventTier['value']}}</label>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="dropdown mr-3">
            <button class="px-3 py-2 button-design-removed" type="button" id="dropdownFilterTier"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
                onclick="stopPropagation(event);"; 
                class="dropdown-menu px-0" aria-labelledby="dropdownFilterTier"
            >
                <div class="px-3 pt-2 min-w-250px">
                    <input onchange="setLocalStorageFilter(event);" type="checkbox" name="region" value="SEA">
                    <label for="eventTier">South East Asia (SEA)</label>
                </div>
            </div>
        </div>

        <div class="dropdown mr-3">
            <button class="px-3 py-2 button-design-removed" type="button" id="dropdownFilterTier"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span>Date </span>
                <span class="dropbtn-arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="feather feather-chevron-down">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </span>
            </button>
            <div
                onclick="stopPropagation(event);"; 
                class="dropdown-menu px-0" aria-labelledby="dropdownFilterTier"
            >
                <div class="px-3  pt-2 min-w-150px">
                    <input onchange="setLocalStorageFilter(event);" type="checkbox" name="eventTier" value="Dolphin">
                    <label for="eventTier">Dolphin</label>
                </div>
                <div class="px-3 min-w-150px">
                    <input onchange="setLocalStorageFilter(event);" type="checkbox" name="eventTier" value="Turtle">
                    <label for="eventTier">Turtle</label>
                </div>
                <div class="px-3 min-w-150px">
                    <input onchange="setLocalStorageFilter(event);" type="checkbox" name="eventTier"
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
        <div class="dropdown dropdown-click-outside">
            <button 
                class="dropbtn px-3 py-2 mr-3" 
                type="button" id="dropdownSortButton" 
                onclick="setLocalStorageSortType()"
                data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <span id="sortByTitleId">Sort by:</span>
                <span class="dropbtn-arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="feather feather-chevron-down">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </span>
            </button>
            <div
                onclick="stopPropagation(event);"; 
                class="dropdown-menu px-3 ml-3" aria-labelledby="dropdownSortButton"
            >
                <div class="sort-box d-block min-w-150px hover-bigger pl-3" onclick="setLocalStorageSortKey('recent', 'Recent');">
                    <label class="mr-3 cursor-pointer" for="recent">Recent</label>
                    <span class="recentSortIcon sortIcon">
                    </span>
                </div>
                <div class="sort-box d-block min-w-150px hover-bigger pl-3" onclick="setLocalStorageSortKey('aToZ', 'A-Z');">
                    <label class="mr-3 cursor-pointer" for="aToZ">A-Z</label>
                    <span class="aToZSortIcon sortIcon">
                    </span>
                </div>
                <div class="sort-box d-block min-w-150px hover-bigger pl-3" onclick="setLocalStorageSortKey('startDate', 'Start Date');">
                    <label class="mr-3 cursor-pointer" for="startDate">Start Date</label>
                    <span class="startDateSortIcon sortIcon">
                    </span>
                </div>
                <div class="sort-box d-block min-w-150px hover-bigger pl-3" onclick="setLocalStorageSortKey('prize', 'Prize Pool');">
                    <label class="mr-3 cursor-pointer" for="prize">Prize Pool</label>
                    <span class="prizeSortIcon sortIcon">
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
