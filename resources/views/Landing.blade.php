<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('__CommonPartials.HeadIcon')
    <link rel="stylesheet" href="{{ asset('assets/css/participant/player_home.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])    
    <title>Driftwood</title>
</head>

<body>
    @include('googletagmanager::body')
    @include('__CommonPartials.__Navbar.NavbarGoToSearchPage', ['search' => true ])

    <main 
    >
        <input type="hidden" id="endpoint_route" value="{{ route('public.landing.view') }}">
        
        <section class="hero user-select-none d-none d-lg-block">
            <img 
                onerror="this.onerror=null;this.src='/assets/images/404q.png';"
                src="{{ asset('/assets/images/homepage new header.png') }}" alt=""
            >
        </section>

        <div class="text__middle pt-3"
        >
            <p class="head">
            @if (empty(app('request')->input('search')))  
            What's happening?
            @else
            Showing search results for '{{app('request')->input('search')}}'
            @endif
            </p>
        </div>

        <section 
            class="featured-events scrolling-pagination"
        >
            @include('__CommonPartials.Landing')
        </section>
        @if (isset($events[0]))
            <div class="no-more-data d-none"></div>
        @else
        <div class="no-more-data text-center mx-auto"> We don't have any events to display </div>
        @endif
        <br><br>
        
        
        <script type="module" src="{{ asset('/assets/js/shared/Landing.js') }}"></script>

    </main>
</body>

</html>
