@include('Organizer.Layout.CheckoutEventHeadTag')

<body>
    @include('CommonPartials.NavbarGoToSearchPage')
    <main class="main-background-2">
        <br><br><br>
        @include('Organizer.CheckoutPartials.CheckoutPaymentOptions', ['event' => $event])
        <br><br>
    </main>
    @include('CommonPartials.BootstrapV5Js')
    @include('Organizer.CheckoutPartials.CheckoutScripts', ['fee' => $fee, 'event' => $event])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.15.6/dist/sweetalert2.all.min.js"></script>
    <script src="{{ asset('/assets/js/navbar/toggleNavbar.js') }}"></script>
</body>
