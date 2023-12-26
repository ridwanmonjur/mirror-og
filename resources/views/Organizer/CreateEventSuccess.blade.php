    @include('Organizer.Layout.CreateEventHeadTag')
    <!-- https://stackoverflow.com/questions/895171/prevent-users-from-submitting-a-form-by-hitting-enter -->
    
    <body>
                    @include('CommonLayout.Navbar')

        <main>
            <div>
                <div>
                        @csrf
                        @include('Organizer.Layout.CreateEventSuccessTimelineBox')
                        @include('Organizer.Layout.CreateEventSuccess', ['event' => $event])
                </div>
            </div>
            <br><br>
        </main>    
        @include('CommonLayout.BootstrapJs')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.15.6/dist/sweetalert2.all.min.js"></script>
        <script src="{{ asset('/assets/js/navbar/toggleNavbar.js') }}"> </script>
    </body>