<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('assets/css/participant/player_home.css') }}">
    
    <link rel=" stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Splash</title>
</head>

<body>
    @include('CommonLayout.NavbarGoToSearchPage')
    <main>
        <section class="hero">
            <img src="{{ asset('/assets/images/ss.png') }}" alt="">
        </section>

        <div class="text__middle">
            <p class="head">What's happening?</p>
        </div>

        <section class="featured-events  scrolling-pagination">
            @include('LandingPageScroll')
        </section>

        <div class="no-more-data d-none"></div>
        <br><br><br>
    </main>
    @include('CommonLayout.BootstrapV5Js')

    <script src="{{ asset('/assets/js/pagination/loadMore.js') }}"></script>
    <script src="{{ asset('/assets/js/navbar/toggleNavbar.js') }}"></script>
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
</body>

</html>

@if(session('token'))
    <script>
        let token = "{{session('token')}}";
        console.log({token})
        localStorage.setItem('token', token)
    </script>
@endif
