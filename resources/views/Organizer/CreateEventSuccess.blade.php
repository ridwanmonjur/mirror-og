    @include('Organizer.includes.CreateEventHeadTag')
    <body>
    @include('googletagmanager::body')
        @include('__CommonPartials.__Navbar.NavbarGoToSearchPage')
        <main>
            <div>
                <div>
                    @include('Organizer.__CreateEditPartials.CreateEventSuccessTimelineBox')
                    @include('Organizer.__CreateEditPartials.CreateEventSuccess', ['event' => $event])
                </div>
            </div>
            <br><br>
        </main>
    </body>
