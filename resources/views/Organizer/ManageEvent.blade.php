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

            <div class="scrolling-pagination grid-container">
                @include('Organizer.__ManageEventPartials.ManageEventScroll')
            </div>

            <div class="no-more-data d-none mb-3" style="margin-top: 50px;" ></div>

            <script src="{{ asset('/assets/js/jsUtils.js') }}"></script>
            <script>
                function cancelEvent(event) {
                    let svgElement = event.target.closest('svg');
                    if (!svgElement) return;
                    let eventUrl = svgElement.dataset.url;
                    console.log({eventUrl});
                    console.log({eventUrl});
                    console.log({eventUrl});

                    Swal.fire({
                        title: "Are you sure?",
                        text: "You won't be able to revert this!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#43A4D7",
                        cancelButtonColor: "#d33",
                        cancelButtonText: "Cancel Event",
                        confirmButtonText: "Oops, no..."
                    })
                    .then((result) => {
                        if (result.isConfirmed) {
                            fetch(eventUrl, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json',
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire(
                                        'Cancelled!',
                                        'Event has been cancelled.',
                                        'success'
                                    );
                                    location.reload();
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        'Failed to cancel the event.',
                                        'error'
                                    );
                                }
                            })
                            .catch((error) => {
                                Swal.fire(
                                    'Error!',
                                    'Something went wrong!',
                                    'error'
                                );
                            });
                        }
                    });
                }

            </script>
            @include('Organizer.__ManageEventPartials.ManageEventScripts')
        </div>
    </main>
</body>
