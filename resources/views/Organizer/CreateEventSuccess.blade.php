    @include('Organizer.Layout.CreateEventHeadTag')
    <!-- https://stackoverflow.com/questions/895171/prevent-users-from-submitting-a-form-by-hitting-enter -->

    <body>
        <main>
            <div>
                @include('CommonLayout.Navbar')
                <div>
                        @csrf
                        @include('Organizer.Layout.CreateEventTimelineBox')
                        @include('Organizer.Layout.CreateEventSuccess')
                </div>
                @include('Organizer.Layout.CreateEventPaymentModal')
            </div>
            <br><br>
        </main>
        @include('Organizer.Layout.CreateEventScripts')


    </body>