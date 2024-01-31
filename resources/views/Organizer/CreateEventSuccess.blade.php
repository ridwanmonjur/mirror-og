    @include('Organizer.Layout.CreateEventHeadTag')
    <body>
        @include('CommonLayout.NavbarGoToSearchPage')

        <main>
            <div>
                <div>
                    @csrf
                    @include('Organizer.CreateEdit.CreateEventSuccessTimelineBox')
                    @include('Organizer.CreateEdit.CreateEventSuccess', ['event' => $event])
                </div>
            </div>
            <br><br>
        </main>
        @include('CommonLayout.BootstrapV5Js')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.15.6/dist/sweetalert2.all.min.js"></script>
        <script src="{{ asset('/assets/js/navbar/toggleNavbar.js') }}"></script>
    </body>
