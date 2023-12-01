<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.15.6/dist/sweetalert2.all.min.js"></script>
<script src="https://js.stripe.com/v3/"></script>
<script src="{{ asset('/assets/js/event_creation/timeline.js') }}"></script>
<script src="{{ asset('/assets/js/event_creation/event_create.js') }}"></script>
<script src="{{ asset('/assets/js/navbar/toggleNavbar.js') }}"></script>
<!-- Including the Tagify library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.3.0/tagify.min.js"></script>
<script>
    // Initializing Tagify on the input field
    new Tagify(document.querySelector('#eventTags'), {});
</script>
<script>
    
    function checkStringNullOrEmptyAndReturn(value) {
        let _value = String(value).trim();
        return (value === null || value === undefined || _value === "") ? null : _value;
    }

    function fillStepValues() {
        let formValues = getFormValues(['eventTier', 'eventType', 'gameTitle']);
        if (
            // do later this way
            'eventTier' in formValues &&
            'gameTitle' in formValues &&
            'eventType' in formValues
        ) {
            let eventTier = formValues['eventTier'];
            let eventType = formValues['eventType'];
            let gameTitle = formValues['gameTitle'];

            console.log({
                eventTier
            })
            console.log({
                eventType
            })
            console.log({
                gameTitle
            })

            // let inputEGameTitleImg = document.querySelector(`img#inputGameTitle${gameTitle}Img`);
            let outputGameTitleImg = document.querySelector('img#outputGameTitleImg');
            let outputGameTitleImgSrc = localStorage.getItem('gameTitleImg');
            console.log({
                outputGameTitleImgSrc
            })
            console.log({
                outputGameTitleImgSrc
            })
            console.log({
                outputGameTitleImgSrc
            })
            let second = checkStringNullOrEmptyAndReturn(outputGameTitleImgSrc);
            console.log({
                second
            })
            console.log({
                second
            })
            console.log({
                second
            })
            if (outputGameTitleImgSrc != null) {
                console.log({
                    outputGameTitleImgSrc
                })
                console.log({
                    outputGameTitleImgSrc
                })
                console.log({
                    outputGameTitleImgSrc
                })
                outputGameTitleImg.src = outputGameTitleImgSrc;
            }
            let outputEventTypeTitle = document.getElementById('outputEventTypeTitle');
            let outputEventTypeDefinition = document.getElementById('outputEventTypeDefinition');

            let outputEventTypeTitleInnerHTML = checkStringNullOrEmptyAndReturn(localStorage.getItem('eventTypeTitle'));
            if (outputEventTypeTitleInnerHTML != null)
                outputEventTypeTitle.innerHTML = outputEventTypeTitleInnerHTML;

            let outputEventTypeDefinitionInnerHTML = checkStringNullOrEmptyAndReturn(localStorage.getItem('eventTypeDefinition'));
            if (outputEventTypeDefinitionInnerHTML != null)
                outputEventTypeDefinition.innerHTML = outputEventTypeDefinitionInnerHTML;

            let outputEventTierImg = document.querySelector(`img#outputEventTierImg`);
            let outputEventTierTitle = document.getElementById('outputEventTierTitle');
            let outputEventTierPerson = document.getElementById('outputEventTierPerson');
            let outputEventTierPrize = document.getElementById('outputEventTierPrize');
            let outputEventTierEntry = document.getElementById('outputEventTierEntry');

            let outputEventTierImgSrc = checkStringNullOrEmptyAndReturn(localStorage.getItem('eventTierImg'));
            if (outputEventTierImgSrc != null)
                outputEventTierImg.src = outputEventTierImgSrc;

            let outputEventTierPersonInnerHTML = checkStringNullOrEmptyAndReturn(localStorage.getItem('eventTierPerson'));
            if (outputEventTierPersonInnerHTML != null)
                outputEventTierPerson.innerHTML = outputEventTierPersonInnerHTML;

            let outputEventTierPrizeInnerHTML = checkStringNullOrEmptyAndReturn(localStorage.getItem('eventTierPrize'));
            if (outputEventTierPrizeInnerHTML != null)
                outputEventTierPrize.innerHTML = outputEventTierPrizeInnerHTML;

            let outputEventTierEntryInnerHTML = checkStringNullOrEmptyAndReturn(localStorage.getItem('eventTierEntry'));
            if (outputEventTierEntryInnerHTML != null)
                outputEventTierEntry.innerHTML = outputEventTierEntryInnerHTML;

            let outputEventTierTitleInnerHTML = checkStringNullOrEmptyAndReturn(localStorage.getItem('eventTierTitle'));
            if (outputEventTierEntryInnerHTML != null)
                outputEventTierTitle.innerHTML = outputEventTierTitleInnerHTML;

        }
    }

    function checkValidTime() {
        var startDateInput = document.getElementById('startDate');
        var endDateInput = document.getElementById('endDate');
        var startTimeInput = document.getElementById('startTime');
        var endTimeInput = document.getElementById('endTime');
        const startDateInputValue = startDateInput.value;
        const endDateInputValue = endDateInput.value;
        const startTimeInputValue = startTimeInput.value;
        const endTimeInputValue = endTimeInput.value;
        var now = new Date();
        var startDate = new Date(startDateInputValue + " " + startTimeInput.value);
        var endDate = new Date(endDateInput.value + " " + endTimeInput.value);
        if (startDate < now || endDate <= now) {
            Toast.fire({
                icon: 'error',
                text: "Start date or end date cannot be earlier than current time."
            });
            if (startDate < now) {
                startDateInput.value = ""
            } else if (endDate < now) {
                endDateInput.value = ""
            }
        }
        if (startTimeInput.value === "" || endTimeInput.value === "") {
            return;
        }
        if (endDate < startDate) {
            Toast.fire({
                icon: 'error',
                text: "End  and time cannot be earlier than start date and time."
            });
            startDateInput.value = "";
            startTimeInput.value = "";
        }
    }

    function handleFile(inputFileId, previewImageId) {
        var selectedFile = document.getElementById(inputFileId).files[0];
        console.log({
            selectedFile
        })
        console.log({
            selectedFile
        })
        console.log({
            selectedFile
        })
        console.log({
            selectedFile
        })
        console.log({
            selectedFile
        })
        var allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];

        if (!allowedTypes.includes(selectedFile.type)) {
            selectedFile.value = '';
            Toast.fire({
                icon: 'error',
                text: "Invalid file type. Please upload a JPEG, PNG, or JPG file."
            })
        } else previewSelectedImage('eventBanner', 'previewImage');
    }
</script>
<script>
    let stripe = Stripe('{{ env("STRIPE_KEY") }}')
    // const appearance = {
    //     theme: 'stripe',

    //     variables: {
    //         colorPrimary: '#0570de',
    //         colorBackground: '#ffffff',
    //         colorText: '#30313d',
    //         colorDanger: '#df1b41',
    //         fontFamily: 'Ideal Sans, system-ui, sans-serif',
    //         spacingUnit: '2px',
    //         borderRadius: '4px',
    //         border: "10px solid black"
    //         // See all possible variables below
    //     }
    // };
    const elements = stripe.elements();
    const cardElement = elements.create('card', {
        style: {
            base: {
                fontSize: '16px'
            }
        },
        hidePostalCode: true
    })
    const cardForm = document.getElementById('card-form')
    const cardName = document.getElementById('card-name')
    cardElement.mount('#card')
    cardForm.addEventListener('submit', async (e) => {
        e.preventDefault()
        const {
            paymentMethod,
            error
        } = await stripe.createPaymentMethod({
            type: 'card',
            card: cardElement,
            billing_details: {
                name: cardName.value
            }
        })
        if (error) {
            console.log(error)
        } else {
            let input = document.createElement('input')
            input.setAttribute('type', 'hidden')
            input.setAttribute('name', 'payment_method')
            input.setAttribute('value', paymentMethod.id)
            cardForm.appendChild(input)
            // payment method created
            setFormValues({
                'isPaymentDone': true,
                paymentMethod: paymentMethod.id
            });
            goToNextScreen('step-11', 'timeline-4');
            document.getElementById('modal-close').click();
            const form = new FormData(cardForm);
            const data = {};
            form.forEach((value, key) => {
                data[key] = value;
            });
            fetch("{{ route('stripe.organizerTeamPay') }}", {
                    method: "POST",
                    divs: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(responseData => {

                })
                .catch(error => {
                    // Handle errors here
                    console.error(error);
                })
        }
    })

    window.onload = function() {
        let $event = {
            {
                Js::from($event)
            }
        };
        console.log({
            $event
        })
        console.log({
            $event
        })
        console.log({
            $event
        })
        console.log({
            $event
        })
        console.log({
            $event
        })
        console.log({
            $event
        })
        let isCreateEventView = $event == null;
        if (isCreateEventView) {
            ['eventTypeTitle', 'gameTitleImg', 'eventTierPrize', 'eventTierPerson',
                'eventTierTitle', 'eventTierEntry', 'eventTypeDefinition', 'eventTierImg'
            ].forEach((key) => {
                localStorage.removeItem(key);
            });
        } else {
            localStorage.setItem('eventTypeTitle', $event.eventTypeTitle);
            localStorage.setItem('gameTitleImg', $event.gameTitleImg);
            localStorage.setItem('eventTierPrize', $event.eventTierPrize);
            localStorage.setItem('eventTierPerson', $event.eventTierPerson);
            localStorage.setItem('eventTierTitle', $event.eventTierTitle);
        }
    }
    $(document).on("keydown", ":input:not(textarea)", function(event) {
        if (event.key == "Enter") {
            event.preventDefault();
        }
    });


</script>


<!-- Including the Tagify library -->

<script>
    // Initializing Tagify on the input field
    new Tagify(document.querySelector('#eventTags'), {});

    function selectOption(element, label, imageUrl) {
        // Add the selected class to the parent button
        const dropdownButton = element.closest('.dropdown').querySelector('.dropbtn');
        dropdownButton.classList.add('selected');

        // Handle selection logic here
        const selectedLabel = dropdownButton.querySelector('.selected-label');
        const selectedImage = dropdownButton.querySelector('.selected-image img');
        selectedLabel.textContent = label;
        selectedImage.src = imageUrl;

        // Close the dropdown
        closeDropDown(dropdownButton);
    }

    // // Function to close the dropdown
    // function closeDropDown(button) {
    //     const dropdownContent = button.nextElementSibling;
    //     dropdownContent.classList.remove('d-block');
    // }

    // // Function to open the dropdown
    // function openDropDown(button) {
    //     const dropdownContent = button.nextElementSibling;
    //     dropdownContent.classList.add('d-block');
    // }
</script>