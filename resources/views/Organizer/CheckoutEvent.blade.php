@include('Organizer.__Partials.CheckoutEventHeadTag')

<body>
    @include('googletagmanager::body')
    @include('__CommonPartials.NavbarGoToSearchPage')
   
    <main class="main-background-2">
        <div id="hidden-variables" class="d-none"
            data-fee-final="{{ $fee['finalFee'] }}"
            data-user-email="{{ $user->email }}"
            data-user-name="{{ $user->name }}"
            data-stripe-customer-id="{{ $user->stripe_customer_id }}"
            data-event-id="{{ $event->id }}"
            data-stripe-key="{{ env('STRIPE_KEY') }}"
            data-stripe-intent-url="{{ route('stripe.stripeCardIntentCreate') }}"
            data-stripe-return-url="{{ route('organizer.checkout.transition', ['id' => $event->id]) }}">
        </div>
        <br><br><br>
        @include('Organizer.__CheckoutPartials.CheckoutPaymentOptions', ['event' => $event])
        <br><br>
    </main>

    @include('Organizer.__CheckoutPartials.CheckoutScripts', ['fee' => $fee, 'event' => $event])
    <script src="{{ asset('/assets/js/jsUtils.js') }}"></script>
</body>
