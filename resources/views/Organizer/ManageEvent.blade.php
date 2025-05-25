<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events</title>
    @include('includes.HeadIcon')
    <link rel="stylesheet" href="{{ asset('/assets/css/organizer/manageEvent.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js', 'resources/js/custom/share.js' ])   
    <link href="https://cdn.jsdelivr.net/npm/litepicker@2.0/dist/css/litepicker.min.css" rel="stylesheet">
</head>

<body>
    @include('googletagmanager::body')
    @include('includes.Navbar.NavbarGoToSearchPage')

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
            @include('includes.__ManageEvent.ManageEventFilterSort', [
                'eventCategoryList' => $eventCategoryList, 
                'eventTierList' => $eventTierList, 
                'eventTypeList' => $eventTypeList 
            ])
            </div>
            <div class="d-none py-2" id="insertFilterTags">
            </div>
            <br>
            <div class="py-0 input-group input-group" style="width: min(90vw, 500px) !important;">
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

            <div class="scrolling-pagination featured-events">
                @include('includes.__ManageEvent.ManageEventScroll')
            </div>

            <div class="no-more-data d-none mb-3" style="margin-top: 50px;" ></div>
            
            <div id="app-data"
                data-endpoint="{{ route('event.search.view') }}"
                data-user-id="{{ $user->id }}"
                data-event-index-url="{{ route('event.index') }}"
                data-event-create-url="{{ route('event.create') }}">
            </div>

            <script src="https://cdn.jsdelivr.net/npm/litepicker@2.0/dist/litepicker.min.js"></script>
            <script src="{{ asset('/assets/js/organizer/ManageEvent.js') }}"></script>
        </div>
    </main>
</body>
