    @include('Organizer.Partials.CreateEventHeadTag')

    <body>
        @include('CommonPartials.NavbarGoToSearchPage')
        <main>
            <div>
                <div>
                    <form enctype="multipart/form-data" onkeydown="return event.key != 'Enter';"
                        action="{{ route('event.store') }}" method="post" name="create-event-form" novalidate>
                        @csrf
                        @include('Organizer.CreateEditPartials.CreateEventHiddenForm')
                        @include('Organizer.CreateEditPartials.CreateEventTimelineBox')
                        @if (session()->has('error'))
                            @include('Organizer.CreateEditPartials.CreateEventTimelineWelcome', [
                                'error' => session()->get('error'),
                            ])
                        @else
                            @include('Organizer.CreateEditPartials.CreateEventTimelineWelcome')
                        @endif
                        @include('Organizer.CreateEditPartials.CreateEventStepOne')
                        
                        @include('Organizer.CreateEditPartials.CreateEventForm')
                        @if (session()->has('success'))
                            @include('Organizer.CreateEditPartials.CreateEventSuccess')
                        @endif
                    </form>
                </div>
            </div>
            <br><br>
        </main>
        @include('Organizer.CreateEditPartials.CreateEventScripts')

    </body>
