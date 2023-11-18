const Toast = Swal.mixin({
    toast: true,
    position: 'top-right',
    width: 'fit-content',
    showConfirmButton: false,
    timer: 4000,
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

function validateFormValuesPresent(values) {
    let isFormValid = true, invalidKey = '';

    var createEventForm = document.forms['create-event-form'];

    if (!createEventForm) {
        Toast.fire({
            icon: 'error',
            title: 'Form not found'
        })
        return [false, "form"];
    }

    for (var key of values) {
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
            if (formField) {
                formField.value = values[key];
            } else {
                Toast.fire({
                    icon: 'error',
                    title: 'Form not found'
                })
            }
        }
    }

}

function getFormValues(keyList) {
    let allFormkeyList = {};
    var createEventForm = document.forms['create-event-form'];

    if (!createEventForm) {
        Toast.fire({
            icon: 'error',
            title: 'Form not found'
        })
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

// function goToNextScreen(nextId, nextTimeline) {
//     const allIDs = [
//         'step-0',
//         'step-1', 'step-2', 'step-3', 'step-4', 'step-5', 'step-6', 'step-7', 'step-8', 'step-9', 'step-10', 'step-11', 'step-12'
//     ];
//     const allTimelines = [
//         'timeline-1', 'timeline-2', 'timeline-3', 'timeline-4'
//     ];
//     allIDs.forEach(id => {
//         const element = document.querySelector(`#${id}`);
//         console.log({ id, element })
//         if (id === nextId) element.classList.remove("d-none");
//         else if (!element.classList.contains("d-none")) {
//             element.classList.add("d-none");
//         }
//     })
//     allTimelines.forEach((timeline, index) => {

//         const paragraph = document.querySelector(`#${timeline} .timestamp span`);
//         const cicle = document.querySelector(`#${timeline} small`);
//         // const border = document.querySelector(`#${timeline} div:nth-child(1)`);
//         if (timeline === nextTimeline) {
//             if (!paragraph.classList.contains("font-color-active-timeline")) paragraph.classList.add("font-color-active-timeline");
//             if (!cicle.classList.contains("background-active-timeline")) cicle.classList.add("background-active-timeline");
//             if (index === 0) {
//                 border.style.borderTop = "2px solid red";
//             }
//         } else {
//             if (paragraph.classList.contains("font-color-active-timeline")) paragraph.classList.remove("font-color-active-timeline");
//             if (cicle.classList.contains("background-active-timeline")) cicle.classList.remove("background-active-timeline");
//         }

//     })


// }

let inputKeyToInputNameMapping = {
    eventTier: 'event tier',
    eventType: 'event type',
    gameTitle: 'game title',
    'event name': 'name of the event',
    eventTier: 'tier of the event',
    eventType: 'type of the event',
    gameTitle: 'title of the game',
    eventBanner: 'event image',
    startDate: 'the start date of the event',
    startTime: 'the start time of the event',
    endDate: 'the end date of the event',
    endTime: 'the end time of the event',
    eventDescription: 'the event description',
    eventTags: 'the event tags'
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
        if (!element.classList.contains("d-none")) {
            currentId = id;
        }
    })
    if (currentId == allIDs[1]) {
        formValidation = validateFormValuesPresent(['gameTitle']);
        // formValidation = formHelper.validateFormValuesPresent(['eventTier', 'eventType', 'gameTitle']);
    }
    else if (currentId == allIDs[2]) {
        formValidation = validateFormValuesPresent(['eventType']);
        // formValidation = formHelper.validateFormValuesPresent(
        //     ['eventBanner', 'startDate', 'startTime', 'endDate', 'endTime', 'eventDescription', 'eventTags']
        // );
    }
    else if (currentId == allIDs[3]) {
        formValidation = validateFormValuesPresent(['eventTier']);
    }
    else if (currentId == allIDs[5]) {
        formValidation = validateFormValuesPresent(['eventType']);
        // formValidation = formHelper.validateFormValuesPresent(
        //     ['eventBanner', 'startDate', 'startTime', 'endDate', 'endTime', 'eventDescription', 'eventTags']
        // );
    }
    if (formValidation != null) {
        isFormValid = formValidation[0];
        invalidKey = formValidation[1];
    }
    if (!isFormValid) {
        Toast.fire({
            icon: 'error',
            text: `Didn't enter ${inputKeyToInputNameMapping[invalidKey] ?? ""}! It is a required field.`
        })
    }
    if (currentId == allIDs[4]) {
        let formValues = getFormValues(['eventTier', 'eventType', 'gameTitle']);
        if (
            // do later this way
            'eventTier' in formValues &&
            'gameTitle' in formValues &&
            'eventType' in formValues
            ) {
            // let eventTier = formValues['eventTier'];
            // let eventType = formValues['eventType'];
            // let gameTitle = formValues['gameTitle'];
            // let inputGameTilte = document.getElementById('img#inputGameTilte');
            // let resultGameTitle = document.querySelector('img#resultGameTitle');
            // let inputEventType = document.getElementById('img#inputEventType');
            // let resultEventType = document.querySelector('img#resultEventType');
            // let inputEventTier = document.getElementById('img#inputEventTier');
            // let resultEventTier = document.querySelector('img#resultEventTier');
            // inputEventTier.innerHTML = resultEventTier.innerHTML;
            // inputEventType.innerHTML = resultEventType.innerHTML;
            // inputGameTilte.src = resultGameTitle.src;
            // console.log({inputGameTilte, resultGameTitle})
            // console.log({inputGameTilte, resultGameTitle})
            // console.log({inputGameTilte, resultGameTitle})
            // console.log({inputGameTilte, resultGameTitle})
            // console.log({inputGameTilte, resultGameTitle})
            // resultGameTitle.src = inputGameTilte.src;
            // getElementByIdAndSetInnerHTML('paymentType', eventType);
            // getElementByIdAndSetInnerHTML('paymentTier', eventTier);
        }
        else {
            Toast.fire({
                icon: 'error',
                text: `Go back and fill all values....`
            })
        }
    }
    if (currentId == allIDs[8]) {
        let eventRate = 20, eventSubTotal = 0, eventFee = 0, eventTotal = 0;
        let eventRateToTierMap = { 'Starfish': 5000, 'Turtle': 10000, 'Dolphin': 15000 };
        let formValues = getFormValues(['eventTier', 'eventType']);
        // if (
            // Get from github
        //     'eventTier' in formValues &&
        //     'eventType' in formValues) {
        //     let eventTier = formValues['eventTier'];
        //     let eventType = formValues['eventType'];
        //     let eventSubTotal = eventRateToTierMap[eventTier] ?? -1;
        //     if (eventRate == -1) {
        //         throw new Error("Invalid event tier");
        //     }
        //     let eventFee = eventSubTotal * (eventRate / 100);
        //     let eventTotal = eventSubTotal + eventFee;
        //     getElementByIdAndSetInnerHTML('paymentType', eventType);
        //     getElementByIdAndSetInnerHTML('paymentTier', eventTier);
        //     getElementByIdAndSetInnerHTML('paymentSubtotal', numberToLocaleString(eventSubTotal));
        //     getElementByIdAndSetInnerHTML('paymentRate', `${eventRate}%`);
        //     getElementByIdAndSetInnerHTML('paymentFee', numberToLocaleString(eventFee));
        //     getElementByIdAndSetInnerHTML('paymentTotal', numberToLocaleString(eventTotal));
        // }
        // else {
        //     throw new Error("Invalid form values for payment screen");
        // }
    }
    if (currentId == allIDs[4]) {
    }
    if (!isFormValid) {
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
        // console.log({ id, element })
        if (id === nextId) {
            element.classList.remove("d-none");
        }
        else if (!element.classList.contains("d-none")) {
            element.classList.add("d-none");
        }
    })
}