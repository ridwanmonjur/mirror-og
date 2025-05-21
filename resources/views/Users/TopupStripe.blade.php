@extends('layout.app')

<head>
    <link rel="stylesheet" href="{{ asset('/assets/css/common/fullpage.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>
@section('content')
    @include('includes.__Navbar.NavbarGoToSearchPage')
    <div class="d-none" id="payment-variables" data-payment-amount="{{ $amount }}" data-user-email="{{ $user->email }}"
        data-user-name="{{ $user->name }}" data-stripe-customer-id="{{ $user->stripe_customer_id }}"
        data-stripe-key="{{ config('services.stripe.key') }}"
        data-stripe-card-intent-url="{{ route('stripe.stripeCardIntentCreate') }}"
        data-checkout-transition-url="{{ route('wallet.topup') }}">

    </div>
    <div class="row mt-4">
        <div class="mx-auto" style="max-width: 900px;">

            <div class="card">
                <div class="card-header">Topup your wallet</div>

                <div class="card-body">
                    @include('includes.Flash')

                    <div id="cardLogoId" class="payment-element-children-view">
                        <form method="POST" id="stripe-form">
                            <br>
                            <div id="spinner-element" class="d-flex justify-content-center mt-5">
                                <div class="spinner-border text-primary" role="status">
                                    <br><br>
                                    <br><br>
                                    <span class="visually-hidden text-center ">Loading...</span>
                                </div>
                            </div>
                            <div class="row w-100">
                                <div class="col-12 col-lg-6">
                                    <div id="address-element" class="my-2"> </div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div id="card-element" class="my-2"> </div>
                                    <div class="d-none d-lg-block">

                                        <br><br><br><br>
                                    </div>
                                    <div class="d-flex justify-content-center my-3 d-none" id="submit-button-element">
                                        <button class="oceans-gaming-default-button" type="submit"> Submit </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
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
                    border: '1.5px solid var(--colorPrimary)'
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
            const submitButton = event.target;

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
                    return;
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
                        return_url: paymentVars['checkoutTransitionUrl'],
                        payment_method_data: {
                            billing_details: billingDetails
                        }
                    }
                });

                if (error) {
                    window.closeLoading();
                    console.log('Payment confirmation error:', error);
                    window.toastError(error.message || 'Payment failed. Please try again.');
                    submitButton.disabled = false;
                    return;
                }

                window.closeLoading();
                return;
            } catch (error) {
                console.log('Exception caught:', error);

                const errorMessage =
                    error.message ||
                    error.error?.message ||
                    'Failed to process your payment. Please try again later.';
                window.closeLoading();
                window.toastError(errorMessage);
                submitButton.disabled = false;
                return;
            }
        }

        initializeStripeCardPayment();

        let form = document.getElementById('stripe-form');
        form.addEventListener('submit', finalizeStripeCardPayment);

    });
</script>
