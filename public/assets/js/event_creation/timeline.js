function toggleNavbar() {
    const x = document.querySelector("nav.mobile-navbar");
    console.log({ x })
    x.classList.toggle("d-none");
let inputKeyToInputNameMapping = {
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
    const allIDs = [
        'step-0',
        'step-1', 'step-2', 'step-3', 'step-4', 'step-5', 'step-6', 'step-7', 'step-8', 'step-9', 'step-10', 'step-11', 'step-12'
    ];
    const allTimelines = [
        'timeline-1', 'timeline-2', 'timeline-3', 'timeline-4'
    ];
    allIDs.forEach(id => {
            const element = document.querySelector(`#${id}`);
            console.log({ id, element })
            if (id === nextId) element.classList.remove("d-none");
            else if (!element.classList.contains("d-none")) {
                element.classList.add("d-none");
            }
        })
        // if (timeline === 'none') {
        //     const border = document.querySelector(`#${timeline} div:nth-child(1)`);
        //     border.style.borderTop = "2px solid green";
        //     return;
        // }
    allTimelines.forEach((timeline, index) => {

    allTimelines.forEach((timeline, _index) => {
        const paragraph = document.querySelector(`#${timeline} .timestamp span`);
        const cicle = document.querySelector(`#${timeline} small`);
        // const border = document.querySelector(`#${timeline} div:nth-child(1)`);
        if (timeline === nextTimeline) {
            if (!paragraph.classList.contains("font-color-active-timeline")) paragraph.classList.add("font-color-active-timeline");
            if (!cicle.classList.contains("background-active-timeline")) cicle.classList.add("background-active-timeline");
            if (index === 0) {
                border.style.borderTop = "2px solid red";
            }
        } else {
            if (paragraph.classList.contains("font-color-active-timeline")) paragraph.classList.remove("font-color-active-timeline");
            if (cicle.classList.contains("background-active-timeline")) cicle.classList.remove("background-active-timeline");
        }

    })


}
