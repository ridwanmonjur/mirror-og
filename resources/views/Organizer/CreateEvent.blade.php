<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Creation</title>
     @include('includes.HeadIcon')
    <link rel="stylesheet" href="{{ asset('/assets/css/organizer/event-creation.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js',  'resources/js/alpine/teamSelect.js'])    
</head>

    <body>
    @include('googletagmanager::body')
        @include('includes.Navbar')
        <main>
            <div>
                <div>
                    <form enctype="multipart/form-data" 
                        action="{{ route('event.store') }}" method="post" name="create-event-form" novalidate>
                        @csrf
                        @include('includes.CreateEditEvent.CreateEventHiddenForm')
                        @include('includes.CreateEditEvent.CreateEventTimelineBox')
                        @if (session()->has('error'))
                            @include('includes.CreateEditEvent.CreateEventTimelineWelcome', [
                                'error' => session()->get('error'),
                            ])
                        @else
                            @include('includes.CreateEditEvent.CreateEventTimelineWelcome')
                        @endif
                        @include('includes.CreateEditEvent.CreateEventStepOne')
                        
                        @include('includes.CreateEditEvent.CreateEventForm')
                        @if (session()->has('success'))
                            @include('includes.CreateEditEvent.CreateEventSuccess')
                        @endif
                    </form>
                </div>
            </div>
            <br><br>
        </main>
        
        <script src="{{ asset('/assets/js/organizer/event_creation/CreateEventPart1.js') }}"></script>
        <script src="{{ asset('/assets/js/organizer/event_creation/timeline.js') }}"></script>
        <script src="{{ asset('/assets/js/organizer/event_creation/event_create.js') }}"></script>
        <script src="{{ asset('/assets/js/organizer/event_creation/CreateEventPart2.js') }}"></script>
    </body>
