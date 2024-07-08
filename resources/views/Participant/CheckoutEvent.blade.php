<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event checkout</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/organizer/event-creation.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])    
    <link rel="stylesheet" href="{{ asset('/assets/css/organizer/event-checkout.css') }}">
</head>

<body>
    @include('__CommonPartials.NavbarGoToSearchPage')
    <main class="main-background-2">
        <br><br><br>
        @include('Participant.__CheckoutPartials.CheckoutPaymentOptions')
        <br><br>
    </main>
    
    @include('Participant.__CheckoutPartials.CheckoutScripts')
    <script src="{{ asset('/assets/js/navbar/toggleNavbar.js') }}"></script>
</body>
