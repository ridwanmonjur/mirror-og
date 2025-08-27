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
        paymentAmount,
        totalFee,
        userEmail,
        userName,
        stripeCustomerId,
        cartTotal,
        cartId,
        couponCode,
        stripeKey,
        stripeCardIntentUrl,
        checkoutTransitionUrl,
        hasPhysicalProducts
    }  = variablesDiv?.dataset ?? {} ;

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
                    purpose: 'shop_purchase_' + paymentVars['cartId'],
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
                const billingAddressElement = elements.create('address', addressElementOptions);
                let shippingAddressElement = null;
                
                // Only create shipping element if there are physical products
                if (paymentVars.hasPhysicalProducts === 'true') {
                    shippingAddressElement = elements.create('address', {
                        ...addressElementOptions,
                        mode: 'shipping'
                    });
                }

                billingAddressElement.on('change', (event) => {
                    if (event.complete) {
                        const address = event.value.address;
                    }
                })

                if (shippingAddressElement) {
                    shippingAddressElement.on('change', (event) => {
                        if (event.complete) {
                            const address = event.value.address;
                        }
                    })
                }

                paymentElement.mount("#card-element");
                billingAddressElement.mount("#billing-address-element");
                
                if (shippingAddressElement) {
                    shippingAddressElement.mount("#shipping-address-element");
                    window.shippingAddressElementRef = shippingAddressElement;
                }
                
                window.billingAddressElementRef = billingAddressElement;
                let paymentIntentInput =  document.getElementById('payment_intent_id');
                if (paymentIntentInput) paymentIntentInput.value = paymenntIntentId;
                document.getElementById('discount-element')?.classList.remove('d-none');
                document.getElementById('submit-button-element')?.classList.remove('d-none');
                document.getElementById('payment-summary')?.classList.remove('d-none');
                
                // Show billing-shipping container and save payment info when spinner disappears
                document.querySelector('.billing-shipping-container')?.classList.remove('d-none');
                document.querySelector('.payment-method-container')?.classList.remove('d-none');
                document.getElementById('save-payment-container')?.classList.remove('d-none');
                
            } else {
                window.toastError(json.message)
            }
        } catch (error) {
            window.toastError(error.message);
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
        if (submitButton.disabled) { 
          console.error("Double clicking");console.error("Double clicking");
            return;
        }

        submitButton.disabled = true;
        try {
            window.showLoading();
            const billingAddressElement = document.querySelector('#billing-address-element iframe');
            const shippingAddressElement = document.querySelector('#shipping-address-element iframe');
            
            let billingResult, shippingResult;
            
            try {
                billingResult = await window.billingAddressElementRef.getValue();
                
                // Only get shipping result if there are physical products
                if (paymentVars.hasPhysicalProducts === 'true' && window.shippingAddressElementRef) {
                    shippingResult = await window.shippingAddressElementRef.getValue();
                } else {
                    // For digital products, use billing address as shipping address
                    shippingResult = billingResult;
                }
            } catch (error) {
                console.error('Error getting address values:', error);
                window.closeLoading();
                toastError("Error processing address information");
                submitButton.disabled = false;
                return;
            }

             if (!billingResult.complete) {
                window.closeLoading();
                toastError("Please fill the complete billing address");
                submitButton.disabled = false;
                return;
            }

            // Only validate shipping address if there are physical products
            if (paymentVars.hasPhysicalProducts === 'true' && !shippingResult.complete) {
                window.closeLoading();
                toastError("Please fill the complete shipping address");
                submitButton.disabled = false;
                return;
            }

            const billingDetails = {
                address: {
                    city: billingResult.value.city,
                    country: billingResult.value.country,
                    line1: billingResult.value.line1,
                    line2: billingResult.value.line2,
                    postal_code: billingResult.value.postal_code,
                    state: billingResult.value.state
                },
                name: billingResult.value.name,
                email: billingResult.value.email,
                phone: billingResult.value.phone
            };

            const shippingDetails = {
                address: {
                    city: shippingResult.value.city,
                    country: shippingResult.value.country,
                    line1: shippingResult.value.line1,
                    line2: shippingResult.value.line2,
                    postal_code: shippingResult.value.postal_code,
                    state: shippingResult.value.state
                },
                name: shippingResult.value.name,
                email: shippingResult.value.email,
                phone: shippingResult.value.phone
            };

            const confirmParams = {
                return_url: paymentVars['checkoutTransitionUrl'] 
                    + `?savePayment=${savePaymentCheck.checked}` 
                    // + `&saveDefault=${saveDefaultCheck.checked}`
                    ,
                payment_method_data: {
                    billing_details: billingDetails
                }
            };

            // Only include shipping if there are physical products
            if (paymentVars.hasPhysicalProducts === 'true') {
                confirmParams.shipping = shippingDetails;
            }

            const {
                error
            } = await stripe.confirmPayment({
                elements,
                confirmParams
            });

            if (error) {
                window.closeLoading();
                console.log('Payment confirmation error:', error);
                window.toastError(error.message || 'Payment failed. Please try again.');
                submitButton.disabled = false;
                return;
            }

            // Update payment intent input to show success
            let paymentIntentInput = document.getElementById('payment_intent_id');
            if (paymentIntentInput) paymentIntentInput.value = 'success';

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

    function initializeBillingShippingToggle() {
        const billingHeader = document.querySelector('.billing-header');
        const shippingHeader = document.querySelector('.shipping-header');
        const billingSection = document.querySelector('#billing-address-element');
        const shippingSection = document.querySelector('#shipping-address-element');
        const billingArrow = billingHeader?.querySelector('.arrow-icon');
        const shippingArrow = shippingHeader?.querySelector('.arrow-icon');

        // Set initial state - billing expanded by default
        updateHeaderState(billingHeader, billingArrow, true);
        updateHeaderState(shippingHeader, shippingArrow, false);

        if (billingHeader && shippingHeader && billingSection && shippingSection) {
            billingHeader.addEventListener('click', function() {
                const isBillingExpanded = !billingSection.classList.contains('d-none');
                const isShippingExpanded = !shippingSection.classList.contains('d-none');

                // Always give click feedback
                addTextInvisibilityFeedback(billingHeader);

                if (!isBillingExpanded) {
                    // Show billing, hide shipping
                    billingSection.classList.remove('d-none');
                    shippingSection.classList.add('d-none');
                    updateHeaderState(billingHeader, billingArrow, true);
                    updateHeaderState(shippingHeader, shippingArrow, false);
                } else if (isShippingExpanded) {
                    // Only collapse billing if shipping is expanded
                    // Hide billing, show shipping
                    billingSection.classList.add('d-none');
                    shippingSection.classList.remove('d-none');
                    updateHeaderState(billingHeader, billingArrow, false);
                    updateHeaderState(shippingHeader, shippingArrow, true);
                }
                // If billing is expanded and shipping is collapsed, do nothing except feedback
            });

            shippingHeader.addEventListener('click', function() {
                const isBillingExpanded = !billingSection.classList.contains('d-none');
                const isShippingExpanded = !shippingSection.classList.contains('d-none');

                // Always give click feedback
                addTextInvisibilityFeedback(shippingHeader);

                if (!isShippingExpanded) {
                    // Show shipping, hide billing
                    shippingSection.classList.remove('d-none');
                    billingSection.classList.add('d-none');
                    updateHeaderState(shippingHeader, shippingArrow, true);
                    updateHeaderState(billingHeader, billingArrow, false);
                } else if (isBillingExpanded) {
                    // Only collapse shipping if billing is expanded
                    // Hide shipping, show billing
                    shippingSection.classList.add('d-none');
                    billingSection.classList.remove('d-none');
                    updateHeaderState(shippingHeader, shippingArrow, false);
                    updateHeaderState(billingHeader, billingArrow, true);
                }
                // If shipping is expanded and billing is collapsed, do nothing except feedback
            });
        }
    }

    function updateHeaderState(header, arrow, isExpanded) {
        if (!header || !arrow) return;

        if (isExpanded) {
            header.classList.add('text-primary', 'border-bottom', 'border-primary');
            header.classList.remove('border-secondary');
            header.style.borderBottom = '1px solid var(--bs-primary)';
            arrow.classList.remove('arrow-collapsed');
            arrow.style.color = '';
        } else {
            header.classList.remove('text-primary', 'border-bottom', 'border-primary');
            header.classList.add('border-bottom', 'border-secondary');
            header.style.borderBottom = '1px solid var(--bs-secondary)';
            arrow.classList.add('arrow-collapsed');
            arrow.style.color = '#6c757d';
        }
    }

    function addClickFeedback(element) {
        element.style.transform = 'scale(0.98)';
        element.style.transition = 'transform 0.1s ease';
        
        setTimeout(() => {
            element.style.transform = 'scale(1)';
        }, 100);
    }

    function addTextInvisibilityFeedback(element) {
        element.style.opacity = '0';
        element.style.transition = 'opacity 0.85s ease';
        
        setTimeout(() => {
            element.style.opacity = '1';
        }, 150);
    }

    function initializePaymentMethodToggle() {
        const walletHeader = document.querySelector('.wallet-payment-header');
        const cardHeader = document.querySelector('.card-payment-header');
        const walletSection = document.querySelector('#discount-element');
        const cardSection = document.querySelector('#card-element');
        const walletArrow = walletHeader?.querySelector('.arrow-icon');
        const cardArrow = cardHeader?.querySelector('.arrow-icon');

        // Set initial state - wallet expanded by default
        updateHeaderState(walletHeader, walletArrow, true);
        updateHeaderState(cardHeader, cardArrow, false);

        if (walletHeader && cardHeader && walletSection && cardSection) {
            walletHeader.addEventListener('click', function() {
                const isWalletExpanded = !walletSection.classList.contains('d-none');
                const isCardExpanded = !cardSection.classList.contains('d-none');

                // Always give click feedback
                addTextInvisibilityFeedback(walletHeader);

                if (!isWalletExpanded) {
                    // Show wallet, hide card
                    walletSection.classList.remove('d-none');
                    cardSection.classList.add('d-none');
                    updateHeaderState(walletHeader, walletArrow, true);
                    updateHeaderState(cardHeader, cardArrow, false);
                } else if (isCardExpanded) {
                    // Only collapse wallet if card is expanded
                    // Hide wallet, show card
                    walletSection.classList.add('d-none');
                    cardSection.classList.remove('d-none');
                    updateHeaderState(walletHeader, walletArrow, false);
                    updateHeaderState(cardHeader, cardArrow, true);
                }
                // If wallet is expanded and card is collapsed, do nothing except feedback
            });

            cardHeader.addEventListener('click', function() {
                const isWalletExpanded = !walletSection.classList.contains('d-none');
                const isCardExpanded = !cardSection.classList.contains('d-none');

                // Always give click feedback
                addTextInvisibilityFeedback(cardHeader);

                if (!isCardExpanded) {
                    // Show card, hide wallet
                    cardSection.classList.remove('d-none');
                    walletSection.classList.add('d-none');
                    updateHeaderState(cardHeader, cardArrow, true);
                    updateHeaderState(walletHeader, walletArrow, false);
                } else if (isWalletExpanded) {
                    // Only collapse card if wallet is expanded
                    // Hide card, show wallet
                    cardSection.classList.add('d-none');
                    walletSection.classList.remove('d-none');
                    updateHeaderState(cardHeader, cardArrow, false);
                    updateHeaderState(walletHeader, walletArrow, true);
                }
                // If card is expanded and wallet is collapsed, do nothing except feedback
            });
        }
    }

    addOnLoad(()=> {
        initializeStripeCardPayment();
        initializeBillingShippingToggle();
        initializePaymentMethodToggle();
    });