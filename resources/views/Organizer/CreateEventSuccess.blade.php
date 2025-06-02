<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Creation</title>
     @include('includes.HeadIcon')
    <link rel="stylesheet" href="{{ asset('/assets/css/organizer/event-creation.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])    
</head>    

<body>
    @include('googletagmanager::body')
        @include('includes.Navbar')
        <main>
            <div>
                <div>
                    @include('includes.CreateEditEvent.CreateEventSuccessTimelineBox')
                    @include('includes.CreateEditEvent.CreateEventSuccess', ['event' => $event])
                </div>
            </div>
            <br><br>
        </main>
    </body>
