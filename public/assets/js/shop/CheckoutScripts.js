class PaymentProcessor {
    constructor(paymentAmount) {
        this.isPaymentSelected = false;
        this.paymentType = null;
        this.paymentElement = null;
        this.paymentAmount = Number(paymentAmount);
    }

    getPaymentAmount() {
        return this.paymentAmount;
    }

    getIsPaymentSelected() {
        // use this to enable "Confirm and Pay"
        return this.isPaymentSelected;
    }

    getPaymentElement() {
        return this.paymentElement;
    }

    getPaymentType() {
        return this.paymentType;
    }

    setIsPaymentSelected(value) {
        if (typeof value === 'boolean') {
            this.isPaymentSelected = value;
        } else {
            throw new Error('Invalid value for isPaymentSelected. Expected a boolean.');
        }
    }

    setPaymentElement(value) {
        if (typeof value === 'string') {
            this.paymentElement = value;
        } else {
            throw new Error('Invalid value for paymentType. Expected a string.');
        }
    }

    setPaymentType(value) {
        if (typeof value === 'string') {
            this.paymentType = value;
        } else {
            throw new Error('Invalid value for paymentType. Expected a string.');
        }
    }

    reset() {
        this.isPaymentSelected = false;
        this.paymentType = null;
        this.paymentElement = null;
    }
}

    let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    let variablesDiv = document.getElementById('payment-variables');
    const paymentVars = {
        paymentAmount: variablesDiv.dataset.paymentAmount,
        totalFee: variablesDiv.dataset.totalFee,
        userEmail: variablesDiv.dataset.userEmail,
        userName: variablesDiv.dataset.userName,
        stripeCustomerId: variablesDiv.dataset.stripeCustomerId,
        cartTotal: variablesDiv.dataset.cartTotal,
        couponCode: variablesDiv.dataset.couponCode,
        stripeKey: variablesDiv.dataset.stripeKey,
        stripeCardIntentUrl: variablesDiv.dataset.stripeCardIntentUrl,
        checkoutTransitionUrl: variablesDiv.dataset.checkoutTransitionUrl
    };

    console.log({paymentVars});

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
    let amount = paymentVars['paymentAmount'];
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
            const paymentAmount = paymentProcessor.getPaymentAmount(); // Keep in RM, backend will convert to cents
            console.log('Payment Amount (RM):', paymentAmount);
            console.log('Payment Vars:', paymentVars);

            const response = await fetch(paymentVars['stripeCardIntentUrl'], {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify({
                    paymentAmount: paymentAmount,
                    totalFee: paymentVars['totalFee'],
                    email: paymentVars['userEmail'],
                    name: paymentVars['userName'],
                    capture_method: 'manual',
                    stripe_customer_id: paymentVars['stripeCustomerId'],
                    role: "SHOP",
                    metadata : {
                        cartTotal: paymentVars['cartTotal'],
                        couponCode: paymentVars['couponCode'] || null
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
                window.toastError(json.message)
            }
        } catch (error) {
            // window.toastError(error.message);
            console.error("Error initializing Stripe Card Payment:", error);
        }
    }

    function validateInput(input) {
        const amount = +input.dataset.amount;
        const walletAmount = +input.dataset.wallet;
        let value = +input.value;
        console.log({amount, walletAmount,value});

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

        input.value = value.toFixed(2); 
    }

    async function handleSubmit(event) {
        event.preventDefault();
        const form = event.currentTarget;
        form.submit();
    }

   
    

    async function finalizeStripeCardPayment(event) {
        event.preventDefault();
        const submitButton = event.currentTarget;
        let savePaymentCheck = document.getElementById('save-payment');
        let saveDefaultCheck = document.getElementById('save-default');

        submitButton.disabled = true;
        try {
            window.showLoading();
            const addressElement = elements.getElement('address');
            const { complete, value: addressValue } = await addressElement.getValue();

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
                    return_url: paymentVars['checkoutTransitionUrl'] 
                        + `?savePayment=${savePaymentCheck.checked}` 
                        // + `&saveDefault=${saveDefaultCheck.checked}`
                        ,
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
        } catch (error) {
            console.log('Exception caught:', error);
    
            const errorMessage = 
                error.message || 
                error.error?.message || 
                'Failed to process your payment. Please try again later.';
            window.closeLoading();
            window.toastError(errorMessage);
            submitButton.disabled = false;

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

    initializeStripeCardPayment();