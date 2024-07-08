    @include('Organizer.__Partials.CreateEventHeadTag')
    <body>
        @include('__CommonPartials.NavbarGoToSearchPage')

        <main>
            <div>
                <div>
                    @csrf
                    @include('Organizer.__CreateEditPartials.CreateEventSuccessTimelineBox')
                    @include('Organizer.__CreateEditPartials.CreateEventSuccess', ['event' => $event])
                </div>
            </div>
            <br><br>
        </main>
        
        <script src="{{ asset('/assets/js/navbar/toggleNavbar.js') }}"></script>
    </body>
