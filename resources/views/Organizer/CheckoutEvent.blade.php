@include('Organizer.__Partials.CheckoutEventHeadTag')

<body>
    @include('__CommonPartials.NavbarGoToSearchPage')
    <main class="main-background-2">
        <br><br><br>
        @include('Organizer.__CheckoutPartials.CheckoutPaymentOptions', ['event' => $event])
        <br><br>
    </main>
    
    @include('Organizer.__CheckoutPartials.CheckoutScripts', ['fee' => $fee, 'event' => $event])
    <script src="{{ asset('/assets/js/jsUtils.js') }}"></script>
</body>
