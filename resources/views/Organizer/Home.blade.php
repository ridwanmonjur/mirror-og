<!DOCTYPE html>
<html lang="en">


<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organizer Home Page</title>
    @include('includes.HeadIcon')

    <link rel="stylesheet" href="{{ asset('/assets/css/organizer/home.css') }}">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])    
</head>

<body>
    @include('googletagmanager::body')
    @include('includes.Navbar')
    <main>
        <input type="hidden" id="endpoint_route" value="{{ route('public.landing.view') }}">
       
        
        <section class="hero user-select-none d-none d-lg-block">
            <img 
                onerror="this.onerror=null;this.src='/assets/images/404q.png';"
                src="{{ asset('/assets/images/homepage new header.png') }}" alt=""
            >
        </section>

        <div class="d-block text-center d-lg-none">
            <br><br>
        </div>

         <div class="text__middle d-none d-lg-block"
        >
            <p class="head py-0 text-center mb-4 text-lg-start mt-3">What would you like to do?</p>
        </div>

        <section class="d-flex flex-wrap justify-content-center">
            <!-- EVENTS -->
            <!-- Box 1 -->
            <a href="{{ route('event.create') }}" class="clickable-box " id="imageLink1">
                <div class="event d-flex justify-content-center flex-column">
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
            <a href="{{ route('event.index') }}" class="clickable-box " id="imageLink2">
                <div class="event d-flex justify-content-center flex-column">
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
            <a href="{{ route('shop.index') }}" class="clickable-box " id="imageLink3">
                <div class="event d-flex justify-content-center flex-column">
                    <div class="event_head_container">
                        View the shop
                    </div>

                    <div class="frame1">
                        <img src="{{ asset('/assets/images/events/shop.png') }}" width="200px" height="200px"
                            alt="Clickable Image" />
                    </div><br>

                    <div class="caption">
                        See what is listed in the official store and make your purchases!
                        All listings may be available for a limited time only.
                    </div>
                </div>
            </a>
            
            <script src="{{ asset('/assets/js/organizer/Home.js') }}"></script>
        </section>
    </main>
</body>

</html>
