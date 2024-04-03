    @include('Organizer.Layout.CreateEventHeadTag')

    <body>
        @include('CommonLayout.NavbarGoToSearchPage')
        <main>
            <div>
                <div>
                    <form enctype="multipart/form-data" onkeydown="return event.key != 'Enter';"
                        action="{{ route('event.store') }}" method="post" name="create-event-form" novalidate>
                        @csrf
                        @include('Organizer.CreateEditLayout.CreateEventHiddenForm')
                        @include('Organizer.CreateEditLayout.CreateEventTimelineBox')
                        @if (session()->has('error'))
                            @include('Organizer.CreateEditLayout.CreateEventTimelineWelcome', [
                                'error' => session()->get('error'),
                            ])
                        @else
                            @include('Organizer.CreateEditLayout.CreateEventTimelineWelcome')
                        @endif
                        @include('Organizer.CreateEditLayout.CreateEventStepOne')
                        @include('CommonLayout.BootstrapV5Js')
                        @include('Organizer.CreateEditLayout.CreateEventForm')
                        @if (session()->has('success'))
                            @include('Organizer.CreateEditLayout.CreateEventSuccess')
                        @endif
                    </form>
                </div>
            </div>
            <br><br>
        </main>
        @include('Organizer.CreateEditLayout.CreateEventScripts')

    </body>
