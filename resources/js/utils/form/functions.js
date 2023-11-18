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

function previewSelectedImage(imageId, previewImageId) {
    const imageInput = document.getElementById(imageId);
    if (!imageInput){
        throw new Error("Image input not found!")
    }
    const previewImage = document.getElementById(previewImageId);
    if (!previewImage){
        throw new Error("Preview not found!")
    }
    if (previewImage.classList.contains("d-none")){
        previewImage.classList.remove("d-none");
    }
    const file = imageInput.files[0];
    if (file) {
       const reader = new FileReader();
       reader.readAsDataURL(file);
       reader.onload = function(e) {
          previewImage.src = e.target.result;
       }
    }
 }


export { addFormValues, getFormValues, setFormValues, validateFormValuesPresent, previewSelectedImage };