const Toast = Swal.mixin({
    toast: true,
    position: 'top-right',
    width: 'fit-content',
    padding: '0.7rem',
    showConfirmButton: false,
    timer: 6000,
    timerProgressBar: true
})

function addFormValues(keyList) {
    let allFormkeyList = {};
    var createEventForm = document.forms['create-event-form'];
    if (!createEventForm) {
        Toast.fire({
            icon: 'error',
            title: 'Form cannot be found!'
        })
        return []
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

function validateFormValuesPresent(keyList) {
    let isFormValid = true, invalidKey = '';

    var createEventForm = document.forms['create-event-form'];

    if (!createEventForm) {
        Toast.fire({
            icon: 'error',
            title: 'Form not found'
        })
        return [false, "form"];
    }

    for (var key of keyList) {
        var formField = createEventForm.elements[key];
        if (formField) {
            var value = formField.value.trim();
            if (value === '') {
                isFormValid = false;
                invalidKey = key;
            }
        }
        else {
            Toast.fire({
                icon: 'error',
                title: 'Form not found'
            })
            isFormValid = false;
            invalidKey = key;
        }
    }
    return isFormValid ? [isFormValid, null] : [isFormValid, invalidKey];
}


function setFormValues(values) {
    var createEventForm = document.forms['create-event-form'];

    if (createEventForm) {
        for (var key in values) {
            console.log({ [key]: values[key] });
            var formField = createEventForm.elements[key];
            if (formField && values[key]) {
                formField.value = values[key];
            } else {
                console.log(`Form field  with key ${key}  not found!`);
            }
        }
    }

}

function getFormValues(keyList = ["ALL_FORM_KEYS"]) {
    let allFormkeyList = {};
    var createEventForm = document.forms['create-event-form'];

    if (!createEventForm) {
        Toast.fire({
            icon: 'error',
            title: 'Form not found'
        })
    }

    if (keyList[0] === "ALL_FORM_KEYS") {
        keyList = Object.keys(createEventForm.elements);
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

function previewSelectedImage(imageId, previewImageId) {
    const imageInput = document.getElementById(imageId);
    if (!imageInput) {
        throw new Error("Image input not found!")
    }
    const previewImage = document.getElementById(previewImageId);
    if (!previewImage) {
        throw new Error("Preview not found!")
    }
    if (previewImage.classList.contains("d-none")) {
        previewImage.classList.remove("d-none");
    }
    previewImage.style.objectFit = "cover";
    const file = imageInput.files[0];
    if (file) {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = function (e) {
            previewImage.src = e.target.result;
        }
    }
}

function previewSelectedImageByForm(imageId, previewImageId) {
    const imageInput = document.getElementById(imageId);
    if (!imageInput) {
        throw new Error("Image input not found!")
    }
    const previewImage = document.getElementById(previewImageId);
    if (!previewImage) {
        throw new Error("Preview not found!")
    }
    if (previewImage.classList.contains("d-none")) {
        previewImage.classList.remove("d-none");
    }
    previewImage.style.objectFit = "cover";
    const file = imageInput.files[0];
    if (file) {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = function (e) {
            previewImage.src = e.target.result;
        }
    }
}


let inputKeyToInputNameMapping = {
    eventName: 'name of the event',
    eventTier: 'tier of the event',
    eventType: 'type of the event',
    gameTitle: 'title of the game',
    eventBanner: 'event image',
    startDate: 'the start date of the event',
    startTime: 'the start time of the event',
    endDate: 'the end date of the event',
    endTime: 'the end time of the event',
    eventDescription: 'the event description',
    launch_date: "launch date",
    launch_time: "launch time",
    launch_schedule: "launch schedule",
    launch_visible: "launch type (public/ private/ draft)",
    isPaymentDone: "payment"
}

let inputKeyToStepNameMapping = {
    eventTier: ['step-3', 'timeline-1'],
    eventType: ['step-2', 'timeline-1'],
    gameTitle: ['step-1', 'timeline-1'],
    eventName: ['step-6', 'timeline-2'],
    eventBanner: ['step-9', 'timeline-2'],
    startDate: ['step-5', 'timeline-2'],
    startTime: ['step-5', 'timeline-2'],
    endDate: ['step-5', 'timeline-2'],
    endTime: ['step-5', 'timeline-2'],
    eventDescription: ['step-7', 'timeline-2'],
    launch_visible: ['step-11', 'timeline-4'],
    launch_date: ['step-11', 'timeline-4'],
    launch_time: ['step-11', 'timeline-4'],
    launch_schedule: ['step-11', 'timeline-4'],
    isPaymentDone: ['step-10', 'timeline-3']
}

function closeDropDown(element, id, keyValues, key) {
    element.parentElement.previousElementSibling.classList.toggle("dropbtn-open");
    element.parentElement.classList.toggle("d-none");
    document.getElementById(id).innerHTML = `
    Selected (${keyValues[key]})
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
function numberToLocaleString(number) {
    return Number(number).toLocaleString()
}

function saveForLivePreview() {
    var createEventForm = document.forms['create-event-form'];
    setFormValues({ 'livePreview': 'true' });
    createEventForm.submit();
}

function saveEvent() {
    let isFormValid = true, invalidKey = '', formValidation = null;
    var createEventForm = document.forms['create-event-form'];
    if (!createEventForm) {
        Toast.fire({
            icon: 'error',
            title: 'Form cannot be found!'
        })
        return;
    }
    formValidation = validateFormValuesPresent([
        'gameTitle', 'eventType', 'eventTier',
        'startDate', 'startTime', 'endDate', 'endTime', 'eventName', 'eventDescription', 
        'isPaymentDone',
        'launch_visible',
    ]);
    if (formValidation != null) {
        isFormValid = formValidation[0];
        invalidKey = formValidation[1];
    }
    if (localStorage.getItem('eventBanner') != null) {
        // createEventForm.elements['eventBanner'].value = localStorage.getItem('eventBanner');
        console.log({ banner: localStorage.getItem('eventBanner') })
    }
    else{
        isFormValid = false;
        invalidKey = 'eventBanner';
    }
    if (!isFormValid) {
        console.log({ formValidation })
        console.log({ formValidation })
        console.log({ formValidation })
        console.log({ formValidation })
        Toast.fire({
            icon: 'error',
            text: `Didn't enter ${inputKeyToInputNameMapping[invalidKey] ?? ""}! It is a required field.`
        })
        let [nextId, nextTimeline] = inputKeyToStepNameMapping[invalidKey];
        goToNextScreen(nextId, nextTimeline);
        return;
    }
    if (createEventForm.elements['launch_visible'].value == 'DRAFT') {
        createEventForm.submit();
        return;
    }
    else {
        formValidation = validateFormValuesPresent([
            'launch_schedule',
        ]);
        if (formValidation != null) {
            isFormValid = formValidation[0];
            invalidKey = formValidation[1];
        }
        else {
            if (createEventForm.elements['launch_schedule'] != 'now') {
                formValidation = validateFormValuesPresent([
                    'launch_date', 'launch_time'
                ]);
                if (formValidation != null) {
                    isFormValid = formValidation[0];
                    invalidKey = formValidation[1];
                }
            }
        }
        if (!isFormValid) {
            console.log({ formValidation })
            console.log({ formValidation })
            console.log({ formValidation })
            console.log({ formValidation })
            Toast.fire({
                icon: 'error',
                text: `Didn't enter ${inputKeyToInputNameMapping[invalidKey] ?? ""}! It is a required field.`
            })
            let [nextId, nextTimeline] = inputKeyToStepNameMapping[invalidKey];
            goToNextScreen(nextId, nextTimeline);
            return;
        }
        else {
            createEventForm.submit();
        }
    }
}

function goToNextScreen(nextId, nextTimeline) {
    document.getElementsByClassName("navbar")[0].scrollIntoView({ behavior: 'smooth' });

    const allIDs = [
        'step-0',
        'step-1', 'step-2', 'step-3', 'step-4', 'step-5', 'step-6', 'step-7', 'step-8', 'step-9', 'step-10', 'step-11', 'step-12'];

    const allTimelines = ['timeline-1', 'timeline-2', 'timeline-3', 'timeline-4'];
    let isFormValid = true, invalidKey = '', formValidation = null;
    let currentId = 'step-0';
    allIDs.forEach(id => {
        const element = document.querySelector(`#${id}`);
        if (element && !element.classList.contains("d-none")) {
            currentId = id;
        }
    })
    console.log({ nextId, currentId, id: allIDs[10] })

    if (currentId == allIDs[1]) {
        formValidation = validateFormValuesPresent(['gameTitle']);
    }
    else if (currentId == allIDs[2]) {
        formValidation = validateFormValuesPresent(['eventType']);
    }
    else if (currentId == allIDs[3]) {
        formValidation = validateFormValuesPresent(['eventTier']);
    }

    else if (currentId == allIDs[5]) {
        formValidation = validateFormValuesPresent(['startDate', "startTime", "endDate", "endTime"]);
    }
    else if (currentId == allIDs[6]) {
        formValidation = validateFormValuesPresent(['eventName']);
    }
    else if (currentId == allIDs[7]) {
        formValidation = validateFormValuesPresent(['eventDescription']);
    }
    else if (currentId == allIDs[9]) {
        formValidation = validateFormValuesPresent(['eventBanner']);
    }
    if (nextId == allIDs[10]) {
        const paymentMethodConditionFulfilledButton = document.getElementsByClassName('choose-payment-method-condition-fulfilled')[0];
        const paymentMethodCondition = document.getElementsByClassName('choose-payment-method')[0];
        console.log({ found: true })
        let eventRate = 20, eventSubTotal = 0, eventFee = 0, eventTotal = 0;
        let eventRateToTierMap = { 'Starfish': 5000, 'Turtle': 10000, 'Dolphin': 15000 };
        let formValues = getFormValues(['eventTier', 'eventType']);
        if (
            'eventTier' in formValues &&
            'eventType' in formValues
        ) {

            let eventTier = formValues['eventTier'] ?? null;
            let eventType = formValues['eventType'] ?? null;
            let eventSubTotal = eventRateToTierMap[eventTier] ?? -1;
            if (eventRate == -1) {
                Toast.fire({
                    icon: 'error',
                    text: `Invalid event tier or event type!`
                })
            }
            let eventFee = eventSubTotal * (eventRate / 100);
            let eventTotal = eventSubTotal + eventFee;
            if (eventTier == null || eventType == null || eventSubTotal == -1) {
                getElementByIdAndSetInnerHTML('paymentType', "N/A");
                getElementByIdAndSetInnerHTML('paymentTier', "N/A");
                getElementByIdAndSetInnerHTML('paymentTotal', "N/A");

                if (!paymentMethodCondition.classList.contains("d-none")) {
                    paymentMethodCondition.classList.add("d-none");
                }
                if (paymentMethodConditionFulfilledButton.classList.contains("d-none")) {
                    paymentMethodConditionFulfilledButton.classList.remove("d-none");
                }
            }
            else {
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
        else {
            throw new Error("Invalid form values for payment screen");
        }
    }
    else if (nextId == allIDs[11]) {
        formValidation = validateFormValuesPresent(['isPaymentDone']);
    }

    if (formValidation != null) {
        isFormValid = formValidation[0];
        invalidKey = formValidation[1];
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
        // console.log({ id, element })
        if (id === nextId) {
            element.classList.remove("d-none");
        }
        else if (element && !element.classList.contains("d-none")) {
            element.classList.add("d-none");
        }
    })
}