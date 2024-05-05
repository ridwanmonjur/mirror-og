@include('Organizer.Partials.CheckoutEventHeadTag')

<body>
    @include('CommonPartials.NavbarGoToSearchPage')
    <main class="main-background-2">
        <br><br><br>
        @include('Organizer.CheckoutPartials.CheckoutPaymentOptions', ['event' => $event])
        <br><br>
    </main>
    
    @include('Organizer.CheckoutPartials.CheckoutScripts', ['fee' => $fee, 'event' => $event])
    <script src="{{ asset('/assets/js/navbar/toggleNavbar.js') }}"></script>
</body>
