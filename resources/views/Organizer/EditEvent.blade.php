    @include('Organizer.Layout.CreateEventHeadTag')
    <!-- https://stackoverflow.com/questions/895171/prevent-users-from-submitting-a-form-by-hitting-enter -->

    <body>
        @include('CommonPartials.NavbarGoToSearchPage')
        <main>
            <div>
                <div>
                    <form enctype="multipart/form-data" onkeydown="return event.key != 'Enter';"
                        action="{{ route('event.updateForm', $event->id) }}" method="post" name="create-event-form"
                        novalidate>
                        @csrf
                        @include('Organizer.CreateEditPartials.EditEventHiddenForm', ['event' => $event])
                        @include('Organizer.CreateEditPartials.CreateEventTimelineBox')
                        @if (session()->has('error'))
                            @include('Organizer.CreateEditPartials.EditEventStepOne', [
                                'error' => session()->get('error'),
                            ])
                        @else
                            @include('Organizer.CreateEditPartials.EditEventStepOne')
                        @endif
                        @include('Organizer.CreateEditPartials.CreateEventForm', ['event' => $event])
                        @if (session()->has('success'))
                            @include('Organizer.CreateEditPartials.CreateEventSuccess')
                        @endif
                    </form>
                </div>
            </div>
            <br><br>
        </main>
        @include('CommonPartials.BootstrapV5Js')
        @include('Organizer.CreateEditPartials.CreateEventScripts')
        <script>

        </script>
    </body>
