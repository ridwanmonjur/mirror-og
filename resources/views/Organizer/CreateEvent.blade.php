    @include('Organizer.__Partials.CreateEventHeadTag')

    <body>
        @include('__CommonPartials.NavbarGoToSearchPage')
        <main>
            <div>
                <div>
                    <form enctype="multipart/form-data" onkeydown="return event.key !== 'Enter';"
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
        @include('Organizer.__CreateEditPartials.CreateEventScripts')

    </body>
