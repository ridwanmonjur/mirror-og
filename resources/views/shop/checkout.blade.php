<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    @include('includes.HeadIcon')
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <script src="https://js.stripe.com/v3/"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/common/shop.css') }}">
    <style>
        .StripeElement {
            box-sizing: border-box;

            height: 40px;

            padding: 10px 12px;

            border: 1px solid transparent;
            border-radius: 4px;
            background-color: white;

            box-shadow: 0 1px 3px 0 #e6ebf1;
            -webkit-transition: box-shadow 150ms ease;
            transition: box-shadow 150ms ease;
        }

        .StripeElement--focus {
            box-shadow: 0 1px 3px 0 #cfd7df;
        }

        .StripeElement--invalid {
            border-color: #fa755a;
        }

        .StripeElement--webkit-autofill {
            background-color: #fefde5 !important;
        }
    </style>
</head>

<body>
    @include('googletagmanager::body')
    @include('includes.Navbar')

    <main class="px-4 product">

        <p class="lead mt-3">Complete your order</p>

        <hr>

        <div class="row">
            <div class="col-md-8 order-md-1">
                <h4 class="mb-3">SHIPPING ADDRESS</h4>

                {{-- success error msg start --}}
                @if (session()->has('success_message'))
                    <div class="spacer"></div>
                    <div class="text-success">
                        {{ session()->get('success_message') }}
                    </div>
                @endif

                @if (count($errors) > 0)
                    <div class="spacer"></div>
                    <div class=" text-red">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{!! $error !!}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                {{-- success error msg end --}}

                <form action="{{ route('checkout.store') }}" method="POST" id="payment-form">
                    {{ csrf_field() }}

                    <div class="mb-3">
                        <label for="email">Email Address</label>
                        @if (auth()->user())
                            <input type="email" class="form-control" id="email" name="email"
                                value="{{ auth()->user()->email }}" readonly>
                        @else
                            <input type="email" class="form-control" id="email" name="email"
                                value="{{ old('email') }}" required>
                        @endif
                    </div>


                    <div class="mb-3">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name"
                            value="{{ old('name') }}" required>
                    </div>


                    <div class="mb-3">
                        <label for="address">Address</label>
                        <input type="text" class="form-control" id="address" name="address"
                            value="{{ old('address') }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="city">City</label>
                            <input type="text" class="form-control" id="city" name="city"
                                value="{{ old('city') }}" required>
                        </div>
                        <div class="col-md-5 mb-3">
                            <label for="province">Province</label>
                            <input type="text" class="form-control" id="province" name="province"
                                value="{{ old('province') }}" required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="postalcode">Postal Code</label>
                            <input type="text" class="form-control" id="postalcode" name="postalcode"
                                value="{{ old('postalcode') }}" required>
                        </div>
                    </div> <!-- row end -->


                    <div class="mb-3">
                        <label for="phone">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone"
                            value="{{ old('phone') }}" required>
                    </div>


                    <hr class="mb-4">

                    <h4 class="mb-3">Payment</h4>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="name_on_card">Name on Card</label>
                            <input type="text" class="form-control" id="name_on_card" name="name_on_card"
                                value="">
                        </div>

                        <div class="col-md-12">
                            <label for="card-element">
                                Credit or debit card
                            </label>
                            <div id="card-element">
                                <!-- a Stripe Element will be inserted here. -->
                            </div>

                            <!-- Used to display form errors -->
                            <div id="card-errors" role="alert"></div>
                        </div>
                    </div>

                    <hr class="mb-4">

                    <button type="submit" id="complete-order"
                        class="btn btn-primary text-white px-4">Checkout</button>


                </form>



            </div>{{--  col-md-8 order-md-1 end --}}
            <div class="col-md-4 order-md-2 mb-4">
                <div class="cart_sidebar">
                    <br>


                    <h4 style="font-weight: 600; font-size: 22px; margin-left: 9px;">ORDER SUMMARY:</h4>
                    <div class="cart-calculator">
                        <table class="table">
                            <tr>
                                <td>{{ $cart->getCount() }} PRODUCTS</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Product total</td>
                                <td>RM {{ number_format($cart->getSubTotal(), 2) }}</td>

                            </tr>
                            @if (session()->has('coupon'))
                                <tr>
                                    <td>
                                        COUPON : {{ session()->get('coupon')['name'] }}
                                    </td>

                                    <td>- RM {{ number_format(session()->get('coupon')['discount'], 2) }}
                                        <form method="post" action="{{ route('coupon.destroy') }}"
                                            style="display:inline">
                                            {{ csrf_field() }}
                                            {{ method_field('delete') }}
                                            <button type="submit" class="btn btn-link"
                                                style="color: #000; font-size: 12px;"><span
                                                    class="fa fa-trash"></span></button>
                                        </form>
                                    </td>

                                </tr>
                            @endif
                            <tr style="font-weight: bold">
                                <td>Total</td>
                                <td>RM {{ number_format($cart->getTotal(), 2) }}</td>
                            </tr>

                            @if (session()->has('coupon'))
                                <tr>
                                    <td>Discount<br>
                                        <b>Net Total</b>
                                    </td>
                                    <td>- RM {{ number_format($discount, 2) }}<br>
                                        <b>RM {{ number_format($newTotal, 2) }} </b>

                                    </td>
                                </tr>

                                <style type="text/css">
                                    .cart_sidebar {
                                        height: 465px;
                                        width: 300px;
                                        background-color: #f8f9fa;
                                    }

                                    .cart-calculator {
                                        margin: 10px;

                                        height: 395px;
                                        width: 280px;
                                        background-color: #fff;
                                    }
                                </style>
                            @endif

                        </table>
                    </div>
                </div> {{-- cart_sidebar end --}}
                <br>

                {{--    Coupon start --}}
                <div class="coupon_fr">

                    <div class="coupon_in">
                        <table class="table">
                            <tr>
                                <td>
                                    <b>PROMO CODE</b>
                                </td>
                            </tr>
                        </table>
                        <div class="row">
                            <div class="col">
                                <div class="card card-body">
                                    <form method="post" action="{{ route('coupon.store') }}">
                                        {{ csrf_field() }}
                                        <input type="text" name="coupon_code" class="form-control"
                                            placeholder="CODES ARE CASE-SENSITIVE">
                                        <p style="font-size: 12px; color: grey;">Casing & hyphens need to be exact
                                        </p>

                                        <button type="submit" class="btn btn-success text-dark  btn-block rounded-pill">Apply 
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right ms-2" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{--    Coupon end --}}
                <br>


                <br>


            </div>{{--  cart_sidebar2 end --}}

        </div>{{-- col-md-4 order-md-2 mb-4 end --}}
        </div> {{-- row end --}}




        </div>

        <br><br>

    </main>

    <script>
        (function() {
            // Create a Stripe client
            var stripe = Stripe('{{ config('services.stripe.key') }}');

            // Create an instance of Elements
            var elements = stripe.elements();

            // Custom styling can be passed to options when creating an Element.
            // (Note that this demo uses a wider set of styles than the guide below.)
            var style = {
                base: {
                    color: '#32325d',
                    lineHeight: '18px',
                    fontFamily: '"Roboto", Helvetica Neue", Helvetica, sans-serif',
                    fontSmoothing: 'antialiased',
                    fontSize: '16px',
                    '::placeholder': {
                        color: '#aab7c4'
                    }
                },
                invalid: {
                    color: '#fa755a',
                    iconColor: '#fa755a'
                }
            };

            // Create an instance of the card Element
            var card = elements.create('card', {
                style: style,
                hidePostalCode: true
            });

            // Add an instance of the card Element into the `card-element` <div>
            card.mount('#card-element');

            // Handle real-time validation errors from the card Element.
            card.addEventListener('change', function(event) {
                var displayError = document.getElementById('card-errors');
                if (event.error) {
                    displayError.textContent = event.error.message;
                } else {
                    displayError.textContent = '';
                }
            });

            // Handle form submission
            var form = document.getElementById('payment-form');
            form.addEventListener('submit', function(event) {
                event.preventDefault();

                // Disable the submit button to prevent repeated clicks
                document.getElementById('complete-order').disabled = true;

                var options = {
                    name: document.getElementById('name_on_card').value,
                    address_line1: document.getElementById('address').value,
                    address_city: document.getElementById('city').value,
                    address_state: document.getElementById('province').value,
                    address_zip: document.getElementById('postalcode').value
                }

                stripe.createToken(card, options).then(function(result) {
                    if (result.error) {
                        // Inform the user if there was an error
                        var errorElement = document.getElementById('card-errors');
                        errorElement.textContent = result.error.message;

                        // Enable the submit button
                        document.getElementById('complete-order').disabled = false;
                    } else {
                        // Send the token to your server
                        stripeTokenHandler(result.token);
                    }
                });
            });

            function stripeTokenHandler(token) {
                // Insert the token ID into the form so it gets submitted to the server
                var form = document.getElementById('payment-form');
                var hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'stripeToken');
                hiddenInput.setAttribute('value', token.id);
                form.appendChild(hiddenInput);

                // Submit the form
                form.submit();
            }


        })();
    </script>
</body>

</html>
