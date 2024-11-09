let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const hiddenElement = document.getElementById('hidden-variables');
    const hiddenVars = {
        csrfToken: hiddenElement.dataset.csrfToken,
        finalFee: hiddenElement.dataset.feeFinal,
        userEmail: hiddenElement.dataset.userEmail,
        userName: hiddenElement.dataset.userName,
        stripeCustomerId: hiddenElement.dataset.stripeCustomerId,
        eventId: hiddenElement.dataset.eventId,
        stripeKey: hiddenElement.dataset.stripeKey,
        stripeReturnUrl: hiddenElement.dataset.stripeReturnUrl,
        stripeIntentUrl: hiddenElement.dataset.stripeIntentUrl

    };

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
    let paymentProcessor = new PaymentProcessor(hiddenVars['finalFee']);

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
            const response = await fetch(hiddenVars['stripeIntentUrl'], {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify({
                    role: "ORGANIZER",
                    paymentAmount: paymentProcessor.getPaymentAmount(),
                    email: hiddenVars['userEmail'],
                    name: hiddenVars['userName'],
                    stripe_customer_id: hiddenVars['stripeCustomerId'],
                    metadata : {
                        eventId: hiddenVars['eventId']
                    }
                })
            });

            const json = await response.json();
            let spinner = document.getElementById('spinner-element');
            spinner?.remove();
            const clientSecret = json.data.client_secret;
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

            document.getElementById('submit-button-element').classList.remove('d-none');
            document.getElementById('payment-summary').classList.remove('d-none');

        } catch (error) {
            console.error("Error initializing Stripe Card Payment:", error);
        }
    }
    async function finalizeStripeCardPayment(event) {
        event.preventDefault();
        try {
            let addressElement = elements.getElement('address');

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
                    return_url: hiddenVars['stripeReturnUrl'],
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

    let stripe = Stripe(hiddenVars['stripeKey'])
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
            phone: 'always',
        },
    };

    initializeStripeCardPayment();