<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.15.6/dist/sweetalert2.all.min.js"></script>
<script src="https://js.stripe.com/v3/"></script>
<script src="{{ asset('/assets/js/event_creation/timeline.js') }}"></script>
<script src="{{ asset('/assets/js/event_creation/event_create.js') }}"></script>
<script src="{{ asset('/assets/js/navbar/toggleNavbar.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.3.0/tagify.min.js"></script>

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
    }

    function setImageSrcFromLocalStorage(key, element) {
        let value = checkStringNullOrEmptyAndReturnFromLocalStorage(key);
        if (value) element.src = value;
    }

    function setLocalStorageFromEventObject(key, property) {
        let value = checkStringNullOrEmptyAndReturn(property);
        console.log({
            value
        })
        console.log({
            value
        })
        console.log({
            value
        })
        console.log({
            value
        })
        console.log({
            value
        })
        if (value) localStorage.setItem(key, value);
    }

    function fillStepValues() {
        let formValues = getFormValues(['eventTier', 'eventType', 'gameTitle']);
        if (
            'eventTier' in formValues &&
            'gameTitle' in formValues &&
            'eventType' in formValues
        ) {
            let eventTier = formValues['eventTier'];
            let eventType = formValues['eventType'];
            let gameTitle = formValues['gameTitle'];

            // Game Title
            // let inputEGameTitleImg = document.querySelector(`img#inputGameTitle${gameTitle}Img`);
            let outputGameTitleImg = document.querySelector('img#outputGameTitleImg');
            // let outputGameTitleImgSrcImg = checkStringNullOrEmptyAndReturnFromLocalStorage('gameTitleImg');
            // if (outputGameTitleImgSrcImg) {
            //     outputGameTitleImg.src = outputGameTitleImgSrcImg;
            // 
            setImageSrcFromLocalStorage('gameTitleImg', outputGameTitleImg);
        }

        // Event Type
        let outputEventTypeTitle = document.getElementById('outputEventTypeTitle');
        let outputEventTypeDefinition = document.getElementById('outputEventTypeDefinition');
        setInnerHTMLFromLocalStorage('eventTypeTitle', outputEventTypeTitle);

        // let outputEventTypeDefinitionInnerHTML = checkStringNullOrEmptyAndReturnFromLocalStorage('eventTypeDefinition');
        // if (outputEventTypeDefinitionInnerHTML)
        //     outputEventTypeDefinition.innerHTML = outputEventTypeDefinitionInnerHTML;
        setInnerHTMLFromLocalStorage('eventTypeDefinition', outputEventTypeDefinition);

        // Event Tier
        let outputEventTierImg = document.querySelector(`img#outputEventTierImg`);
        let outputEventTierTitle = document.getElementById('outputEventTierTitle');
        let outputEventTierPerson = document.getElementById('outputEventTierPerson');
        let outputEventTierPrize = document.getElementById('outputEventTierPrize');
        let outputEventTierEntry = document.getElementById('outputEventTierEntry');

        // let outputEventTierImgSrc = checkStringNullOrEmptyAndReturnFromLocalStorage('eventTierImg');
        // if (outputEventTierImgSrc)
        //     outputEventTierImg.src = outputEventTierImgSrc;
        setImageSrcFromLocalStorage('eventTierImg', outputEventTierImg);
        setInnerHTMLFromLocalStorage('eventTierPerson', outputEventTierPerson);
        setInnerHTMLFromLocalStorage('eventTierPrize', outputEventTierPrize);
        setInnerHTMLFromLocalStorage('eventTierEntry', outputEventTierEntry);
        setInnerHTMLFromLocalStorage('eventTierTitle', outputEventTierTitle);

        // let outputEventTierPersonInnerHTML = checkStringNullOrEmptyAndReturnFromLocalStorage('eventTierPerson');
        // if (outputEventTierPersonInnerHTML)
        //     outputEventTierPerson.innerHTML = outputEventTierPersonInnerHTML;

        // let outputEventTierPrizeInnerHTML = checkStringNullOrEmptyAndReturnFromLocalStorage('eventTierPrize');
        // if (outputEventTierPrizeInnerHTML)
        //     outputEventTierPrize.innerHTML = outputEventTierPrizeInnerHTML;

        // let outputEventTierEntryInnerHTML = checkStringNullOrEmptyAndReturnFromLocalStorage('eventTierEntry');
        // if (outputEventTierEntryInnerHTML)
        //     outputEventTierEntry.innerHTML = outputEventTierEntryInnerHTML;

        // let outputEventTierTitleInnerHTML = checkStringNullOrEmptyAndReturnFromLocalStorage('eventTierTitle');
        // if (outputEventTierEntryInnerHTML)
        //     outputEventTierTitle.innerHTML = outputEventTierTitleInnerHTML;
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
            Toast.fire({
                icon: 'success',
                text: "Payment succeeded. Please proceed to the next step."
            })
            let paymentDiv = document.querySelector('.choose-payment-method');
            paymentDiv.style.backgroundColor = 'green';
            paymentDiv.textContent = 'Payment successful';
            paymentDiv.removeAttribute('data-bs-toggle');
            paymentDiv.removeAttribute('data-bs-target');
            // goToNextScreen('step-11', 'timeline-4');
            document.getElementById('modal-close').click();
            const form = new FormData(cardForm);
            const data = {};
            form.forEach((value, key) => {
                data[key] = value;
            });
            fetch("{{ route('stripe.organizerTeamPay') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(responseData => {
                    console.log(responseData);
                    if (responseData.success) {

                    } else {
                        Toast.fire({
                            icon: 'error',
                            text: "Payment failed unfortunately due to unknown reasons."
                        })
                    }
                })
                .catch(error => {
                    console.error(error);
                })
        }
    })

    function clearLocalStorage() {
        ['eventTypeTitle', 'gameTitleImg', 'eventTierPrize', 'eventTierPerson',
            'eventTierTitle', 'eventTierEntry', 'eventTypeDefinition', 'eventTierImg',
            'eventBanner'
        ].forEach((key) => {
            localStorage.removeItem(key);
        });
    }

    window.onload = function() {
        /* beautify preserve:start */
        let $event = {!! json_encode($event) !!};
        /* beautify preserve:start */
        clearLocalStorage();
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
        if ($event) {

            let assetKeyWord = "{{asset('')}}"
            // game
            setLocalStorageFromEventObject('gameTitleImg', assetKeyWord+ 'storage/' + $event?.game?.gameIcon);
            // event type
            setLocalStorageFromEventObject('eventTypeTitle', $event?.type?.eventType);
            setLocalStorageFromEventObject('eventTypeDefinition', $event?.type?.eventDefinitions);
            // tier
            setLocalStorageFromEventObject('eventTierImg', assetKeyWord+ 'storage/' + $event?.tier?.tierIcon);
            setLocalStorageFromEventObject('eventTierPerson', $event?.tier?.tierTeamSlot);
            setLocalStorageFromEventObject('eventTierPrize', $event?.tier?.tierPrizePool);
            setLocalStorageFromEventObject('eventTierEntry', $event?.tier?.tierEntryFee);
            setLocalStorageFromEventObject('eventBanner', $event?.eventBanner);
            // banner
            setLocalStorageFromEventObject('eventTierTitle', $event?.tier?.eventTier);
            setFormValues({
                'gameTitleId': $event?.game?.id,
                'eventTypeId': $event?.type?.id,
                'eventTierId': $event?.tier?.id,
                'eventTier': $event?.tier?.eventTier,
                'eventType': $event?.type?.eventType,
                'gameTitle': $event?.game?.gameTitle,
                'eventTags': $event?.eventTags,
                'eventDescription': $event?.eventDescription,
                'eventName': $event?.eventName ?? '',
                'startDate': $event?.startDate ?? '',
                'endDate': $event?.endDate ?? '',
                'startTime': $event?.startTime,
                'endTime': $event?.endTime,
                'sub_action_public': $event?.sub_action_public,
                'sub_action_private': $event?.sub_action_private,
                'sub_action_team': $event?.sub_action_team,
            });
            new Tagify(document.querySelector('#eventTags'), [...$event?.eventTags] ?? []);

            // tagify.addTags(["banana", "orange", "apple"])

            // <input type="hidden" name="livePreview" id="livePreview" value="false">
            //             <input type="hidden" name="gameTitle" id="gameTitle">
            //             <input type="hidden" name="eventTier" id="eventTier">
            //             <input type="hidden" name="eventType"  id="eventType">
            //             <input type="hidden" name="gameTitleId" id="gameTitleId">
            //             <input type="hidden" name="eventTierId" id="eventTierId">
            //             <input type="hidden" name="eventTypeId"  id="eventTypeId">
            //             <input type="hidden" name="isPaymentDone"  id="isPaymentDone">
            //             <input type="hidden" name="paymentMethod"  id="paymentMethod">
        }
        else{
            new Tagify(document.querySelector('#eventTags'), {});
        }
    }
    $(document).on("keydown", ":input:not(textarea)", function(event) {
        if (event.key == "Enter") {
            event.preventDefault();
        }
    });
</script>



<script>
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