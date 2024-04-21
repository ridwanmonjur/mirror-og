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
                        @include('Organizer.CreateEditLayout.EditEventHiddenForm', ['event' => $event])
                        @include('Organizer.CreateEditLayout.CreateEventTimelineBox')
                        @if (session()->has('error'))
                            @include('Organizer.CreateEditLayout.EditEventStepOne', [
                                'error' => session()->get('error'),
                            ])
                        @else
                            @include('Organizer.CreateEditLayout.EditEventStepOne')
                        @endif
                        @include('Organizer.CreateEditLayout.CreateEventForm', ['event' => $event])
                        @if (session()->has('success'))
                            @include('Organizer.CreateEditLayout.CreateEventSuccess')
                        @endif
                    </form>
                </div>
            </div>
            <br><br>
        </main>
        @include('CommonPartials.BootstrapV5Js')
        @include('Organizer.CreateEditLayout.CreateEventScripts')
        <script>

        </script>
    </body>
