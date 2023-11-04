<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('assets/css/participant/player_home.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Splash</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/app.css') }}">
    <script src="{{ asset('script.js') }}"></script>
</head>

<body>
    <!-- @include('CommonLayout.Navbar') -->

    <section class="hero">
        <img src="{{ asset('/assets/images/ss.png') }}" alt="">
    </section>

    <div class="text__middle">
        <p class="head">What's happening?</p>
    </div>

    <section class="featured-events">
        @foreach($events as $event)
        <div class="event">
            <div class="event_head_container">
                <img id='turtle' src="{{ asset('/assets/images/logo/3.png') }}" class="event_head">
            </div>
            <img src="{{ asset('/assets/images/event_bg.jpg') }}" class="cover">
            <div class="frame1">
                <img src="{{ asset('/assets/images/dota.png') }}" class="logo2">
                <a class="event_status_1">{{ $event->status }}</a>
            </div><br>
            <div class="league_name">
                <b>{{ $event->name }}</b><br>
                <a><small>{{ $event->venue }}</small></a>
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
@stack('script')

</html>