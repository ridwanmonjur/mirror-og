{{-- @php
    dd($user);
@endphp --}}
<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Checkout</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/organizer/event-creation.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])    
    @include('includes.HeadIcon')
    <style>
        #Field-nameInput, #Field-addressLine2Input {
            width: 50% !important;
            display: inline !important;
        }
    </style>
</head>

<body>
    @include('googletagmanager::body')
    @include('includes.Navbar')
    <main class="main-background-2 pt-3">
        @include('includes.Checkout.PlayerCheckout')
        <br>
    </main>
    
    <div class="d-none" id="payment-variables" 
        data-payment-amount="{{ $fee['finalFee'] }}"
        data-total-fee="{{ $fee['totalFee'] }}"
        data-user-email="{{ $user->email }}"
        data-user-name="{{ $user->name }}"
        data-stripe-customer-id="{{ $user->stripe_customer_id }}"
        data-join-event-id="{{ $joinEventId }}"
        data-member-id="{{ $memberId }}"
        data-team-id="{{ $teamId }}"
        data-event-id="{{ $event->id }}"
        data-event-type="{{ $event->getRegistrationStatus() }}"
        data-coupon-code="{{ $prevForm['coupon_code'] }}"
        data-stripe-key="{{ config('services.stripe.key') }}"
        data-stripe-card-intent-url="{{ route('stripe.stripeCardIntentCreate') }}"
        data-checkout-transition-url="{{ route('participant.checkout.transition') }}"
    >    
    
    </div>

    <script src="https://js.stripe.com/v3/"></script>
    <script src="{{ asset('/assets/js/organizer/event_creation/event_create.js') }}"></script>
    <script src="{{ asset('/assets/js/participant/CheckoutScripts.js') }}"></script>
    
</body>
