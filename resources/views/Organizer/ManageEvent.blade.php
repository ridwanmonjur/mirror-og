@include('Organizer.Layout.ManageEventHeadTag')

<body>
    @include('CommonLayout.NavbarGoToSearchPage')

    <main>
        <br class="d-none-at-desktop">
        <div class="">
            <header class="flexbox-welcome">
                <u>
                    <h3>
                        Manage your events
                    </h3>
                </u>
                <button class="oceans-gaming-default-button" value="Create Event" onclick="goToCreateScreen();">
                    Create Event
                </button>


            </header>
            <div class="flexbox-filter">
                <p class="status-ALL">
                    <a href="{{ route('event.index', ['status' => 'ALL', 'page' => 1]) }}">All</a>
                </p>
                <p class="status-LIVE">
                    <a href="{{ route('event.index', ['status' => 'LIVE', 'page' => 1]) }}">Live</a>
                </p>
                <p class="status-SCHEDULED">
                    <a href="{{ route('event.index', ['status' => 'SCHEDULED', 'page' => 1]) }}">Scheduled</a>
                </p>
                <p class="status-DRAFT">
                    <a href="{{ route('event.index', ['status' => 'DRAFT', 'page' => 1]) }}">Drafts</a>
                </p>
                <p class="status-ENDED">
                    <a href="{{ route('event.index', ['status' => 'ENDED', 'page' => 1]) }}">Ended</a>
                </p>
            </div>
            <br>
            <div class="d-flex justify-content-between">
                <div class="icon2 mr-3 d-inline-block"
                    onclick="openElementById('close-option'); openElementById('filter-option');  closeElementById('sort-option');">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                        class="bi bi-filter" viewBox="0 0 16 16">
                        <path
                            d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5" />
                    </svg>
                </div>
                <div id="filter-option" class="d-none">
                    <form name="filter" onSubmit="onSubmit(event);">
                        <div>
                            <label> Game Title:</label>
                            &emsp;
                            <input onchange="setLocalStorageFilter(event);" type="radio" name="gameTitle"
                                value="Dota 2">
                            <label for="gameTitle">Dota 2</label>
                            &ensp;
                            <input onchange="setLocalStorageFilter(event);" type="radio" name="gameTitle"
                                value="Dota">
                            <label for="gameTitle">Dota</label>
                        </div>
                        <div>
                            <label> Event Type:</label>
                            &emsp;
                            <input onchange="setLocalStorageFilter(event);" type="radio" name="eventType"
                                value="Tournament">
                            <label for="eventTyoe">Tournament</label>
                            &ensp;
                            <input onchange="setLocalStorageFilter(event);" type="radio" name="eventType"
                                value="League">
                            <label for="eventTyoe">League</label>
                        </div>
                        <div>
                            <label> Event Tier: </label>
                            &emsp;&nbsp;&nbsp;
                            <input onchange="setLocalStorageFilter(event);" type="radio" name="eventTier"
                                value="Dolphin">
                            <label for="eventTier">Dolphin</label>
                            &ensp;
                            <input onchange="setLocalStorageFilter(event);" type="radio" name="eventTier"
                                value="Turtle">
                            <label for="eventTier">Turtle</label>
                            &ensp;
                            <input onchange="setLocalStorageFilter(event);" type="radio" name="eventTier"
                                value="Starfish">
                            <label for="eventTier">Starfish</label>
                        </div>
                    </form>
                </div>
                <div class="d-flex justify-content-between">
                    <div 
                        class="icon2 d-inline mr-2 mt-2"
                        onclick="openElementById('close-option'); openElementById('sort-option'); closeElementById('filter-option');"
                        id="sortMenuButton" 
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                            class="bi bi-sort-down-alt" viewBox="0 0 16 16">
                            <path
                                d="M3.5 3.5a.5.5 0 0 0-1 0v8.793l-1.146-1.147a.5.5 0 0 0-.708.708l2 1.999.007.007a.497.497 0 0 0 .7-.006l2-2a.5.5 0 0 0-.707-.708L3.5 12.293zm4 .5a.5.5 0 0 1 0-1h1a.5.5 0 0 1 0 1zm0 3a.5.5 0 0 1 0-1h3a.5.5 0 0 1 0 1zm0 3a.5.5 0 0 1 0-1h5a.5.5 0 0 1 0 1zM7 12.5a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 0-1h-7a.5.5 0 0 0-.5.5" />
                        </svg>
                    </div>
                     <div class="dropdown">
                        <button class="dropbtn" onclick="toggleDropdown()">
                            <span id="selectedTeamLabel">Select Team</span>
                            <span class="dropbtn-arrow">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="feather feather-chevron-down">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </span>
                        </button>
                        <div class="dropdown-content" id="teamList">
                            <div>
                                <div> HI </div>
                            </div>
                        </div>
                </div>
            </div>
                </div>
            </div>
            <br>
            <div class="search-bar">
                    <svg onclick= "handleInputBlur();" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round"
                        class="feather feather-search search-bar2-adjust">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" name="search" id="searchInput"
                        placeholder="Search using title, description, or keywords">
                    <button type="button" onclick="resetUrl();" class="oceans-gaming-default-button d-none"
                        style="background: #8CCD39 !important">
                        Reset
                    </button>
                </div>
            <br><br>
            <div class="modal fade" id="shareModal" tabindex="-1" role="dialog"
                aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Share on social media</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            NEED TO GET UI FROM LEIGH
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="scrolling-pagination grid-container">
                @include('Organizer.ManageEvent.ManageEventScroll')
            </div>
            <svg class="none-sort-icon d-none" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" class="feather feather-refresh-cw">
                <polyline points="23 4 23 10 17 10"></polyline>
                <polyline points="1 20 1 14 7 14"></polyline>
                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
            </svg>
            <svg class="asc-sort-icon d-none" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" class="feather feather-chevrons-up">
                <polyline points="17 11 12 6 7 11"></polyline>
                <polyline points="17 18 12 13 7 18"></polyline>
            </svg>
            <svg class="desc-sort-icon d-none" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" class="feather feather-chevrons-down">
                <polyline points="7 13 12 18 17 13"></polyline>
                <polyline points="7 6 12 11 17 6"></polyline>
            </svg>

            <div class="no-more-data d-none" style="margin-top: 50px;"></div>
            @include('CommonLayout.BootstrapJs')
            <script src="{{ asset('/assets/js/navbar/toggleNavbar.js') }}"></script>
            <script src="{{ asset('/assets/js/pagination/loadMore.js') }}"></script>
            @include('Organizer.ManageEvent.ManageEventScripts')
    </main>
