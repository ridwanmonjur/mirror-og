    @include('Organizer.Layout.CreateEventHeadTag')

    <body>
        @include('CommonLayout.NavbarGoToSearchPage')
        <main>
            <div>
                <div>
                    <form enctype="multipart/form-data" onkeydown="return event.key != 'Enter';"
                        action="{{ route('event.store') }}" method="post" name="create-event-form" novalidate>
                        @csrf
                        @include('Organizer.CreateEdit.CreateEventHiddenForm')
                        @include('Organizer.CreateEdit.CreateEventTimelineBox')
                        @if (session()->has('error'))
                            @include('Organizer.CreateEdit.CreateEventTimelineWelcome', [
                                'error' => session()->get('error'),
                            ])
                        @else
                            @include('Organizer.CreateEdit.CreateEventTimelineWelcome')
                        @endif
                        @include('Organizer.CreateEdit.CreateEventStepOne')
                        @include('CommonLayout.BootstrapJs')
                        @include('Organizer.CreateEdit.CreateEventForm')
                        @if (session()->has('success'))
                            @include('Organizer.CreateEdit.CreateEventSuccess')
                        @endif
                    </form>
                </div>
            </div>
            <br><br>
        </main>
        @include('Organizer.CreateEdit.CreateEventScripts')

    </body>
