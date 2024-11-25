{{-- @php
    dd($user);
@endphp --}}
<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event checkout</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/organizer/event-creation.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])    
    @include('__CommonPartials.HeadIcon')
    <style>
        #Field-nameInput, #Field-addressLine2Input {
            width: 50% !important;
            display: inline !important;
        }
    </style>
</head>

<body>
    @include('googletagmanager::body')
    @include('__CommonPartials.NavbarGoToSearchPage')
    <main class="main-background-2">
        <br><br><br>
        @include('Participant.__CheckoutPartials.CheckoutPaymentOptions')
        <br><br>
    </main>
    
    <div class="d-none" id="payment-variables" 
        data-payment-amount="{{ $amount }}"
        data-user-email="{{ $user->email }}"
        data-user-name="{{ $user->name }}"
        data-stripe-customer-id="{{ $user->stripe_customer_id }}"
        data-join-event-id="{{ $joinEventId }}"
        data-member-id="{{ $memberId }}"
        data-team-id="{{ $teamId }}"
        data-event-id="{{ $event->id }}"
        data-event-type="{{ $event->getRegistrationStatus() }}"
        data-stripe-key="{{ env('STRIPE_KEY') }}"
        data-stripe-card-intent-url="{{ route('stripe.stripeCardIntentCreate') }}"
        data-checkout-transition-url="{{ route('participant.checkout.transition') }}"
    >    
    
    </div>
    @include('Participant.__CheckoutPartials.CheckoutScripts')
    
</body>
