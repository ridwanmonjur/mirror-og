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
                            @include('Organizer.__CreateEditPartials.EditEventStepOne', [
                                'error' => session()->get('error'),
                            ])
                        @else
                            @include('Organizer.__CreateEditPartials.EditEventStepOne')
                        @endif
                        @include('Organizer.__CreateEditPartials.CreateEventForm', ['event' => $event])
                        @if (session()->has('success'))
                            @include('Organizer.__CreateEditPartials.CreateEventSuccess')
                        @endif
                    </form>
                </div>
            </div>
            <br><br>
        </main>
        
        @include('Organizer.__CreateEditPartials.CreateEventScripts')
        <script>

        </script>
    </body>
