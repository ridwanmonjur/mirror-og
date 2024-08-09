@include('Organizer.__Partials.ManageEventHeadTag')


<body>
    @include('__CommonPartials.NavbarGoToSearchPage')

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
            <div class="flexbox-filter flex-wrap">
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
                 <p class="status-PENDING">
                    <a href="{{ route('event.index', ['status' => 'PENDING', 'page' => 1]) }}">Pending</a>
                </p>
                {{-- whereNotIn('status', ['DRAFT', 'PENDING']) --}}
                <p class="status-ENDED">
                    <a href="{{ route('event.index', ['status' => 'ENDED', 'page' => 1]) }}">Ended</a>
                </p>
            </div>
            @include('Organizer.__ManageEventPartials.ManageEventFilterSort', [
                'eventCategoryList' => $eventCategoryList, 
                'eventTierList' => $eventTierList, 
                'eventTypeList' => $eventTypeList 
            ])
            </div>
            <div class="d-none" id="insertFilterTags">
            </div>
            <br>
            <div class="py-4 input-group input-group" style="width: min(90vw, 500px) !important;">
                <div class="input-group-text" style="background: white; outline-width: 1px 1px 0 1px; border-radius: 30px 0 0 30px;">
                    <svg onclick= "handleSearch();" xmlns="http://www.w3.org/2000/svg" width="20"
                        height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round"
                        class="feather feather-search">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </div>
                    
                <input style="border-radius: 0 30px 30px 0;" class="form-control" type="text" name="search" id="searchInput" 
                    placeholder="Search using title, description, or keywords">
              
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
                @include('Organizer.__ManageEventPartials.ManageEventScroll')
            </div>

            <div class="d-none"> 
                <div class="cursor-pointer d-inline me-2 mt-2 asc-sort-icon cursor-pointer" 
                    onclick="toggleDropdown('dropdownSortButton'); setFetchSortType(event);"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                        class="bi bi-sort-up" viewBox="0 0 16 16">
                        <path
                            d="M3.5 3.5a.5.5 0 0 0-1 0v8.793l-1.146-1.147a.5.5 0 0 0-.708.708l2 1.999.007.007a.497.497 0 0 0 .7-.006l2-2a.5.5 0 0 0-.707-.708L3.5 12.293zm4 .5a.5.5 0 0 1 0-1h1a.5.5 0 0 1 0 1zm0 3a.5.5 0 0 1 0-1h3a.5.5 0 0 1 0 1zm0 3a.5.5 0 0 1 0-1h5a.5.5 0 0 1 0 1zM7 12.5a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 0-1h-7a.5.5 0 0 0-.5.5" />
                    </svg>
                </div>
                <div class="cursor-pointer d-inline me-2 mt-2 desc-sort-icon cursor-pointer" 
                    onclick="toggleDropdown('dropdownSortButton'); setFetchSortType(event);"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-sort-down" viewBox="0 0 16 16">
                        <path d="M3.5 12.5a.5.5 0 0 1-1 0V3.707L1.354 4.854a.5.5 0 1 1-.708-.708l2-1.999.007-.007a.5.5 0 0 1 .7.006l2 2a.5.5 0 1 1-.707.708L3.5 3.707zm3.5-9a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5M7.5 6a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1z"/>
                    </svg>
                </div>
                <div class="cursor-pointer d-inline me-2 mt-2 none-sort-icon cursor-pointer" 
                    onclick="toggleDropdown('dropdownSortButton'); setFetchSortType(event);"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-ban" viewBox="0 0 16 16">
                        <path d="M15 8a6.97 6.97 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0"/>
                    </svg>
                </div>
            </div>
            <div class="no-more-data d-none mb-3" style="margin-top: 50px;" ></div>

            
            <script src="{{ asset('/assets/js/navbar/toggleNavbar.js') }}"></script>
            <script src="{{ asset('/assets/js/pagination/loadMore.js') }}"></script>
            @include('Organizer.__ManageEventPartials.ManageEventScripts')
    </main>
