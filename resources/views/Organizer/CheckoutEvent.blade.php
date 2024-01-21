@include('Organizer.Layout.CheckoutEventHeadTag')

<body>
    @include('CommonLayout.NavbarGoToSearchPage')
    <main class="main-background-2">
        <br><br><br>
        @include('Organizer.Checkout.CheckoutPaymentOptions', ['event' => $event])
        <br><br>
    </main>
    @include('CommonLayout.BootstrapJs')
    @include('Organizer.Checkout.CheckoutScripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.15.6/dist/sweetalert2.all.min.js"></script>
    <script src="{{ asset('/assets/js/navbar/toggleNavbar.js') }}"></script>
</body>
