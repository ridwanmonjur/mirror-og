<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organizer Home Page</title>
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/organizer/home.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
    @include('CommonLayout.NavbarGoToSearchPage')
    <main>
        <section class="hero">
            <img src="{{ asset('/assets/images/events/ss.png') }}" alt="">
        </section>

        <div class="text__middle">
            <p class="head">What would you like to do?</p>
        </div>

        <section class="featured-events">
            <!-- EVENTS -->
            <!-- Box 1 -->
            <a href="{{ route('event.create') }}" class="clickable-box" id="imageLink1">
                <div class="event">
                    <div class="event_head_container">
                        Create an Event
                    </div>

                    <div class="frame1">
                        <img src="{{ asset('/assets/images/events/edit.png') }}" width="200px" height="200px"
                            alt="Clickable Image" />
                    </div><br>

                    <div class="caption">
                        Choose from a list of categories and customize your event card to reflect your brand
                    </div>
                </div>
            </a>

            <!-- Box 2 -->
            <a href="{{ route('event.index') }}" class="clickable-box" id="imageLink2">
                <div class="event">
                    <div class="event_head_container">
                        Manage your events
                    </div>

                    <div class="frame1">
                        <img src="{{ asset('/assets/images/events/settings.png') }}" width="200px" height="200px"
                            alt="Clickable Image" />
                    </div><br>

                    <div class="caption">
                        Edit your unpublished events, get updates for your live events, get insights from your past
                        events
                    </div>
                </div>
            </a>

            <!-- Box 3 -->
            <a href="#" class="clickable-box" id="imageLink3">
                <div class="event">
                    <div class="event_head_container">
                        Manage your shop
                    </div>

                    <div class="frame1">
                        <img src="{{ asset('/assets/images/events/shop.png') }}" width="200px" height="200px"
                            alt="Clickable Image" />
                    </div><br>

                    <div class="caption">
                        See what you have listed in the official store view your sales, and get buyer insights for your
                        listings
                    </div>
                </div>
            </a>
            <script src="{{ asset('/assets/js/navbar/toggleNavbar.js') }}"></script>
            <script src="{{ asset('/assets/js/pagination/loadMore.js') }}"></script>
            @include('CommonLayout.BootstrapV5Js')
            <script>
                var ENDPOINT = "{{ route('landing.view') }}";
                var page = 1;
                var search = null;
                window.addEventListener(
                    "scroll",
                    throttle((e) => {
                        var windowHeight = window.innerHeight;
                        var documentHeight = document.documentElement.scrollHeight;
                        var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                        if (scrollTop + windowHeight >= documentHeight - 200) {
                            page++;
                            ENDPOINT = "{{ route('landing.view') }}";
                            console.log({
                                search
                            })
                            if (!search || String(search).trim() == "") {
                                search = null;
                                ENDPOINT += "?page=" + page;
                            } else {
                                ENDPOINT += "?search=" + search + "&page=" + page;
                            }
                            infinteLoadMore(null, ENDPOINT);
                        }
                    }, 300)
                );
            </script>

        </section>
    </main>
</body>

</html>
