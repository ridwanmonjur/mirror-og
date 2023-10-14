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

function goToNextScreen(nextId, nextTimeline) {
    document.getElementsByClassName("navbar")[0].scrollIntoView({ behavior: 'smooth' });
    const allIDs = [
        'step-0',
        'step-1', 'step-2', 'step-3', 'step-4'];

    const allTimelines = ['timeline-1', 'timeline-2', 'timeline-3', 'timeline-4'];

    let isFormValid = true;
    allTimelines.forEach((index) => {

        if (index == 3) {
            isFormValid =validateFormValuesEmpty([
                'launch_type'
            ])
        }
    })

    if (!isFormValid) {
        alert("Please fill all the fields");
        return;
    }

    allTimelines.forEach((timeline, index) => {

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