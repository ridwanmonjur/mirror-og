    @include('Organizer.__Partials.CreateEventHeadTag')
    <!-- https://stackoverflow.com/questions/895171/prevent-users-from-submitting-a-form-by-hitting-enter -->

    <body>
        @include('__CommonPartials.NavbarGoToSearchPage')
        <main>
            <div>
                <div>
                    <form enctype="multipart/form-data" onkeydown="return event.key != 'Enter';"
                        action="{{ route('event.updateForm', $event->id) }}" method="post" name="create-event-form"
                        novalidate>
                        @csrf
                        @include('Organizer.__CreateEditPartials.EditEventHiddenForm', ['event' => $event])
                        @include('Organizer.__CreateEditPartials.CreateEventTimelineBox')
                        @if (session()->has('error'))
                            @include('Organizer.__CreateEditPartials.EditEventTimelineWelcome', [
                                'error' => session()->get('error'),
                            ])
                        @else
                            @include('Organizer.__CreateEditPartials.EditEventTimelineWelcome')
                        @endif
                        @include('Organizer.__CreateEditPartials.EditEventStepOne')
                        @include('Organizer.__CreateEditPartials.CreateEventForm', ['event' => $event])
                        @if (session()->has('success'))
                            @include('Organizer.__CreateEditPartials.CreateEventSuccess')
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
        <script src="{{ asset('/assets/js/jsUtils.js') }}"></script>
        <script src="{{ asset('/assets/js/organizer/event_creation/CreateEventPart2.js') }}"></script>
    </body>
