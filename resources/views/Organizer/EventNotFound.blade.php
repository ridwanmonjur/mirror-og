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
        <input type="hidden" id="manage_event_route" value="{{ route('event.index') }}">
        <input type="hidden" id="edit_event_route" value="{{ route('event.edit', ['event' => isset($id) ? $id : -1]) }}">

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
            <button onclick="goToManageScreen();" class="oceans-gaming-default-button" style="padding: 10px 50px; background-color: white; color: #2e4b59; border: 1px solid black;">
                Go to event page
            </button>
            @if (isset($id) && isset($edit) && $edit )
                <br><br>
                <button onclick="goToEditScreen();" class="oceans-gaming-default-button" style="padding: 10px 50px; background-color: white; color: #2e4b59; border: 1px solid black;">
                    Edit event
                </button>
            @endif
        </div>
        <script src="{{ asset('/assets/js/organizer/EventNotFound.js') }}"></script>
    </main>
</body>
