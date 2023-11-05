<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('assets/css/participant/player_home.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Splash</title>
    <script src="{{ asset('script.js') }}"></script>
</head>
<body>
    <nav>
        <div class="nav__sect1">
            <img class="logo" src="{{ asset('/assets/images/logo2.png') }}" alt="">
            <div class="search_box">
                <i class="fa fa-search"></i>
                <input class="nav__input" type="text" placeholder="Search for events">
            </div>
        </div>

        <div class="hamburger-menu">
            <i class="fa fa-bars"></i>
        </div>
        @guest
        <div class="nav__items">
            <ul>
                <li><a href="#" id='nav-but' class="moop">Where's Moop?</a></li>
                <li><a href="#" id='nav-but' class="sign">Sign In</a></li>
            </ul>
        </div>
        @endguest
        @auth
        <div class="nav__items">
            <ul>
                <li><a href="#" id='nav-but' class="moop">Where's Moop?</a></li>
                <li>
                    <img style="position: relative; top: 0px; cursor: pointer;" width="50px" height="40px" src="{{ asset('/assets/images/navbar-account.png') }}" alt="">
                </li>
                <li>
                    <img style="position: relative; top: 0px; left: -20px; cursor: pointer;" width="70px" height="40px" src="{{ asset('/assets/images/navbar-crown.png') }}" alt="">
                </li>
            </ul>
        </div>
        @endauth
    </nav>

    <section class="hero">
        <img src="{{ asset('/assets/images/ss.png') }}" alt="">
    </section>

    <div class="text__middle">
        <p class="head">What's happening?</p>
    </div>

    <section class="featured-events">
        @foreach($events as $event)
        @php
            $stylesEventStatus = '';
            $stylesEventStatus .= 'padding-top: -150px; ';
            $stylesEventStatus .= 'background-color: ' . $mappingEventState[$event->action]['buttonBackgroundColor'] .' ;' ;
            $stylesEventStatus .= 'color: ' . $mappingEventState[$event->action]['buttonTextColor'] .' ; ' ;
            $stylesEventStatus .= 'border: 1px solid ' . $mappingEventState[$event->action]['borderColor'] .' ; ';
            @endphp
            <div class="event">
                <div class="event_head_container">
                    <img id='turtle' src="{{ asset('/assets/images/logo/3.png') }}" class="event_head">
                </div>
                <img src="{{ asset('/assets/images/event_bg.jpg') }}" class="cover">
                <div class="frame1">
                    <img src="{{ asset('/assets/images/dota.png') }}" class="logo2">
                    <a class="event_status_1" style="@php echo $stylesEventStatus; @endphp">{{ $event->action }}</a>
                </div><br>
                <div class="league_name">
                    <b>{{ $event->eventName }}</b><br>
                    <a><small>South East Asia</small></a>
                </div><br>
                <div class="trophy_caption">
                    <img src="{{ asset('/assets/images/trophy.png') }}" class="trophy"><br>
                    <a class="league_caption">
                        <b>{{ $event->caption }}</b>
                    </a>
                </div>
            </div>
        @endforeach
    </section>

    <footer>
        <p>Show More</p>
    </footer>
</body>
</html>
