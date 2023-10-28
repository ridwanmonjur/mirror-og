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
        console.log({key})
        console.log({key})
        console.log({key})
        console.log({key})
        console.log({key})
        var formField = createEventForm.elements[key];
        if (formField) {
            console.log({ formField, formFieldValue: formField.value })
            var value = formField.value.trim();
            if (value === '') {
                isFormValid = false;
                invalidKey = key;
            }
        }
        else {
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


export { addFormValues, getFormValues, setFormValues, validateFormValuesPresent };