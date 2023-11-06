@include('Organizer.Layout.ManageEventHeadTag')

<body>
    @include('CommonLayout.Navbar')

    <main>
        <br class="d-none-at-desktop">
        <div class="">
            <header class="flexbox-welcome">
                <u>
                    <h3>
                        Manage your events
                    </h3>
                </u>
                <input type="submit" value="Create Event" onclick="goToCreateScreen();">


            </header>
            <div class="flexbox-filter">
                <p>
                    All
                </p>
                <p>
                    Live
                </p>
                <p>
                    Scheduled
                </p>
                <p>
                    Drafts
                </p>
                <p>
                    Ended
                </p>
            </div>
            <br>
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-filter">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                </svg>
                <span> Filter </span>
                &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.2 7.8l-7.7 7.7-4-4-5.7 5.7" />
                    <path d="M15 7h6v6" />
                </svg>
                <span>
                    Sort
                </span>
            </div>
            <br>
            <div class="search-bar">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search search-bar2-adjust">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" name="search" id="search" placeholder="Search using title, description, or keywords">
            </div>
        </div>
        <br><br>
        <div class="scrolling-pagination grid-container">
            @include("Organizer.ManageEventScroll")
        </div>
        <div class="no-more-data d-none" style="margin-top: 150px;"></div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="{{ asset('/assets/js/pagination/loadMore.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

        <script>
            function goToCreateScreen() {
                let url = "{{ route('event.create') }}";
                window.location.href = url;
            }
        </script>

        <script>
            var ENDPOINT = "{{ route('event.index') }}";
            var page = 1;
            window.addEventListener(
                "scroll",
                throttle((e) => {
                    var windowHeight = window.innerHeight;
                    var documentHeight = document.documentElement.scrollHeight;
                    var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                    if (scrollTop + windowHeight >= documentHeight-250) {
                        page++;
                        infinteLoadMore(page);
                    }
                }, 300)
            );
        </script>
    </main>


    <!-- <script src="script.js"></script> -->