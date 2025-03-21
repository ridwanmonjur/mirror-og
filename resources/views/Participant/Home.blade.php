<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('assets/css/participant/player_home.css') }}">
    @include('includes.HeadIcon')
    <title>Driftwood</title>
</head>

<body>
    @include('googletagmanager::body')
    @include('includes.__Navbar.NavbarGoToSearchPage')
    <main>
        <input type="hidden" id="endpoint_route" value="{{ route('public.landing.view') }}">
        @if(session('token'))
            <input type="hidden" id="session_token" value="{{ session('token') }}">
        @endif
        <section class="hero d-none d-lg-block">
            <img 
                src="{{ asset('/assets/images/homepage new header.png') }}"
                alt=""
                class="user-select-none"
            >
        </section>

        <div class="text__middle">
            <p class="head">What's happening?</p>
        </div>

        <section class="featured-events  scrolling-pagination">
            @include('includes.Landing')
        </section>

        <div class="no-more-data d-none"></div>
        <br><br><br>
    </main>
    

    
    <script src="{{ asset('/assets/js/participant/Home.js') }}"></script>
</body>

</html>
