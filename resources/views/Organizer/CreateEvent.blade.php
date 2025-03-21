    @include('Organizer.includes.CreateEventHeadTag')

    <body>
    @include('googletagmanager::body')
        @include('includes.__Navbar.NavbarGoToSearchPage')
        <main>
            <div>
                <div>
                    <form enctype="multipart/form-data" onkeydown="return event.key != 'Enter';"
                        action="{{ route('event.store') }}" method="post" name="create-event-form" novalidate>
                        @csrf
                        @include('Organizer.__CreateEditPartials.CreateEventHiddenForm')
                        @include('Organizer.__CreateEditPartials.CreateEventTimelineBox')
                        @if (session()->has('error'))
                            @include('Organizer.__CreateEditPartials.CreateEventTimelineWelcome', [
                                'error' => session()->get('error'),
                            ])
                        @else
                            @include('Organizer.__CreateEditPartials.CreateEventTimelineWelcome')
                        @endif
                        @include('Organizer.__CreateEditPartials.CreateEventStepOne')
                        
                        @include('Organizer.__CreateEditPartials.CreateEventForm')
                        @if (session()->has('success'))
                            @include('Organizer.__CreateEditPartials.CreateEventSuccess')
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
