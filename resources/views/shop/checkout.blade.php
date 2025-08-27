<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Checkout</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/organizer/event-creation.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])    
    @include('includes.HeadIcon')
    <style>
        #Field-nameInput, #Field-addressLine2Input {
            width: 50% !important;
            display: inline !important;
        }
        
        .clickable-header {
            cursor: pointer;
            padding: 10px 0;
            transition: background-color 0.2s ease, transform 0.1s ease;
            user-select: none;
        }
       
        
        .clickable-header h6 {
            margin: 0;
            font-weight: 600;
        }
        
        .billing-shipping-container {
            border-radius: 12px;
            padding:  0px ;
        }
        
        .billing-section,
        .shipping-section {
            position: relative;
        }
        
        .address-section {
            transition: all 0.3s ease;
        }
        
        .arrow-icon {
            transition: transform 0.3s ease, color 0.3s ease;
            color: #6c757d;
        }
        
        .text-primary .arrow-icon {
            color: var(--bs-primary) !important;
        }
        
        .arrow-collapsed {
            transform: rotate(-180deg);
        }
    </style>
</head>

<body>
    @include('googletagmanager::body')
    @include('includes.Navbar')
    <main class="main-background-2 pt-3">
        @include('includes.Checkout.ShopCheckout')
        <br>
    </main>
    
    <div class="d-none" id="payment-variables" 
        data-payment-amount="{{ $fee['finalFee'] }}"
        data-total-fee="{{ $fee['totalFee'] }}"
        data-user-email="{{ $user->email }}"
        data-user-name="{{ $user->name }}"
        data-stripe-customer-id="{{ $user->stripe_customer_id }}"
        data-cart-total="{{ $newTotal }}"
         data-cart-id="{{ $cart->id }}"
        data-coupon-code="{{ $prevForm['coupon_code'] ?? '' }}"
        data-stripe-key="{{ config('services.stripe.key') }}"
        data-stripe-card-intent-url="{{ route('stripe.stripeCardIntentCreate') }}"
        data-checkout-transition-url="{{ route('shop.checkout.transition') }}"
        data-has-physical-products="{{ $hasPhysicalProducts ? 'true' : 'false' }}"
    >    
    
    </div>

    <script src="https://js.stripe.com/v3/"></script>
    <script src="{{ asset('/assets/js/organizer/event_creation/event_create.js') }}"></script>
    <script src="{{ asset('/assets/js/shop/CheckoutScripts.js') }}"></script>
    
</body>
</html>