@extends('layout.app')

<head>
    <link rel="stylesheet" href="{{ asset('/assets/css/common/settings.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>
@section('content')
    <main class="wallet2">
    @include('includes.Navbar')
    <div class="d-none" id="payment-variables" data-payment-amount="{{ $amount }}" data-user-email="{{ $user->email }}"
        data-user-name="{{ $user->name }}" data-stripe-customer-id="{{ $user->stripe_customer_id }}"
        data-stripe-key="{{ config('services.stripe.key') }}"
        data-stripe-card-intent-url="{{ route('stripe.stripeCardIntentCreate') }}"
        data-checkout-transition-url="{{ route('wallet.topupCallback') }}">

    </div>
    <div class="row mt-4">
        <div class="mx-auto" style="max-width: 900px;">

            <div class="card mb-2  py-1 border border-3  rounded-30px">
                {{-- <div class="card-header">Topup your wallet</div> --}}

                <div class="card-body ">
                <h5 class="my-3  text-center">Wallet Checkout </h5>
                    @include('includes.Flash')

                    <div id="cardLogoId" class="payment-element-children-view">
                            <div id="spinner-element" class="d-flex justify-content-center mt-5">
                                <div class="spinner-border text-primary" role="status">
                                    <br>
                                    <span class="visually-hidden text-center ">Loading...</span>
                                </div>
                            </div>
                            <div class="row w-100">
                                <div class="col-12 col-lg-6">
                                    <div id="address-element" class="my-2"> </div>
                                </div>
                                <div class="col-12 col-lg-6 my-2">
                                    <div id="statistics" class="d-none">
                                        <div class="row my-2">
                                            <p class="my-0 col-6">Current Balance </p>
                                            <span class="my-0 col-6"> RM {{ number_format($wallet->current_balance, 2) }} </span>
                                        </div>
                                        <div class="row my-2">
                                            <p class="my-0 col-6">Topup Amount </p>
                                            <span class="my-0 col-6"> RM {{ number_format($amount, 2)  }} </span>
                                        </div>
                                        <div class="row my-2">
                                            <p class="my-0  col-6">Final Balance </p>
                                            <span class="text-primary my-0 col-6"> RM {{ number_format($wallet->current_balance + $amount, 2) }} </span>
                                        </div>
                                    </div>
                                    <div id="card-element" class="my-2"> </div>
                                    <div class="my-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="" id="save-payment">
                                            <label class="form-check-label" for="save-payment">
                                                Save Payment Information
                                            </label>
                                        </div>
                                        {{-- <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="" id="save-default">
                                            <label class="form-check-label" for="save-default">
                                                Choose as default payment
                                            </label>
                                        </div> --}}
                                    </div>
                                    <div class="d-none d-lg-block">
                                        <br>
                                    </div>
                                    <div class="d-flex justify-content-center my-3 d-none" id="submit-button-element">
                                        <button class="oceans-gaming-default-button" id="stripe-button"> Submit </button>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    </main>
@endsection
<script src="https://js.stripe.com/v3/"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let variablesDiv = document.getElementById('payment-variables');
        console.log({
            variablesDiv
        });
        const paymentVars = {
            paymentAmount: variablesDiv.dataset.paymentAmount,
            userEmail: variablesDiv.dataset.userEmail,
            userName: variablesDiv.dataset.userName,
            stripeCustomerId: variablesDiv.dataset.stripeCustomerId,
            stripeKey: variablesDiv.dataset.stripeKey,
            stripeCardIntentUrl: variablesDiv.dataset.stripeCardIntentUrl,
            checkoutTransitionUrl: variablesDiv.dataset.checkoutTransitionUrl
        };

        let amount = paymentVars['paymentAmount'];

        async function initializeStripeCardPayment() {
            try {
                const response = await fetch(paymentVars['stripeCardIntentUrl'], {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken
                    },
                    body: JSON.stringify({
                        paymentAmount: amount,
                        email: paymentVars['userEmail'],
                        name: paymentVars['userName'],
                        stripe_customer_id: paymentVars['stripeCustomerId'],
                        purpose: 'wallet_topup_' + new Date().toLocaleString('en-US', {
                            year: 'numeric',
                            month: '2-digit', 
                            day: '2-digit',
                            hour: '2-digit',
                            minute: '2-digit'
                        }).replace(/[\/,\s:]/g, '_'),
                        metadata: {
                            type: 'topup',
                            email: paymentVars['userEmail'],
                            name: paymentVars['userName'],
                        }
                    })
                });

                const json = await response.json();
                if (json.success) {
                    let spinner = document.getElementById('spinner-element');
                    spinner?.remove();
                    let statisticsElement = document.getElementById('statistics');
                    statisticsElement?.classList.remove('d-none');
                    const clientSecret = json.data.client_secret;
                    let paymenntIntentId = json.data.payment_intent.id;
                    elements = stripe.elements({
                        clientSecret,
                        appearance
                    });

                    const paymentElement = elements.create("payment", {});
                    const addressElement = elements.create('address', addressElementOptions);

                    addressElement.on('change', (event) => {
                        if (event.complete) {
                            const address = event.value.address;
                        }
                    })

                    paymentElement.mount("#card-element");
                    addressElement.mount("#address-element");
                    let paymentIntentInput = document.getElementById('payment_intent_id');
                    if (paymentIntentInput) paymentIntentInput.value = paymenntIntentId;
                    document.getElementById('discount-element')?.classList.remove('d-none');
                    document.getElementById('submit-button-element')?.classList.remove('d-none');
                    document.getElementById('payment-summary')?.classList.remove('d-none');

                } else {
                    window.toastError(json.message)
                }
            } catch (error) {
                window.toastError(error.message);
                console.error("Error initializing Stripe Card Payment:", error);
            }
        }

        let stripe = Stripe(paymentVars['stripeKey'])
        const appearance = {
            theme: 'flat',
            variables: {
                colorText: '#30313d',
                colorDanger: '#df1b41',
                fontFamily: 'Ideal Sans, system-ui, sans-serif',
                borderRadius: '0px',
                colorPrimary: '#2e4b59',
                colorBackground: '#ffffff',
                borderRadius: '10px'
            },
            rules: {
                '.Input, .Block': {
                    padding: '7px',
                    backgroundColor: 'transparent',
                    border: '1.5px solid black'
                }
            }
        };


        let addressElementOptions = {
            mode: 'billing',
            blockPoBox: true,
            fields: {
                phone: 'never',
            },
            defaultValues: {
                address: {
                    state: 'Kuala Lumpur',
                    line1: '',
                    line2: '',
                    postal_code: '',
                    country: 'MY',
                },
            },
        };

        async function finalizeStripeCardPayment(event) {
            event.preventDefault();
            event.stopPropagation();
            let savePaymentCheck = document.getElementById('save-payment');
            let saveDefaultCheck = document.getElementById('save-default');

            const submitButton = event.currentTarget;
            if (submitButton.disabled) { 
                console.error("Double clicking");console.error("Double clicking");
                return;
            }

            submitButton.disabled = true;
            try {
                window.showLoading();
                const addressElement = elements.getElement('address');
                const {
                    complete,
                    value: addressValue
                } = await addressElement.getValue();

                if (!complete) {
                    window.closeLoading();
                    toastError("Please fill the complete address");
                    submitButton.disabled = false;
                    return false;
                }

                const billingDetails = {
                    address: {
                        city: addressValue.city,
                        country: addressValue.country,
                        line1: addressValue.line1,
                        line2: addressValue.line2,
                        postal_code: addressValue.postal_code,
                        state: addressValue.state
                    },
                    name: addressValue.name,
                    email: addressValue.email,
                    phone: addressValue.phone
                };

                const {
                    error
                } = await stripe.confirmPayment({
                    elements,
                    confirmParams: {
                        return_url: paymentVars['checkoutTransitionUrl'] 
                            + `?savePayment=${savePaymentCheck.checked}` 
                            // + `&saveDefault=${saveDefaultCheck.checked}`
                            ,
                        payment_method_data: {
                            billing_details: billingDetails
                        }
                    }
                });

                console.log({url: paymentVars['checkoutTransitionUrl']});

                if (error) {
                    window.closeLoading();
                    console.log('Payment confirmation error:', error);
                    window.toastError(error.message || 'Payment failed. Please try again.');
                    submitButton.disabled = false;
                    return false;
                }

                // Update payment intent input to show success
                let paymentIntentInput = document.getElementById('payment_intent_id');
                if (paymentIntentInput) paymentIntentInput.value = 'success';

                window.closeLoading();
                return false;
            } catch (error) {
                console.log('Exception caught:', error);

                const errorMessage =
                    error.message ||
                    error.error?.message ||
                    'Failed to process your payment. Please try again later.';
                window.closeLoading();
                window.toastError(errorMessage);
                submitButton.disabled = false;
                return false;
            }

            return false;
        }

        initializeStripeCardPayment();

        let button = document.getElementById('stripe-button');
        button.addEventListener('click', (event) => {
            return finalizeStripeCardPayment(event);
        });

    });
</script>
