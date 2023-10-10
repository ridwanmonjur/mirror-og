<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/organizer/home.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="script.js"></script>
</head>

<body>
    <nav>
        <div class="nav__sect1">
            <img class="logo" src="{{ asset('/assets/images/events/logo2.png') }}" alt="">
            <div class="search_box">
                <i class="fa fa-search"></i>
                <input class="nav__input" type="text" placeholder="Search for events">
            </div>
        </div>

        <div class="nav__items">
            <ul>
                <li><a href="#" id='nav-but' class="moop">Where's Moop?</a></li>
                <li><a href="{{ route('organizer.signin.view') }}" id='nav-but' class="sign">Sign Out</a></li>
            </ul>

            <div class="hamburger-menu">
                <i class="fa fa-bars"></i>
            </div>
        </div>

    </nav>

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
            <img src="{{ asset('/assets/images/events/edit.png') }}" width="200px" height="200px" alt="Clickable Image" />
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
            <img src="{{ asset('/assets/images/events/settings.png') }}" width="200px" height="200px" alt="Clickable Image" />
        </div><br>

        <div class="caption">
            Edit your unpublished events, get updates for your live events, get insights from your past events
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
            <img src="{{ asset('/assets/images/events/shop.png') }}" width="200px" height="200px" alt="Clickable Image" />
        </div><br>

        <div class="caption">
            See what you have listed in the official store view your sales, and get buyer insights for your listings
        </div>
    </div>
    </a>

    </section>
</body>

</html>
