<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event creation</title>
     @include('includes.HeadIcon')
    <link rel="stylesheet" href="{{ asset('/assets/css/organizer/event-creation.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])    
</head>
<body style="margin-top: 0 !important;">
@include('includes.Navbar.NavbarGoToSearchPage')

    <main>
        <br><br><br><br>
        <div class="text-center" >
            <div >
                <u>
                    <h3 id="heading">Error occurred!</h3>
                </u>
            </div>
            <div class="box-width">
                @if (isset($error))
                <p id="notification">{{ $error }}</p>
                @endif
            </div>
            <br><br><br><br>
            <a href="{{ route('public.landing.view') }}" class="oceans-gaming-default-button" style="padding: 10px 50px; background-color: white; color: #2e4b59; border: 1px solid black; text-decoration: none; display: inline-block;">
                Go to home page
            </a>

        </div>
        

    </main>
</body>
