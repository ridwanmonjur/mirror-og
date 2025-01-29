

<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Teams</title>
        @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('/assets/css/organizer/manageEvent.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamList.css') }}">
    @include('__CommonPartials.HeadIcon')
</head>

<body>
    @include('googletagmanager::body')
    @include('__CommonPartials.NavbarGoToSearchPage')
    <br>
    <main>
        <div class="d-flex justify-content-between mb-2">
            <h5> Your Teams  </h5>
            <a href="{{route('participant.team.create')}}" 
                class="d-inline-flex ms-4 py-2 rounded-pill px-4 text-light btn btn-primary btn-sm"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" 
                    class="mt-1 me-2"
                    viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                </svg>
                Create Team
            </a>
        </div>
        <form id="newTeamsForm">
            <div>
                <input type="hidden" id="request_view_route" value="{{ route('participant.request.view') }}">
                <input type="hidden" id="countServer" value="{{ $count }}">
                <input type="hidden" id="teamListServer" value="{{ json_encode($teamList) }}">
                <input type="hidden" id="membersCountServer" value="{{ json_encode($membersCount) }}">
                <input type="hidden" id="userIdServer" value="{{ $user->id }}">
                <input type="hidden" name="sortKeys" id="sortKeys" value="">
                <input type="hidden" name="sortType" id="sortType" value="">
                <input name="search" style="width: min(90vw, 450px); font-size: 1rem;" class="rounded-pill mb-2 px-4 form-control d-inline-block me-3 cursor-pointer" type="text" placeholder="Search for player name/ email">
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
            <div class="d-inline-flex justify-content-between align-items-center flex-wrap mt-2">
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
                                        <select id="select2-country2" class="form-control form-select" name="region"
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
                                            <input type="checkbox" name="status" class="form-check-input" value="{{ $status['value'] }}">
                                            <label for="status">{{ $status['title'] }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="dropdown me-3" >
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
                                <div style="min-width: 250px;" onclick="event.stopPropagation();" class="dropdown-menu px-3 py-1"
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


                            <div class="dropdown me-3">
                                <button class="ps-0 pe-3 py-1 py-2 button-design-removed" type="button"
                                    id="dropdownFilterType" data-bs-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    <span>Date Joined </span>
                                    <span class="dropbtn-arrow">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="feather feather-chevron-down">
                                            <polyline points="6 9 12 15 18 9"></polyline>
                                        </svg>
                                    </span>
                                </button>
                                <div style="min-width: 250px;" onclick="event.stopPropagation();" class="dropdown-menu px-3"
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
                             <div class="sort-box d-block min-w-150px hover-bigger ps-3 py-1"
                                onclick="setSortForFetch('created_at');">
                                <label class="me-3 cursor-pointer" for="created_at">Date Joined</label>
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

            <div id="filter-search-results" class="pb-2">
                <span class="me-5 " >
                    <small class="me-4"></small>
                    <span class="">
                        <small data-form-parent="default-filter" class="me-2">
                            <small class="btn btn-secondary text-light d-none rounded-pill px-2 py-0">
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
    <script src="{{ asset('/assets/js/participant/TeamList.js') }}"></script>
</body>

</html>
