<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('assets/css/participant/player_home.css') }}">
    @include('__CommonPartials.HeadIcon')
    <link rel=" stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Driftwood</title>
</head>

<body>
    @include('__CommonPartials.NavbarGoToSearchPage')
    <main>
        <input type="hidden" id="endpoint_route" value="{{ route('landing.view') }}">
        @if(session('token'))
            <input type="hidden" id="session_token" value="{{ session('token') }}">
        @endif
        <section class="hero">
            <img src="{{ asset('/assets/images/homepage new header.png') }}" alt="">
        </section>

        <div class="text__middle">
            <p class="head">What's happening?</p>
        </div>

        <section class="featured-events  scrolling-pagination">
            @include('__CommonPartials.LandingPageScroll')
        </section>

        <div class="no-more-data d-none"></div>
        <br><br><br>
    </main>
    

    <script src="{{ asset('/assets/js/jsUtils.js') }}"></script>
    <script src="{{ asset('/assets/js/participant/Home.js') }}"></script>
</body>

</html>
