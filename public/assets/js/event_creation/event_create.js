function openDropDown(element) {
    element.classList.toggle("dropbtn-open");
    element.nextElementSibling.classList.toggle("d-none");
}

function toggleRadio(_input, message) {
    const elements = document.querySelectorAll(`.radio-indent-hidden`);
    elements.forEach(element => {
        if (element.classList.contains(message)) {
            element.classList.remove("d-none");
        } else element.classList.add("d-none");
    })
}

function addEvent(button) {
    button.parentElement.parentElement.classList.add("d-none");
    const feedbackElement = document.querySelector("#feedback");
    const headingElement = document.querySelector("#heading");
    const notificationElement = document.querySelector("#notification");
    const descriptionElement = document.querySelector("#description");
    feedbackElement.classList.remove("d-none");
    headingElement.innerHTML = "All done";
    notificationElement.innerHTML = "Your event has been launched to the world!";
    descriptionElement.innerHTML = "You will be notified as players join your event.";
}

function chooseEventType(button) {
    const eventTypes = document.querySelectorAll(".event-type");
    eventTypes.forEach(eventType => {
        if (eventType.classList.contains("d-none")) {
            eventType.classList.remove("d-none");
        } else eventType.classList.add("d-none");
    })
    button.parentElement.classList.add("d-none");
}