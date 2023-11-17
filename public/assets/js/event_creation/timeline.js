// function toggleNavbar() {
//     const x = document.querySelector("nav.mobile-navbar");
//     console.log({ x })
//     x.classList.toggle("d-none");
// }

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
    formHelper.setFormValues(keyValues)
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
        let formValidation = formHelper.validateFormValuesPresent(['eventTier', 'eventType', 'gameTitle']);
        formValidation = formHelper.validateFormValuesPresent(['eventTier', 'eventType', 'gameTitle']);
    }
    else if (currentId == allIDs[2]) {
        formValidation = formHelper.validateFormValuesPresent(
            ['eventBanner', 'startDate', 'startTime', 'endDate', 'endTime', 'eventDescription', 'eventTags']
        );
    }
    else if (currentId == allIDs[3]) {
        formValidation = formHelper.validateFormValuesPresent(
            ['eventTier', 'eventType', 'gameTitle']
        );
    }
    if (formValidation != null) {
        isFormValid = formValidation[0];
        invalidKey = formValidation[1];
    }
    if (!isFormValid) {
        Toast.fire({
            icon: 'error',
            text: `Didn't enter ${inputKeyToInputNameMapping[invalidKey]?? ""}! It is a required field.`
            text: `Didn't enter ${inputKeyToInputNameMapping[invalidKey] ?? ""}! It is a required field.`
        })
    }
    if (nextTimeline == allTimelines[2]) {
        let eventRate = 20, eventSubTotal = 0, eventFee = 0, eventTotal = 0;
        let eventRateToTierMap = { 'Starfish': 5000, 'Turtle': 10000, 'Dolphin': 15000 };
        let formValues = formHelper.getFormValues(['eventTier', 'eventType']);
        if (
            'eventTier' in formValues &&
            'eventType' in formValues) {
            let eventTier = formValues['eventTier'];
            let eventType = formValues['eventType'];
            let eventSubTotal = eventRateToTierMap[eventTier] ?? -1;
            if (eventRate == -1) {
                throw new Error("Invalid event tier");
            }
            let eventFee = eventSubTotal * (eventRate / 100);
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
        console.log({ id, element })
        if (id === nextId) {
            element.classList.remove("d-none");
        }
        else if (!element.classList.contains("d-none")) {
            element.classList.add("d-none");
        }
    })
}