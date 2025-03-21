<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Checkout</title>
    @include('includes.HeadIcon')
    <link rel="stylesheet" href="{{ asset('/assets/css/organizer/event-creation.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])    
</head>

<body>
    @include('googletagmanager::body')
    @include('includes.__Navbar.NavbarGoToSearchPage')
   
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
        @include('Organizer.__CheckoutPartials.CheckoutPaymentOptions', ['event' => $event])
    </main>

    <script src="https://js.stripe.com/v3/"></script>
    <script src="{{ asset('/assets/js/organizer/event_creation/event_create.js') }}"></script>
    <script src="{{ asset('/assets/js/organizer/CheckoutScripts.js') }}"></script>
</body>
