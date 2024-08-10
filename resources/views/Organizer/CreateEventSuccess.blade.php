    @include('Organizer.__Partials.CreateEventHeadTag')
    <body>
        @include('__CommonPartials.NavbarGoToSearchPage')

        <main>
            <div>
                <div>
                    @include('Organizer.__CreateEditPartials.CreateEventSuccessTimelineBox')
                    @include('Organizer.__CreateEditPartials.CreateEventSuccess', ['event' => $event])
                </div>
            </div>
            <br><br>
        </main>
        
        <script src="{{ asset('/assets/js/jsUtils.js') }}"></script>
    </body>
