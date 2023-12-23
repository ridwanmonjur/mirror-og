    @include('Organizer.Layout.CreateEventHeadTag')
    <!-- https://stackoverflow.com/questions/895171/prevent-users-from-submitting-a-form-by-hitting-enter -->
    
    <body>
        <main>
            <div>
                @include('CommonLayout.Navbar')
                <div>
                        @csrf
                        @include('Organizer.Layout.CreateEventTimelineBox')
                        @include('Organizer.Layout.CreateEventSuccess', ['event' => $event])
                </div>
            </div>
            <br><br>
        </main>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.15.6/dist/sweetalert2.all.min.js"></script>
    
    </body>