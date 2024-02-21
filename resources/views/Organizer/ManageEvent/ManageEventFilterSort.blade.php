<div class="d-flex justify-content-between w-70s align-items-center flex-wrap">
    <div class="icon2 me-3 d-inline-block"
        onclick="openElementById('close-option'); openElementById('filter-option');  closeElementById('sort-option');">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-filter mt-2"
            viewBox="0 0 16 16">
            <path
                d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5" />
        </svg>
    </div>
    <div id="filter-option" class="d-flex justify-content-start flex-wrap">
        <div class="dropdown me-3">
            <button class="px-3 py-2 button-design-removed" type="button" id="dropdownFilterTitle"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
                @foreach($eventCategoryList as $eventCategoryItem)
                    <div class="px-3 min-w-150px py-1">
                        <input onchange="setFilterForFetch(event, '{{$eventCategoryItem['gameTitle']}}');" type="checkbox" name="gameTitle" value="{{$eventCategoryItem['id']}}">
                        <label for="gameTitle">{{$eventCategoryItem['gameTitle']}}</label>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="dropdown me-3">
            <button
                class="px-3 py-1 py-2 button-design-removed" type="button" id="dropdownFilterType"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
                @foreach($eventTypeList as $eventType)
                    <div class="px-3 min-w-150px py-1">
                        <input onchange="setFilterForFetch(event, '{{$eventType['eventType']}}');" type="checkbox" name="eventType" value="{{$eventType['id']}}">
                        <label for="eventType">{{$eventType['eventType']}}</label>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="dropdown me-3">
            <button class="px-3 py-2 button-design-removed" type="button" id="dropdownFilterTier"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
                @foreach($eventTierList as $eventTier)
                    <div class="px-3 min-w-150px py-1">
                        <input onchange="setFilterForFetch(event, '{{$eventTier['eventTier']}}');" type="checkbox" name="eventTier" value="{{$eventTier['id']}}">
                        <label for="eventTier">{{$eventTier['eventTier']}}</label>
                    </div>
                @endforeach
                
            </div>
        </div>

        <div class="dropdown me-3">
            <button class="px-3 py-2 button-design-removed" type="button" id="dropdownFilterTier"
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
                onclick="stopPropagation(event);"; 
                class="dropdown-menu px-0" aria-labelledby="dropdownFilterTier"
            >
                <div class="px-3 py-2 min-w-250px">
                    <input onchange="setFilterForFetch(event, 'SEA');" type="checkbox" name="venue" value="SEA">
                    <label for="eventTier">South East Asia (SEA)</label>
                </div>
            </div>
        </div>

        <div class="dropdown me-3">
            <button class="px-3 py-2 button-design-removed" type="button" id="dropdownFilterTier"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
                @foreach([
                    ['title' => 'Today', 'value' => 'today'],
                    ['title' => 'Yesterday', 'value' => 'yesterday'],
                    ['title' => 'This week', 'value' => 'this-week'],
                    ['title' => 'This month', 'value' => 'this-month'],
                    ['title' => 'This year', 'value' => 'this-year'],
                ] as $eventType)
                    <div class="px-3 py-1" style="width: 200px;">
                        <input onchange="setFilterForFetch(event, '{{$eventType['title']}}'); " type="checkbox" name="date" value="{{$eventType['value']}}">
                        <label for="eventType">{{$eventType['title']}}</label>
                    </div>
                @endforeach
                <div class="px-3 py-1 form-row d-none">
                    <div class="form-group">
                    <label for="inputEmail4">Start Date</label>
                    <input type="date" class="form-control" id="inputEmail4" placeholder="Email">
                    </div>
                    <div class="form-group">
                    <label for="inputPassword4">End date</label>
                    <input type="date" class="form-control" id="inputPassword4" placeholder="Password">
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="d-flex justify-content-between">
        <div class="icon2 d-inline me-2 mt-2" onclick="toggleDropdown('dropdownSortButton')" id="sortMenuButton">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                class="bi bi-sort-down-alt" viewBox="0 0 16 16">
                <path
                    d="M3.5 3.5a.5.5 0 0 0-1 0v8.793l-1.146-1.147a.5.5 0 0 0-.708.708l2 1.999.007.007a.497.497 0 0 0 .7-.006l2-2a.5.5 0 0 0-.707-.708L3.5 12.293zm4 .5a.5.5 0 0 1 0-1h1a.5.5 0 0 1 0 1zm0 3a.5.5 0 0 1 0-1h3a.5.5 0 0 1 0 1zm0 3a.5.5 0 0 1 0-1h5a.5.5 0 0 1 0 1zM7 12.5a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 0-1h-7a.5.5 0 0 0-.5.5" />
            </svg>
        </div>
        <div class="dropdown dropdown-click-outside">
            <button 
                class="dropbtn py-2 me-3" 
                type="button" id="dropdownSortButton" 
                onclick="setFetchSortType()"
                style="width: 150px; display: inline-block;"
                data-bs-toggle="dropdown"
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
                class="dropdown-menu px-3 ms-3" aria-labelledby="dropdownSortButton"
            >
                <div class="sort-box d-block min-w-150px hover-bigger ps-3" onclick="setSortForFetch('created_at', 'Recent');">
                    <label class="me-3 cursor-pointer" for="recent">Recent</label>
                    <span class="recentSortIcon sortIcon">
                    </span>
                </div>
                <div class="sort-box d-block min-w-150px hover-bigger ps-3" onclick="setSortForFetch('eventName', 'A-Z');">
                    <label class="me-3 cursor-pointer" for="aToZ">A-Z</label>
                    <span class="aToZSortIcon sortIcon">
                    </span>
                </div>
                <div class="sort-box d-block min-w-150px hover-bigger ps-3" onclick="setSortForFetch('startDate', 'Start Date');">
                    <label class="me-3 cursor-pointer" for="startDate">Start Date</label>
                    <span class="startDateSortIcon sortIcon">
                    </span>
                </div>
                <div class="sort-box d-block min-w-150px hover-bigger ps-3" onclick="setSortForFetch('prize', 'Prize Pool');">
                    <label class="me-3 cursor-pointer" for="prize">Prize Pool</label>
                    <span class="prizeSortIcon sortIcon">
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
