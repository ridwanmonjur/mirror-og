function goToPaymentPage() {
    goToNextScreen('step-payment', 'timeline-payment');
    fillStepPaymentValues();
}

function goToLaunch2ndPage() {
    goToNextScreen('step-launch-2', 'timeline-launch');
}



function setFormValuesAndNavigate(element) {
    let eventType = element.dataset.eventType;
    let eventTypeId = element.dataset.eventTypeId;
    let eventDefinitions = element.dataset.eventDefinitions;
    if (eventType == "League") {
        window.Swal.fire({
            icon: "error",
            title: "Oops...",   
            confirmButtonColor: '#43a4d7',
            text: "Cannot select League structure for now" 
        });
        return;
    }

    setFormValues({'eventType': eventType, 'eventTypeId': eventTypeId});
    goToNextScreen('step-3', 'timeline-1');
    
    document.querySelectorAll('.box_2nd').forEach((el) => {
        el.classList.remove('color-border-success');
    });

    element.querySelector('.box_2nd').classList.add('color-border-success');
    
    let eventTypeTitle = element.querySelector('.inputEventTypeTitle u').innerHTML;
    localStorage.setItem('eventTypeTitle', eventTypeTitle);
    localStorage.setItem('eventTypeDefinition', eventDefinitions);
    localStorage.setItem('eventTypeId', eventTypeId);
}

function handleTierSelection(element) {
    let eventTier = element.dataset.eventTier;
    let eventTierId = element.dataset.eventTierId;
    let eventTierImg = element.querySelector('.inputEventTierImg').src;
    let eventTierPerson = element.querySelector('.inputEventTierPerson').innerHTML;
    let eventTierPrize = element.querySelector('.inputEventTierPrize').innerHTML;
    let eventTierEntry = element.querySelector('.inputEventTierEntry').innerHTML;
    let eventTierTitle = element.querySelector('.inputEventTierTitle').innerHTML;

    setFormValues({'eventTier': eventTier, 'eventTierId': eventTierId});
    
    localStorage.setItem('eventTierPerson', eventTierPerson);
    localStorage.setItem('eventTierPrize', eventTierPrize);
    localStorage.setItem('eventTierImg', eventTierImg);
    localStorage.setItem('eventTierEntry', eventTierEntry);
    localStorage.setItem('eventTierTitle', eventTierTitle);
    localStorage.setItem('eventTierId', eventTierId);
    
    fillStepGameDetailsValues();
    
    document.querySelectorAll('.box-tier').forEach(element => {
        element.classList.remove('color-border-success-dotted');
    });
    element.querySelector('.box-tier').classList.add('color-border-success-dotted');
    
    goToNextScreen('step-4', 'timeline-1');
}

function fillStepPaymentValues() {
    
    const paymentMethodConditionFulfilledButton =
        document.getElementsByClassName('choose-payment-method-condition-fulfilled')[0];
    
    const paymentMethodCondition = document.getElementsByClassName('choose-payment-method')[0];
    
    let eventRate = 20,
        eventSubTotal = 0,
        eventFee = 0,
        eventTotal = 0;
    
    let eventRateToTierList = JSON.parse(document.getElementById("allTiers").value);
  
    let eventRateToTierMap = eventRateToTierList.reduce((accumulator, value) => {
        accumulator[value.eventTier] = value.tierPrizePool;
        return accumulator;
    }, {});

    console.log({eventRateToTierMap});
    console.log({eventRateToTierMap});

    
    let formValues = getFormValues(['eventTier', 'eventType']);
    
    if (
        'eventTier' in formValues &&
        'eventType' in formValues
    ) {

        let eventTier = formValues['eventTier'] ?? null;
        let eventType = formValues['eventType'] ?? null;
        eventSubTotal = eventRateToTierMap[eventTier] ?? -1;
        
        if (eventRate == -1) {
            Toast.fire({
                icon: 'error',
                text: `Invalid event tier or event type!`
            })
        }

        eventFee = eventSubTotal * (eventRate / 100);
        eventTotal = Number(eventSubTotal) + Number(eventFee);
        
        if (eventTier == null || eventType == null || eventSubTotal == -1) {
            getElementByIdAndSetInnerHTML('paymentType', "Not available");
            getElementByIdAndSetInnerHTML('paymentTier', "Not available");
            getElementByIdAndSetInnerHTML('paymentTotal', "Not available");

            if (!paymentMethodCondition.classList.contains("d-none")) {
                paymentMethodCondition.classList.add("d-none");
            }

            if (paymentMethodConditionFulfilledButton.classList.contains("d-none")) {
                paymentMethodConditionFulfilledButton.classList.remove("d-none");
            }
        } else {
            getElementByIdAndSetInnerHTML('paymentType', eventType);
            getElementByIdAndSetInnerHTML('paymentTier', eventTier);
            getElementByIdAndSetInnerHTML('paymentSubtotal', "RM " + numberToLocaleString(eventSubTotal));
            getElementByIdAndSetInnerHTML('paymentRate', `${eventRate}%`);
            getElementByIdAndSetInnerHTML('paymentFee', "RM " + numberToLocaleString(eventFee));
            getElementByIdAndSetInnerHTML('paymentTotal', "RM " + numberToLocaleString(eventTotal));
            
            if (!paymentMethodConditionFulfilledButton.classList.contains("d-none")) {
                paymentMethodConditionFulfilledButton.classList.add("d-none");
            }

            if (paymentMethodCondition.classList.contains("d-none")) {
                paymentMethodCondition.classList.remove("d-none");
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const scrollElements = document.querySelectorAll('.custom-scrollbar2');
    
    function updateScrollState(element) {
        const isAtStart = element.scrollLeft === 0;
        const isAtEnd = element.scrollLeft >= (element.scrollWidth - element.clientWidth - 9);
        
        element.classList.toggle('scroll-at-start', isAtStart);
        element.classList.toggle('scroll-at-end', isAtEnd);
    }
    
    scrollElements.forEach(element => {
        // Set initial state
        updateScrollState(element);
        
        // Listen for scroll events
        element.addEventListener('scroll', function() {
            updateScrollState(this);
        });
        
        // Also listen for resize events in case content changes
        const resizeObserver = new ResizeObserver(() => {
            updateScrollState(element);
        });
        resizeObserver.observe(element);
    });
});