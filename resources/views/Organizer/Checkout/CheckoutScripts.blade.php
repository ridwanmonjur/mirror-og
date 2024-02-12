<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.15.6/dist/sweetalert2.all.min.js"></script>
<script src="https://js.stripe.com/v3/"></script>
<script src="{{ asset('/assets/js/event_creation/event_create.js') }}"></script>
<script src="{{ asset('/assets/js/navbar/toggleNavbar.js') }}"></script>
<script src="{{ asset('/assets/js/models/PaymentProcessor.js') }}"></script>

<script>
    document.querySelectorAll('.transform-number').forEach((element)=>{
        console.log({element})
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

    let paymentProcessor = new PaymentProcessor({{$fee['finalFee']}});

    function onChoosePayment(event, type, element) {
        let target = event.currentTarget;
        let classNameList = ["squared-box", "border-2px"];
        let searchQuery = `.payment-element.${classNameList[0]}`; 
        let allElements = document.querySelectorAll(searchQuery);
        
        allElements.forEach((_element)=>{
            classNameList.forEach((_class)=> {
                _element.classList.remove(`${_class}`);
            })
            
            if (_element.nextElementSibling && 
                _element.nextElementSibling.classList.contains('check-tick')
            ) {
                _element.nextElementSibling.classList.add("d-none");
            }
        })

        classNameList.forEach((_class)=> {
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
           console.log({paymentProcessor, ended: true});
           return;
        } else {
           console.log({paymentProcessor, ended: false});

            let screenPaymentView = document.getElementById('payment-element-view');
            let checkoutView = document.getElementById('payment-discount-view');
            let allPaymentElements = document.querySelectorAll(".payment-element-children-view");

            const stepList = {
                'bank': 'bankLogoId',
                'eWallet': 'eWalletLogoId',
                'otherEWallet': 'otherEWalletLogoId',
                'card': 'cardLogoId',
            };      
            console.log({stepList})
            let currentElementId  = stepList[paymentProcessor.getPaymentType()];
            console.log({stepList, currentElementId});

            let currentElement = document.getElementById(currentElementId);
            
            allPaymentElements?.forEach((_element)=>{
                _element.classList.add('d-none');
            })

            currentElement?.classList.remove('d-none');
            screenPaymentView?.classList.toggle('d-none');
            checkoutView?.classList.toggle('d-none');

            switch (paymentProcessor.getPaymentType()) {
                case 'bank':
                    // Do something for 'bank'
                    console.log('Bank case');
                    break;
                case 'eWallet':
                    // Do something for 'eWallet'
                    console.log('eWallet case');
                    break;
                case 'otherEWallet':
                    // Do something for 'otherEWallet'
                    initializeApplePayment();
                    break;
                case 'card':
                    initializeStripeCardPayment();
                    break;
                default:
                    // Default case
                    console.log('Default case');
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
                    paymentAmount: paymentProcessor.getPaymentAmount()
                })
            });
            
            const json = await response.json();
            const clientSecret = json.data.client_secret;
            elements = stripe.elements({ clientSecret, appearance});
            const paymentElementOptions = { layout: "tabs" };
            const paymentElement = elements.create("payment", paymentElementOptions);
            paymentElement.mount("#card");
        } catch (error) {
            console.error("Error initializing Stripe Card Payment:", error);
        }
    }

    async function finalizeStripeCardPayment(event) {
        event.preventDefault();

        try {
            const response = await fetch("{{ route('stripe.organizerTeamPay') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    paymentAmount: paymentProcessor.getPaymentAmount()
                })
            });

            const json = await response.json();
        } catch (error) {
            console.error("Error submitting card form:", error);
        }
    }

    function initializeApplePayment() {
        const expressCheckoutOptions = {
        buttonType: {
            applePay: 'buy',
            googlePay: 'buy',
            paypal: 'buynow'
          }
        }
        
        const elements = stripe.elements({
            locale: 'sg',
            mode: 'payment',
            amount: aymentProcessor.getPaymentAmount(),
            currency: 'myr',
        })
        const expressCheckoutElement = elements.create(
            'expressCheckout',
            expressCheckoutOptions
        )
        expressCheckoutElement.mount('#express-apple-checkout-element')
    }

    let stripe = Stripe('{{ env('STRIPE_KEY') }}')
    const appearance = {
        theme: 'flat',
        variables: {
            colorText: '#30313d',
            colorDanger: '#df1b41',
            fontFamily: 'Ideal Sans, system-ui, sans-serif',
            spacingUnit: '2px',
            borderRadius: '20px',
            colorPrimary: 'black',
            colorBackground: '#ffffff',

        },
        rules: {
            '.Input': {
                borderColor: '#E0E6EB',
            },

        }
    };
    const loader = 'auto';
    const cardForm = document.getElementById('card-form')
    const cardName = document.getElementById('card-name')


</script>

