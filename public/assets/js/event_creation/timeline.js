function validateFormValuesEmpty(values) {
    let isFormValid = true;

    var createEventForm = document.forms['create-event-form'];

    if (!createEventForm) {
        return false;
    }

    for (var key of values) {
        var formField = createEventForm.elements[key];
        if (formField) {
            console.log({ formField, formFieldValue: formField.value })
            var value = formField.value.trim();
            if (value === '') {
                isFormValid = false;
            }
        }
        else {
            isFormValid = false;
        }
    }
    return isFormValid;
}

function setFormValues(values) {
    var createEventForm = document.forms['create-event-form'];

    if (createEventForm) {
        for (var key in values) {
            console.log(key);
            console.log(values[key]);
            var formField = createEventForm.elements[key];
            if (formField) {
                console.log({ formField, formFieldValue: formField.value })
                formField.value = values[key];
                console.log({ formField: formField.value })

            } else {
                console.error('Form field with name "' + formField + '" not found.');
            }
        }
        console.log([...formData.entries()]);
    }

}

function getFormValues(keyList) {
    let allFormkeyList = {};
    var createEventForm = document.forms['create-event-form'];

    if (!createEventForm) {
        return false;
    }

    for (var key of keyList) {
        var formField = createEventForm.elements[key];
        if (formField) {
            var value = formField.value;
            allFormkeyList[key] = value;
        }

    }
    return allFormkeyList;
}


function closeDropDown(element, id, keyValues, key) {
    element.parentElement.previousElementSibling.classList.toggle("dropbtn-open");
    element.parentElement.classList.toggle("d-none");
    document.getElementById(id).innerHTML = `
    Selected type (${keyValues[key]})
    <span class="dropbtn-arrow">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down">
            <polyline points="6 9 12 15 18 9"></polyline>
        </svg>
    </span>
    `;
    setFormValues(keyValues)
}


function getElementByIdAndSetInnerHTML(id, innerHTML) {
    let documentElement = document.getElementById(id);
    if (documentElement) {  
        documentElement.innerHTML = innerHTML;
    }
    else {
        console.error(`Element with id ${id} not found`);
    }
}


{/* <div>&nbsp;&nbsp;&nbsp;&nbsp;Type: <span id="paymentType"> </span></div>
<br>
<div class="flexbox">
    <span>Subtotal</span>
    <span id="paymentSubtotal"id="subtotal"></span>
</div>
<div class="flexbox">
    <span>Event Creation Fee Rate</span>
    <span id="paymentRate"></span>
</div>
<div class="flexbox">
    <span>Event Creation Fee total</span>
    <span id="paymentFee"></span>
</div>
<br>
<div class="flexbox">
    <h5> TOTAL </h5>
    <h5 id="paymentTotal"></h5>
</div> */}

function numberToLocaleString(number){
   return Number(number).toLocaleString()
}

function goToNextScreen(nextId, nextTimeline) {
    document.getElementsByClassName("navbar")[0].scrollIntoView({ behavior: 'smooth' });
    const allIDs = [
        'step-0',
        'step-1', 'step-2', 'step-3', 'step-4'];

    const allTimelines = ['timeline-1', 'timeline-2', 'timeline-3', 'timeline-4'];

    let isFormValid = true;

    if (nextTimeline == allTimelines[2]) {
        let eventRate = 20, eventSubTotal = 0, eventFee = 0, eventTotal = 0;
        let eventRateToTierMap = { 'Starfish': 5000, 'Turtle': 10000, 'Dolphin': 15000 };
        let formValues = getFormValues(['eventTier', 'eventType']);
        if (
            'eventTier' in formValues &&
            'eventType' in formValues) {
                let eventTier = formValues['eventTier'];
                let eventType = formValues['eventType'];
                let eventSubTotal = eventRateToTierMap[eventTier] ?? -1;
                if (eventRate == -1 ){
                    throw new Error("Invalid event tier");
                }
                let eventFee = eventSubTotal*(eventRate/100);
                let eventTotal = eventSubTotal + eventFee;
                getElementByIdAndSetInnerHTML('paymentType', eventType);
                getElementByIdAndSetInnerHTML('paymentTier', eventTier);
                getElementByIdAndSetInnerHTML('paymentSubtotal', numberToLocaleString(eventSubTotal));
                getElementByIdAndSetInnerHTML('paymentRate', `${eventRate}%`);
                getElementByIdAndSetInnerHTML('paymentFee', numberToLocaleString(eventFee));
                getElementByIdAndSetInnerHTML('paymentTotal', numberToLocaleString(eventTotal));
        }
        else {
            throw new Error("Invalid form values for payment screen");
        }
        console.log({ formValues });
        console.log({ formValues });
        console.log({ formValues });
        console.log({ formValues });
        console.log({ formValues });
    }


    // validate form values

    // allTimelines.forEach((value, index) => {
    //     if (index == 3) {
    //         isFormValid =validateFormValuesEmpty([
    //             'launch_type'
    //         ])
    //     }
    // })

    if (!isFormValid) {
        alert("Please fill all the fields");
        return;
    }

    allTimelines.forEach((timeline, _index) => {

        const paragraph = document.querySelector(`#${timeline} .timestamp span`);
        const cicle = document.querySelector(`#${timeline} small`);
        if (timeline == nextTimeline) {
            if (!paragraph.classList.contains("font-color-active-timeline")) paragraph.classList.add("font-color-active-timeline");
            if (!cicle.classList.contains("background-active-timeline")) cicle.classList.add("background-active-timeline");

        }
        else {
            if (paragraph.classList.contains("font-color-active-timeline")) paragraph.classList.remove("font-color-active-timeline");
            if (cicle.classList.contains("background-active-timeline")) cicle.classList.remove("background-active-timeline");
        }
    })

    allIDs.forEach(id => {
        const element = document.querySelector(`#${id}`);
        console.log({ id, element })
        if (id === nextId) {
            element.classList.remove("d-none");
        }
        else if (!element.classList.contains("d-none")) {
            element.classList.add("d-none");
        }
    })

}