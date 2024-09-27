
<script src="https://js.stripe.com/v3/"></script>
<script src="{{ asset('/assets/js/organizer/event_creation/event_create.js') }}"></script>
<script src="{{ asset('/assets/js/jsUtils.js') }}"></script>
<script src="{{ asset('/assets/js/models/PaymentProcessor.js') }}"></script>
<script>
    document.querySelectorAll('.transform-number').forEach((element) => {
        let text = element.textContent;
        let number = Number(text);
        element.textContent = number.toLocaleString('en');
    })

    function toggleArrows(event) {
        const element = event.currentTarget;
        const parent = element.parentNode;
        var firstElement = element.querySelector('.bi-chevron-down');
        var secondElement = element.querySelector('.bi-chevron-up');

        if (firstElement) {
            firstElement.classList.toggle('d-none');
        }

        if (secondElement) {
            secondElement.classList.toggle('d-none');
        }
        parent.classList.toggle("squared-box");
        element.classList.toggle("rounded-box");
    }
    let amount = "{{ $amount }}";
    let paymentProcessor = new PaymentProcessor( amount );

    function onChoosePayment(event, type, element) {
        let target = event.currentTarget;
        let classNameList = ["squared-box", "border-2px"];
        let searchQuery = `.payment-element.${classNameList[0]}`;
        let allElements = document.querySelectorAll(searchQuery);

        allElements.forEach((_element) => {
            classNameList.forEach((_class) => {
                _element.classList.remove(`${_class}`);
            })

            if (_element.nextElementSibling &&
                _element.nextElementSibling.classList.contains('check-tick')
            ) {
                _element.nextElementSibling.classList.add("d-none");
            }
        })
        classNameList.forEach((_class) => {
            target.classList.add(`${_class}`);
        })
        if (target.nextElementSibling) target.nextElementSibling.classList.remove("d-none");
        changeButtonColor(type, element);
    }

    function changeButtonColor(type, element) {
        let button = document.querySelector(".payment-button");
        button.classList.add('oceans-gaming-default-button');
        button.classList.remove('oceans-gaming-gray-button');
        paymentProcessor.setIsPaymentSelected(true);
        paymentProcessor.setPaymentType(type);
        paymentProcessor.setPaymentElement(element);
    }

    function changeScreen() {
        if (!paymentProcessor.getIsPaymentSelected()) {
            return;
        } else {
            let screenPaymentView = document.getElementById('payment-element-view');
            let checkoutView = document.getElementById('payment-discount-view');
            let allPaymentElements = document.querySelectorAll(".payment-element-children-view");
            const stepList = {
                'bank': 'bankLogoId',
                'eWallet': 'eWalletLogoId',
                'otherEWallet': 'otherEWalletLogoId',
                'card': 'cardLogoId',
            };

            let currentElementId = stepList[paymentProcessor.getPaymentType()];
            let currentElement = document.getElementById(currentElementId);

            allPaymentElements?.forEach((_element) => {
                _element.classList.add('d-none');
            })
            currentElement?.classList.remove('d-none');
            screenPaymentView?.classList.toggle('d-none');
            checkoutView?.classList.toggle('d-none');

            switch (paymentProcessor.getPaymentType()) {
                case 'bank':
                    break;
                case 'eWallet':
                    break;
                case 'otherEWallet':
                    initializeStripeEWalletPayment();
                    break;
                case 'card':
                    initializeStripeCardPayment();
                    break;
                default:
                    break;
            }
        }
    }

    function selectCards() {
        let element1 = document.querySelector('.card-select-view');
        let element2 = document.querySelector('.card-focus-view');
        element1.classList.toggle('d-none');
        element2.classList.toggle('d-none');
    }

    async function initializeStripeCardPayment() {
        try {
            const response = await fetch("{{ route('stripe.stripeCardIntentCreate') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    paymentAmount: paymentProcessor.getPaymentAmount(),
                    email: "{{ $user->email }}",
                    name: "{{ $user->name }}",
                    stripe_customer_id: "{{ $user->stripe_customer_id }}",
                    role: "PARTICIPANT",
                    metadata : {
                        joinEventId: "{{ $joinEventId }}",
                        memberId: "{{ $memberId }}",
                        teamId: "{{ $teamId }}",
                        eventId: "{{ $event->id }}",
                        eventType: "{{ $event->getRegistrationStatus() }}"
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

                const paymentElement = elements.create("payment", paymentElementOptions);
                const addressElement = elements.create('address', addressElementOptions);

                addressElement.on('change', (event) => {
                    if (event.complete) {
                        const address = event.value.address;
                    }
                })

                paymentElement.mount("#card-element");
                addressElement.mount("#address-element");
                let paymentIntentInput =  document.getElementById('payment_intent_id');
                if (paymentIntentInput) paymentIntentInput.value = paymenntIntentId;
                document.getElementById('discount-element')?.classList.remove('d-none');
                document.getElementById('submit-button-element')?.classList.remove('d-none');
                document.getElementById('payment-summary')?.classList.remove('d-none');
                
            } else {
                window.toastError(json.message);
            }
        } catch (error) {
            window.toastError(error.message);
            console.error("Error initializing Stripe Card Payment:", error);
        }
    }

    function validateInput(input) {
        const amount = parseFloat(input.dataset.amount);
        const walletAmount = parseFloat(input.dataset.wallet);
        let value = parseFloat(input.value);

        if (isNaN(value)) {
            value = 0;
        }

        if (value === amount || value <= amount - 5) {
        } else if (value > amount) {
            value = amount;
        } else {
            value = amount - 5;
        }

        value = Math.min(value, walletAmount);

        input.value = value.toFixed(); 
    }

    async function handleSubmit(event) {
        event.preventDefault();
        const form = event.target;
        let formData = new FormData(form);
        let jsonObject = {};   
        for (let [key, value] of formData.entries()) {
            jsonObject[key] = value;
        }
        const input = form.querySelector('input[name="discount_applied_amount"]');
        validateInput(input);
        
        if (form.checkValidity()) {
            try {
                const response = await fetch("{{ route('stripe.discountCheckout.action') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify(jsonObject)
                });

                const json = await response.json();
                if (json.success) {
                    const {data} = json;

                    if (data.is_payment_completed) {
                        window.location.href = form.dataset.redirect_url;

                    } else {
                        document.getElementById('discount-element').classList.remove('d-none');
                        document.getElementById('submit-button-element').classList.remove('d-none');
                        document.getElementById('payment-summary').classList.remove('d-none');
                        document.getElementById('actualPaymentTable').innerText = data.newAmount;
                        document.getElementById('payment_amount_input').value = data.newAmount;
                        document.getElementById('wallet_amount').innerText = `RM ${data.wallet_amount}`;
                    }
                   
                }
                else {
                    window.toastError(json.message);
                }
            } catch (error) {
                console.error("Error initializing Stripe Card Payment:", error);
            }        
        }
    }

   

    async function finalizeStripeCardPayment(event) {
        event.preventDefault();
        try {
            const addressElement = elements.getElement('address');
            const { complete, value: addressValue } = await addressElement.getValue();

             if (!complete) {
                    toastError("Please fill the complete address");
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
                    return_url: "{{ route('participant.checkout.transition') }}",
                    payment_method_data: {
                        billing_details: billingDetails
                    }
                }
            });
        } catch (error) {
            console.error("Error submitting card form:", error);
        }
    }
    async function initializeStripeEWalletPayment() {

        const expressCheckoutOptions = {
            buttonType: {
                applePay: 'buy',
                googlePay: 'buy',
                paypal: 'buynow'
            }
        }
    }

    let stripe = Stripe('{{ env('STRIPE_KEY') }}')
    const appearance = {
        theme: 'flat',
        variables: {
            colorText: '#30313d',
            colorDanger: '#df1b41',
            fontFamily: 'Ideal Sans, system-ui, sans-serif',
            borderRadius: '0px',
            colorPrimary: 'black',
            colorBackground: '#ffffff',
            borderRadius: '20px' 
        },
        rules: {
            '.Input, .Block': {
                padding: '10px',
                backgroundColor: 'transparent',
                border: '1.5px solid var(--colorPrimary)'
            }
        }
    };
    let paymentElementOptions = {
        type: 'accordion',
    };
    let addressElementOptions = {
        mode: 'billing',
        blockPoBox: true,
        fields: {
            phone: 'never',
        },
    };

    initializeStripeCardPayment();
</script>
