    @include('Organizer.Partials.CreateEventHeadTag')
    <body>
        @include('CommonPartials.NavbarGoToSearchPage')

        <main>
            <div>
                <div>
                    @csrf
                    @include('Organizer.CreateEditPartials.CreateEventSuccessTimelineBox')
                    @include('Organizer.CreateEditPartials.CreateEventSuccess', ['event' => $event])
                </div>
            </div>
            <br><br>
        </main>
        @include('CommonPartials.BootstrapV5Js')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.15.6/dist/sweetalert2.all.min.js"></script>
        <script src="{{ asset('/assets/js/navbar/toggleNavbar.js') }}"></script>
    </body>
