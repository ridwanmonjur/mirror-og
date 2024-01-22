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
    function checkStringNullOrEmptyAndReturn(value) {
        if (value === null || value === undefined) return null;

        let _value = String(value).trim();
        return (_value === "") ? null : _value;
    }

    function checkStringNullOrEmptyAndReturnFromLocalStorage(key) {
        let item = localStorage.getItem(key);
        return checkStringNullOrEmptyAndReturn(item);
    }

    function setInnerHTMLFromLocalStorage(key, element) {
        let value = checkStringNullOrEmptyAndReturnFromLocalStorage(key);
        if (value) element.innerHTML = value;
        else console.error(`Item not in localStorage: ${key} ${value}`)
    }

    function setImageSrcFromLocalStorage(key, element) {
        let value = checkStringNullOrEmptyAndReturnFromLocalStorage(key);
        if (value && element) element.src = value;
        else console.error(`Can't set image for: ${key}, ${value} ${value}`)
    }

    function setLocalStorageFromEventObject(key, property) {
        let value = checkStringNullOrEmptyAndReturn(property);
        if (value) localStorage.setItem(key, value);
        else console.error(`Item not in localStorage: ${key} ${value}`)
    }
</script>

<script>
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

    function clearLocalStorage() {
        localStorage.clear();
    }
</script>

