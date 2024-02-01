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
                <p class="status-ENDED">
                    <a href="{{ route('event.index', ['status' => 'ENDED', 'page' => 1]) }}">Ended</a>
                </p>
            </div>
            @include('Organizer.ManageEvent.ManageEventFilterSort', [
                'eventCategoryList' => $eventCategoryList, 
                'eventTierList' => $eventTierList, 
                'eventTypeList' => $eventTypeList 
            ])
            </div>
            <div class="d-none" id="insertFilterTags">
            </div>
            <br>
            <div class="search-bar">
                <svg onclick= "handleSearch();" xmlns="http://www.w3.org/2000/svg" width="24"
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

            <div class="none-sort-icon d-none cursor-pointer" > 
                <svg  class="asc-sort-icon d-none cursor-pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-sort-alpha-up-alt" viewBox="0 0 16 16">
                    <path d="M12.96 7H9.028v-.691l2.579-3.72v-.054H9.098v-.867h3.785v.691l-2.567 3.72v.054h2.645z"/>
                    <path fill-rule="evenodd" d="M10.082 12.629 9.664 14H8.598l1.789-5.332h1.234L13.402 14h-1.12l-.419-1.371zm1.57-.785L11 9.688h-.047l-.652 2.156z"/>
                    <path d="M4.5 13.5a.5.5 0 0 1-1 0V3.707L2.354 4.854a.5.5 0 1 1-.708-.708l2-1.999.007-.007a.5.5 0 0 1 .7.006l2 2a.5.5 0 1 1-.707.708L4.5 3.707z"/>
                </svg>
                <svg class="desc-sort-icon d-none cursor-pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-sort-alpha-down-alt" viewBox="0 0 16 16">
                    <path d="M12.96 7H9.028v-.691l2.579-3.72v-.054H9.098v-.867h3.785v.691l-2.567 3.72v.054h2.645z"/>
                    <path fill-rule="evenodd" d="M10.082 12.629 9.664 14H8.598l1.789-5.332h1.234L13.402 14h-1.12l-.419-1.371zm1.57-.785L11 9.688h-.047l-.652 2.156z"/>
                    <path d="M4.5 2.5a.5.5 0 0 0-1 0v9.793l-1.146-1.147a.5.5 0 0 0-.708.708l2 1.999.007.007a.497.497 0 0 0 .7-.006l2-2a.5.5 0 0 0-.707-.708L4.5 12.293z"/>
                </svg>
            </div>
            <svg  class="asc-sort-icon d-none cursor-pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-sort-alpha-up-alt" viewBox="0 0 16 16">
                <path d="M12.96 7H9.028v-.691l2.579-3.72v-.054H9.098v-.867h3.785v.691l-2.567 3.72v.054h2.645z"/>
                <path fill-rule="evenodd" d="M10.082 12.629 9.664 14H8.598l1.789-5.332h1.234L13.402 14h-1.12l-.419-1.371zm1.57-.785L11 9.688h-.047l-.652 2.156z"/>
                <path d="M4.5 13.5a.5.5 0 0 1-1 0V3.707L2.354 4.854a.5.5 0 1 1-.708-.708l2-1.999.007-.007a.5.5 0 0 1 .7.006l2 2a.5.5 0 1 1-.707.708L4.5 3.707z"/>
            </svg>
            <svg class="desc-sort-icon d-none cursor-pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-sort-alpha-down-alt" viewBox="0 0 16 16">
                <path d="M12.96 7H9.028v-.691l2.579-3.72v-.054H9.098v-.867h3.785v.691l-2.567 3.72v.054h2.645z"/>
                <path fill-rule="evenodd" d="M10.082 12.629 9.664 14H8.598l1.789-5.332h1.234L13.402 14h-1.12l-.419-1.371zm1.57-.785L11 9.688h-.047l-.652 2.156z"/>
                <path d="M4.5 2.5a.5.5 0 0 0-1 0v9.793l-1.146-1.147a.5.5 0 0 0-.708.708l2 1.999.007.007a.497.497 0 0 0 .7-.006l2-2a.5.5 0 0 0-.707-.708L4.5 12.293z"/>
            </svg>

            <div class="no-more-data d-none" style="margin-top: 50px;"></div>

            @include('CommonLayout.BootstrapV5Js')
            <script src="{{ asset('/assets/js/navbar/toggleNavbar.js') }}"></script>
            <script src="{{ asset('/assets/js/pagination/loadMore.js') }}"></script>
            @include('Organizer.ManageEvent.ManageEventScripts')
    </main>
