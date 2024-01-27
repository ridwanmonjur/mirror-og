<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.15.6/dist/sweetalert2.all.min.js"></script>
<script src="https://js.stripe.com/v3/"></script>
<script src="{{ asset('/assets/js/event_creation/event_create.js') }}"></script>
<script src="{{ asset('/assets/js/navbar/toggleNavbar.js') }}"></script>
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
</script>
<script>
    class PaymentProcessor {
        constructor() {
            this.isPaymentSelected = false;
            this.paymentType = null;
            this.paymentElement = null;
            this.nextStepId = null;
        }

        getIsPaymentSelected() {
            return this.isPaymentSelected;
        }

        getPaymentElement() {
            return this.paymentElement;
        }

        getPaymentType() {
            return this.paymentType;
        }

        getNextStepId() {
            return this.nextStepId;
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
                this.setNextStepId();
            } else {
                throw new Error('Invalid value for paymentType. Expected a string.');
            }
        }

        setNextStepId() {
            const stepList = {
                'bank': 'bankLogoId',
                'eWallet': 'eWalletLogoId',
                'otherEWallet': 'otherEWalletLogoId',
                'card': 'cardLogoId',
            };

            this.nextStepId = stepList[this.paymentType];
        }

        reset() {
            this.isPaymentSelected = false;
            this.paymentType = null;
            this.paymentElement = null;
            this.nextStepId = null;
        }
    }

    let paymentProcessor = new PaymentProcessor();

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
        if (paymentProcessor.getNextStepId() == null) {
           return;
        }

        let screenPaymentView = document.getElementById('payment-element-view');
        let checkoutView = document.getElementById('payment-discount-view');
        let allPaymentElements = document.querySelectorAll(".payment-element-children-view");
        let currentElementId = paymentProcessor.getNextStepId();
        let currentElement = document.getElementById(currentElementId);
        
        allPaymentElements?.forEach((_element)=>{
            _element.classList.add('d-none');
        })

        currentElement?.classList.remove('d-none');
        screenPaymentView?.classList.toggle('d-none');
        checkoutView?.classList.toggle('d-none');
    }

    function selectCards() {
        let element1 = document.querySelector('.card-select-view');
        let element2 = document.querySelector('.card-focus-view');
        element1.classList.toggle('d-none');
        element2.classList.toggle('d-none');
    }

    function initializePayment() {
        fetch("{{ route('stripe.createIntent') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    paymentAmount
                })
            })
            .then((data) => data.json())
            .then((json) => {
                let clientSecret = json.data.client_secret;

                elements = stripe.elements({
                    clientSecret,
                    appearance
                });

                const paymentElementOptions = {
                    layout: "tabs",
                };

                const paymentElement = elements.create("payment", paymentElementOptions);
                paymentElement.mount("#card");

                cardForm.addEventListener('submit', async (e) => {
                    e.preventDefault()

                    
                })
            });
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

    let paymentAmount = localStorage.getItem('eventTierPrize');
    paymentAmount = String(paymentAmount);

    if (paymentAmount) {
        paymentAmount = paymentAmount.replace("RM ", "");
        paymentAmount = parseInt(paymentAmount);
        // initializePayment();
    }

</script>

