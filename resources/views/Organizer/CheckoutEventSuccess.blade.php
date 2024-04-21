    @include('Organizer.Layout.CreateEventHeadTag')

    <body>
        @include('CommonPartials.NavbarGoToSearchPage')

        <main>
            <div>
                <div class="pt-5">
                    @include('Organizer.CheckoutLayout.CheckoutEventSuccess', ['event' => $event])
                </div>
            </div>
            <br><br>
        </main>
        @include('CommonPartials.BootstrapV5Js')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.15.6/dist/sweetalert2.all.min.js"></script>
        <script src="{{ asset('/assets/js/navbar/toggleNavbar.js') }}"></script>
    </body>
