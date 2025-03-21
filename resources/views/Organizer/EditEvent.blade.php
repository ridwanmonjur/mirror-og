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
    <!-- https://stackoverflow.com/questions/895171/prevent-users-from-submitting-a-form-by-hitting-enter -->

    <body>
    @include('googletagmanager::body')
        @include('includes.__Navbar.NavbarGoToSearchPage')
        <main>
            <div>
                <div>
                    <form enctype="multipart/form-data" onkeydown="return event.key != 'Enter';"
                        action="{{ route('event.updateForm', $event->id) }}" method="post" name="create-event-form"
                        novalidate>
                        @csrf
                        @include('includes.__CreateEditEventPartials.EditEventHiddenForm', ['event' => $event])
                        @include('includes.__CreateEditEventPartials.CreateEventTimelineBox')
                        @if (session()->has('error'))
                            @include('includes.__CreateEditEventPartials.EditEventTimelineWelcome', [
                                'error' => session()->get('error'),
                            ])
                        @else
                            @include('includes.__CreateEditEventPartials.EditEventTimelineWelcome')
                        @endif
                        @include('includes.__CreateEditEventPartials.CreateEventStepOne', ['event' => $event])
                        @include('includes.__CreateEditEventPartials.CreateEventForm', ['event' => $event])
                        @if (session()->has('success'))
                            @include('includes.__CreateEditEventPartials.CreateEventSuccess')
                        @endif
                    </form>
                    <form onkeydown="return event.key != 'Enter';" id="cancelEvent" method="POST"
                        action="{{ route('event.updateForm', $event->id) }}"
                    >
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
