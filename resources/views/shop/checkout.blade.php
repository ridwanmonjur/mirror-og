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

        

        <div class="row mt-3">
            <div class="bg-white p-3  rounded border border-light rounded-3 col-md-8 order-md-1">
                <h4 class=" text-primary px-3">Complete your order</h4>

                <hr>
                <h4 class="mb-3 lead text-primary px-3">Shipping address</h4>

                <div class="px-3">
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
                </div>
                {{-- success error msg end --}}

                <form class="px-3" action="{{ route('checkout.store') }}" method="POST" id="payment-form">
                    {{ csrf_field() }}

                    <div class="mb-3 col-xl-9">
                        <label for="email">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16">
                                    <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z"/>
                                </svg>
                            </span>
                            @if (auth()->user())
                                <input type="email" class="form-control" id="email" name="email"
                                    value="{{ auth()->user()->email }}" readonly>
                            @else
                                <input type="email" class="form-control" id="email" name="email"
                                    value="{{ old('email') }}" required>
                            @endif
                        </div>
                    </div>


                    <div class="mb-3 col-xl-9">
                        <label for="name">Name</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
                                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0Zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4Zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10Z"/>
                                </svg>
                            </span>
                            <input type="text" class="form-control" id="name" name="name"
                                value="{{ old('name') }}" required>
                        </div>
                    </div>


                    <div class="mb-3 col-xl-9">
                        <label for="address">Address</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt" viewBox="0 0 16 16">
                                    <path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A31.493 31.493 0 0 1 8 14.58a31.481 31.481 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94zM8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10z"/>
                                    <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                                </svg>
                            </span>
                            <input type="text" class="form-control" id="address" name="address"
                                value="{{ old('address') }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3 col-xl-4">
                            <label for="city">City</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-building" viewBox="0 0 16 16">
                                        <path d="M4 2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1Zm3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1Zm3.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1ZM4 5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1ZM7.5 5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1Zm2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1ZM4.5 8a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1Zm2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1Zm3.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1Z"/>
                                        <path d="M2 1a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V1Zm11 0H3v14h3v-2.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5V15h3V1Z"/>
                                    </svg>
                                </span>
                                <input type="text" class="form-control" id="city" name="city"
                                    value="{{ old('city') }}" required>
                            </div>
                        </div>
                        <div class="col-md-5 mb-3 col-xl-3">
                            <label for="province">Province</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-map" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M15.817.113A.5.5 0 0 1 16 .5v14a.5.5 0 0 1-.402.49l-5 1a.502.502 0 0 1-.196 0L5.5 15.01l-4.902.98A.5.5 0 0 1 0 15.5v-14a.5.5 0 0 1 .402-.49l5-1a.5.5 0 0 1 .196 0L10.5.99l4.902-.98a.5.5 0 0 1 .415.103zM10 1.91l-4-.8v12.98l4 .8V1.91zm1 12.98 4-.8V1.11l-4 .8v12.98zm-6-.8V1.11l-4 .8v12.98l4-.8z"/>
                                    </svg>
                                </span>
                                <input type="text" class="form-control" id="province" name="province"
                                    value="{{ old('province') }}" required>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3 col-xl-2">
                            <label for="postalcode">Postal Code</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-postcard" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H2ZM1 4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V4Zm7.5.5a.5.5 0 0 0-1 0v7a.5.5 0 0 0 1 0v-7ZM2 5.5a.5.5 0 0 1 .5-.5H6a.5.5 0 0 1 0 1H2.5a.5.5 0 0 1-.5-.5Zm0 2a.5.5 0 0 1 .5-.5H6a.5.5 0 0 1 0 1H2.5a.5.5 0 0 1-.5-.5Zm0 2a.5.5 0 0 1 .5-.5H6a.5.5 0 0 1 0 1H2.5a.5.5 0 0 1-.5-.5ZM10.5 7a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3ZM13 8v2h-2V8h2Z"/>
                                    </svg>
                                </span>
                                <input type="text" class="form-control" id="postalcode" name="postalcode"
                                    value="{{ old('postalcode') }}" required>
                            </div>
                        </div>
                    </div> <!-- row end -->


                    <div class="mb-3 col-xl-5">
                        <label for="phone">Phone</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-phone" viewBox="0 0 16 16">
                                    <path d="M11 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h6zM5 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H5z"/>
                                    <path d="M8 14a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                                </svg>
                            </span>
                            <input type="text" class="form-control" id="phone" name="phone"
                                value="{{ old('phone') }}" required>
                        </div>
                    </div>



                    <h4 class="mb-3 lead text-primary mt-2">Payment</h4>

                    <div class="row">
                        <div class="col-md-12 col-xl-9 mb-3">
                            <label for="name_on_card">Name on Card</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-credit-card" viewBox="0 0 16 16">
                                        <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v1h14V4a1 1 0 0 0-1-1H2zm13 4H1v5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V7z"/>
                                        <path d="M2 10a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1v-1z"/>
                                    </svg>
                                </span>
                                <input type="text" class="form-control" id="name_on_card" name="name_on_card"
                                    value="">
                            </div>
                        </div>

                        <div class="col-md-12 col-xl-9">
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
                <div class="cart_sidebar p-3 bg-white rounded rounded-3 border border-light">

                    <h4 class="p-0 m-0 text-primary">Order Summary</h4>
                    <hr>
                    <div class="cart-calculator">
                        <table class="table table-borderless">
                            <tr>
                                <td>Products</td>
                                <td class="text-end">{{ $cart->getCount() }}</td>
                            </tr>
                            <tr>
                                <td>Product total</td>
                                <td class="text-end">RM {{ number_format($cart->getSubTotal(), 2) }}</td>
                            </tr>
                            @if (session()->has('coupon'))
                                <tr>
                                    <td>
                                        Coupon: {{ session()->get('coupon')['name'] }}
                                        <form method="post" action="{{ route('coupon.destroy') }}"
                                            style="display:inline">
                                            {{ csrf_field() }}
                                            {{ method_field('delete') }}
                                            <button type="submit" class="btn btn-link p-0 ms-1"
                                                style="color: #000; font-size: 12px;"><span
                                                    class="fa fa-trash"></span></button>
                                        </form>
                                    </td>
                                    <td class="text-end">- RM {{ number_format(session()->get('coupon')['discount'], 2) }}</td>
                                </tr>
                            @endif
                            @if (session()->has('coupon'))
                                <tr>
                                    <td>Discount</td>
                                    <td class="text-end">- RM {{ number_format($discount, 2) }}</td>
                                </tr>
                                <tr style="font-weight: bold; border-top: 1px solid #dee2e6;">
                                    <td>Net Total</td>
                                    <td class="text-end">RM {{ number_format($newTotal, 2) }}</td>
                                </tr>
                            @else
                                <tr style="font-weight: bold; border-top: 1px solid #dee2e6;">
                                    <td>Total</td>
                                    <td class="text-end">RM {{ number_format($cart->getTotal(), 2) }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div> {{-- cart_sidebar end --}}
                <br>

                {{--    Coupon start --}}
                <div class="coupon_fr">

                    <div class="coupon_in">
                       
                        <div class="row">
                            <div class="col">
                                <div class="card card-body">
                                    <h4 class="p-0 m-0 text-primary">Promo Code</h4>
                                    <hr>
                                    <form method="post" action="{{ route('coupon.store') }}">
                                        {{ csrf_field() }}
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-tag" viewBox="0 0 16 16">
                                                    <path d="M6 4.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm-1 0a.5.5 0 1 0-1 0 .5.5 0 0 0 1 0z"/>
                                                    <path d="M2 1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 1 6.586V2a1 1 0 0 1 1-1zm0 5.586 7 7L13.586 9l-7-7H2v4.586z"/>
                                                </svg>
                                            </span>
                                            <input type="text" name="coupon_code" class="form-control"
                                                placeholder="CODES ARE CASE-SENSITIVE">
                                        </div>
                                        <p style="font-size: 12px; color: grey;" class="mt-2">Casing & hyphens need to be exact
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
