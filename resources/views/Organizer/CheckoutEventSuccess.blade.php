    @include('Organizer.Partials.CreateEventHeadTag')

    <body>
        @include('CommonPartials.NavbarGoToSearchPage')

        <main>
            <div>
                <div class="pt-5">
                    @include('Organizer.CheckoutPartials.CheckoutEventSuccess', ['event' => $event])
                </div>
            </div>
            <br><br>
        </main>
        
        <script src="{{ asset('/assets/js/navbar/toggleNavbar.js') }}"></script>
    </body>
