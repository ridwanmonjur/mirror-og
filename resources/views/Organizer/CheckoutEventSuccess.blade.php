    @include('Organizer.__Partials.CreateEventHeadTag')

    <body>
        @include('__CommonPartials.NavbarGoToSearchPage')

        <main>
            <div>
                <div class="pt-5">
                    @include('Organizer.__CheckoutPartials.CheckoutEventSuccess', ['event' => $event])
                </div>
            </div>
            <br><br>
        </main>
        
        <script src="{{ asset('/assets/js/navbar/toggleNavbar.js') }}"></script>
    </body>
