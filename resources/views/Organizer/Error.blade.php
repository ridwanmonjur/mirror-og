<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Not Found</title>
     @include('includes.HeadIcon')
    <link rel="stylesheet" href="{{ asset('/assets/css/organizer/event-creation.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])    
</head>

<body style="margin-top: 0 !important;">
@include('includes.Navbar')
    <main>

        <br><br><br><br>
        <div class="text-center" >
            <div >
                <u>
                    <h3 id="heading">Error occurred!</h3>
                </u>
            </div>
            <div class="box-width">
                <p id="notification">{{ $error }}</p>
            </div>
            <br><br><br><br>
            <a href="{{ route('event.index') }}" class="oceans-gaming-default-button" style="padding: 10px 50px; background-color: white; color: #2e4b59; border: 1px solid black; text-decoration: none; display: inline-block;">
                Go to event page
            </a>
            @if (isset($id) && isset($edit) && $edit )
                <br><br>
                <a href="{{ route('event.edit', ['event' => isset($id) ? $id : -1]) }}" class="oceans-gaming-default-button" style="padding: 10px 50px; background-color: white; color: #2e4b59; border: 1px solid black; text-decoration: none; display: inline-block;">
                    Edit event
                </a>
            @endif
        </div>
    </main>
</body>
