    @include('Organizer.Layout.CreateEventHeadTag')
    <!-- https://stackoverflow.com/questions/895171/prevent-users-from-submitting-a-form-by-hitting-enter -->

    <body>
        @include('CommonLayout.NavbarGoToSearchPage')
        <main>
            <div>
                <div>
                    <form enctype="multipart/form-data" onkeydown="return event.key != 'Enter';"
                        action="{{ route('event.updateForm', $event->id) }}" method="post" name="create-event-form"
                        novalidate>
                        @csrf
                        @include('Organizer.CreateEdit.EditEventHiddenForm', ['event' => $event])
                        @include('Organizer.CreateEdit.CreateEventTimelineBox')
                        @if (session()->has('error'))
                            @include('Organizer.CreateEdit.EditEventStepOne', [
                                'error' => session()->get('error'),
                            ])
                        @else
                            @include('Organizer.CreateEdit.EditEventStepOne')
                        @endif
                        @include('Organizer.CreateEdit.CreateEventForm', ['event' => $event])
                        @if (session()->has('success'))
                            @include('Organizer.CreateEdit.CreateEventSuccess')
                        @endif
                    </form>
                </div>
            </div>
            <br><br>
        </main>
        @include('CommonLayout.BootstrapV5Js')
        @include('Organizer.CreateEdit.CreateEventScripts')
        <script>

        </script>
    </body>
