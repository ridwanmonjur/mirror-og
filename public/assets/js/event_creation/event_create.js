function openDropDown(element){
    // if (!element.classList.contains('dropbtn-open')){
    //     element.classList.add("dropbtn-open");
    // }
    // if (element.nextElementSibling.classList.contains('d-none')){
    //     element.nextElementSibling.classList.remove("d-none");
    // }
    element.classList.toggle("dropbtn-open");
    element.nextElementSibling.classList.toggle("d-none");
}

function toggleRadio(_input, message) {
    const elements = document.querySelectorAll(`.radio-indent-hidden`);
    elements.forEach(element => {
        if (element.classList.contains(message)) {
            element.classList.remove("d-none");
        }
        else element.classList.add("d-none");
    })
}

function addEvent(button){
    button.parentElement.parentElement.classList.add("d-none");
    const feedbackElement = document.querySelector("#feedback");
    const headingElement = document.querySelector("#heading");
    const notificationElement = document.querySelector("#notification");
    const descriptionElement = document.querySelector("#description");
    feedbackElement.classList.remove("d-none");
    headingElement.innerHTML = "Event Created";
    notificationElement.innerHTML = "Your event has been created successfully!";
    descriptionElement.innerHTML = "You can view your event in the timeline.";
}

function chooseEventType(button) {
    const eventTypes = document.querySelectorAll(".event-type");
    eventTypes.forEach(eventType => {
        if (eventType.classList.contains("d-none")) {
            eventType.classList.remove("d-none");
        }
        else eventType.classList.add("d-none");
    })
    button.parentElement.classList.add("d-none");
}